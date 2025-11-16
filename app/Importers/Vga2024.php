<?php

namespace App\Importers;

use App\Models\Access;
use App\Models\Action;
use App\Models\Autocompleter;
use App\Models\Award;
use App\Models\AwardFeedback;
use App\Models\AwardSuggestion;
use App\Models\File;
use App\Models\GameRelease;
use App\Models\IpAddress;
use App\Models\Login;
use App\Models\LootboxItem;
use App\Models\LootboxTier;
use App\Models\News;
use App\Models\Nominee;
use App\Models\Permission;
use App\Models\Result;
use App\Models\TableHistory;
use App\Models\Template;
use App\Models\User;
use App\Models\UserInventoryItem;
use App\Models\UserNomination;
use App\Models\UserNominationGroup;
use App\Models\Vote;
use App\Models\VotingCodeLog;
use App\Settings\AppSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use SebastianBergmann\Timer\Timer;

class Vga2024 extends Importer
{
    protected $processed = [];

    protected function year(): string
    {
        return '2024';
    }

    public function database(): void
    {
        foreach ($this->getTableMapping() as $class => $mapping) {
            $this->processTable($class);
        }

        foreach (array_keys($this->getJoinTables()) as $table) {
            $this->processJoinTable($table);
        }

        $this->processSettings();
    }

    private function processSettings()
    {
        echo "config\n";

        $config = $this->db->table('config')->get()->first();

        if (!$config) {
            echo "  settings: 0\n";
            return;
        }

        $configKeyValue = $this->db->table('config_key_value')->get();

        foreach ($configKeyValue as $row) {
            $key = str_replace('-', '_', $row->name);
            $config->{$key} = $row->value;
        }

        $settings = app(AppSettings::class);

        $count = 0;

        foreach ($config as $key => $value) {
            // Set the config value
            if (!property_exists($settings, $key)) {
                continue;
            }

            // Use reflection to get the property type
            $reflection = new \ReflectionProperty($settings, $key);
            $type = $reflection->getType()?->getName();

            if ($value === null) {
                $value = null;
            } elseif ($type === 'bool') {
                $value = (bool)$value;
            } elseif (str_contains($type, 'Carbon')) {
                $value = Date::make($value)->shiftTimezone('America/New_York');
            } elseif ($type === 'array') {
                $value = json_decode($value, true);
            }

            $settings->$key = $value;
            $count++;
        }

        $settings->save();

        echo "  settings: $count\n";
    }

    private function processTable(string $class, bool $force = false)
    {
        if (in_array($class, $this->processed, true)) {
            return;
        }

        $mapping = $this->getTableMapping()[$class];

        $time = $mapping['time'] ?? 0;

        if ($time > 60 && !$force && empty($mapping['yes'])) {
            return;
        }

//        if ((empty($mapping['yes']) || !empty($mapping['skip'])) && !$force) {
//            return;
//        }

        $required = $mapping['required'] ?? [];
        foreach ($required as $entity) {
            if (!in_array($entity, $this->processed, true)) {
                $this->processTable($entity, force: true);
            }
        }

        echo $class . "\n";

        /** @var Model $model */
        $model = new $class;

        $table = $mapping['old_table'] ?? $model->getTable();

        $columns = collect($this->db->getSchemaBuilder()->getColumnListing($table));
        if ($columns->isEmpty()) {
            throw new RuntimeException("Table {$table} does not exist in the old database.");
        }

        // Create a mapping of old column names to new column names
        $columns = $columns->mapWithKeys(function ($value, $key) use ($mapping) {
            if (in_array($value, $mapping['ignore'] ?? [], true)) {
                return [];
            }

            if (array_key_exists($value, $mapping['columns'] ?? []) && $mapping['columns'][$value] === null) {
                return [];
            }

            $column = $mapping['columns'][$value] ?? $this->normaliseColumnName($value);
            return [$value => $column];
        });

        $timer = new Timer();
        $timer->start();

        Schema::disableForeignKeyConstraints();
        $model::truncate();

        if (empty($mapping['disable_foreign_keys'])) {
            Schema::enableForeignKeyConstraints();
        }

        $rows = $this->db->table($table)->get();
        $count = 0;
        foreach ($rows as $row) {
            $data = [];
            foreach ($columns as $oldColumn => $newColumn) {
                // 1-to-1 copy of data
                $data[$newColumn] = $row->$oldColumn;

                // If the new column is cast to array, assume that the old data is JSON and decode it
                if (($model->getCasts()[$newColumn] ?? null) === 'array' && is_string($data[$newColumn])) {
                    $data[$newColumn] = json_decode($data[$newColumn], true, 512, JSON_THROW_ON_ERROR);
                }

                // If the new column name matches an ID mapper, apply the mapper function to convert
                // the old entity ID for the relationship to the new entity ID
                if (isset($this->getIdMappers()[$newColumn]) && $data[$newColumn] !== null) {
                    $mapper = $this->getIdMappers()[$newColumn];
                    $data[$newColumn] = $mapper($data[$newColumn]);
                }
            }

            // Add in columns that require custom mapping
            if (isset($mapping['custom'])) {
                $customData = $mapping['custom']($data, $row);
                $data = [...$data, ...$customData];
            }

            $model::createQuietly($data);
            $count++;
        }

        echo "  rows: $count\n";

        Schema::enableForeignKeyConstraints();

        echo "  time: " . $timer->stop()->asSeconds() . " seconds\n";

        $this->processed[] = $class;
    }

    private function getIdMappers(): array
    {
        $awardMapper = function ($oldId) {
            $award = Award::where('slug', $oldId)->withTrashed()->first();
            if (!$award) {
                throw new RuntimeException("Could not find Award with slug {$oldId}");
            }
            return $award->id;
        };

        $autocompleterMapper = function ($oldId) {
            $autocompleter = Autocompleter::where('slug', $oldId)->first();
            if (!$autocompleter) {
                throw new RuntimeException("Could not find Autocompleter with slug {$oldId}");
            }
            return $autocompleter->id;
        };

        return [
            'autocompleter_id' => $autocompleterMapper,
            'award_id' => $awardMapper,
        ];
    }

    private function getJoinTables(): array
    {
        return [
            'lootbox_item_files' => [
                'model1' => LootboxItem::class,
                'model2' => File::class,
                'relationship' => 'additionalFiles',
            ],
            'user_permissions' => [
                'model1' => User::class,
                'model2' => Permission::class,
                'relationship' => 'permissions',
            ],
            'permission_children' => [
                'model1' => Permission::class,
                'model2' => Permission::class,
                'relationship' => 'children',
            ],
        ];
    }

    private function processJoinTable(string $table): void
    {
        echo "Processing join table: {$table}\n";

        $mapping = $this->getJoinTables()[$table];
        /** @var class-string<Model> $modelClass1 */
        $modelClass1 = $mapping['model1'];
        $relationship = $mapping['relationship'];

        Schema::disableForeignKeyConstraints();
        DB::table($table)->truncate();
        Schema::enableForeignKeyConstraints();

        $columns = collect($this->db->getSchemaBuilder()->getColumnListing($table));
        if ($columns->isEmpty()) {
            throw new RuntimeException("Table {$table} does not exist in the old database.");
        }

        $rows = $this->db->table($table)->get();
        $count = 0;

        foreach ($rows as $row) {
            $entity1 = $modelClass1::find($row->{$columns[0]});
            $entity1->{$relationship}()->attach($row->{$columns[1]});
            $count++;
        }

        echo "  rows: $count\n";
    }

    private function getTableMapping()
    {
        $platformList = ['pc', 'ps3', 'ps4', 'ps5', 'vita', 'psn', 'x360', 'xb1', 'xbla', 'xsx', 'wii', 'wiiu', 'wiiware', 'switch', 'n3ds', 'vr', 'mobile'];

        return [
            IpAddress::class => [
                'time' => 5,
                'old_table' => 'ip_address',
                'columns' => [
                    'last_updated' => 'created_at',
                ],
                'custom' => fn ($newRecord, $oldRecord) => [
                    'updated_at' => $oldRecord->last_updated,
                ],
            ],
            GameRelease::class => [
                'ignore' => $platformList,
                'custom' => function ($newRecord, $oldRecord) use ($platformList) {
                    $platforms = [];
                    foreach ($platformList as $platform) {
                        if (!empty($oldRecord->$platform)) {
                            $platforms[] = $platform;
                        }
                    }

                    return [
                        'list' => 'video-games',
                        'platforms' => $platforms,
                    ];
                },
            ],
            Template::class => [
                'columns' => [
                    'last_updated' => 'created_at',
                ],
                'custom' => fn ($newRecord, $oldRecord) => [
                    'updated_at' => $oldRecord->last_updated,
                ],
            ],
            Autocompleter::class => [
                'columns' => [
                    'id' => 'slug',
                ],
            ],
            LootboxTier::class => [
            ],
            File::class => [
            ],
            Award::class => [
                'required' => [
                    Autocompleter::class,
                    File::class,
                ],
                'columns' => [
                    'id' => 'slug',
                    'autocompleteID' => 'autocompleter_id',
                ],
            ],
            LootboxItem::class => [
                'required' => [
                    LootboxTier::class,
                    File::class,
                ],
                'columns' => [
                    'tier_id' => 'lootbox_tier_id',
                    'short_name' => 'slug',
                    'year' => 'series',
                ],
            ],
            UserInventoryItem::class => [
                'time' => 120,
                'required' => [
                    LootboxItem::class,
                ],
                'columns' => [
                    'user' => 'fuzzy_user_id',
                    'timestamp' => 'created_at',
                    'itemID' => 'lootbox_item_id',
                ]
            ],
            Nominee::class => [
                'required' => [
                    Award::class,
                    File::class,
                ],
                'columns' => [
                    'short_name' => 'slug',
                ]
            ],
            AwardSuggestion::class => [
                'required' => [
                    Award::class,
                ],
                'columns' => [
                    'user' => 'fuzzy_user_id',
                ],
            ],
            AwardFeedback::class => [
                'time' => 10,
                'required' => [
                    Award::class,
                ],
                'columns' => [
                    'user' => 'fuzzy_user_id',
                ],
            ],
            Result::class => [
                'time' => 36,
                'old_table' => 'result_cache',
                'required' => [
                    Award::class,
                ],
            ],
            User::class => [
                'columns' => [
                    'special' => 'team_member',
                    'avatar' => 'avatar_url',
                    'notes' => null,
                ],
            ],
            Vote::class => [
                'time' => 152,
                'required' => [
                    Award::class,
                    User::class,
                ],
                'columns' => [
                    'timestamp' => 'created_at',
                    'number' => 'voting_group',
                ],
                'custom' => fn ($newRecord, $oldRecord) => [
                    'updated_at' => $oldRecord->timestamp,
                ],
            ],
            News::class => [
                'required' => [
                    User::class,
                ],
                'columns' => [
                    'visible' => null,
                    'timestamp' => 'show_at',
                    'deletedBy' => null,
                ],
                'custom' => fn ($newRecord, $oldRecord) => [
                    'created_at' => $oldRecord->timestamp,
                    'updated_at' => $oldRecord->timestamp,
                    'deleted_at' => $oldRecord->deletedBy ? now() : null,
                ],
            ],
            Permission::class => [
            ],
            TableHistory::class => [
                'time' => 4,
                'required' => [
                    User::class,
                ],
                'columns' => [
                    'timestamp' => 'created_at',
                ],
            ],
            Login::class => [
                'required' => [
                    User::class,
                ],
                'columns' => [
                    'timestamp' => 'created_at',
                ],
            ],
            VotingCodeLog::class => [
                'time' => 3,
                'columns' => [
                    'timestamp' => 'created_at',
                ],
                'required' => [
                    User::class,
                ],
            ],
            Access::class => [
                'time' => 1667,
                'required' => [
                    User::class,
                ],
                'columns' => [
                    'timestamp' => 'created_at',
                ],
            ],
            Action::class => [
                'time' => 109,
                'required' => [
                    TableHistory::class,
                    User::class,
                ],
                'columns' => [
                    'timestamp' => 'created_at',
                    'history_id' => 'table_history_id',
                ],
            ],
            UserNominationGroup::class => [
                'time' => 9,
                'required' => [
                    Nominee::class,
                    Award::class,
                ],
                'disable_foreign_keys' => true,
            ],
            UserNomination::class => [
                'time' => 41,
                'required' => [
                    UserNominationGroup::class,
                    Award::class,
                ],
                'columns' => [
                    'user' => 'fuzzy_user_id',
                    'timestamp' => 'created_at',
                    'nomination_group_id' => 'user_nomination_group_id',
                ],
                'custom' => fn ($newRecord, $oldRecord) => [
                    'updated_at' => $oldRecord->timestamp,
                ],
            ],
        ];
    }
}
