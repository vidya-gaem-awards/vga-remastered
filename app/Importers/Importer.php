<?php

namespace App\Importers;

use App\Models\Show;
use BadMethodCallException;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

abstract class Importer
{
    protected string $connection;
    protected Connection $db;
//    protected Show $show;

    protected static ?string $resultFilter = null;

    protected static Collection $awards;

    public function __construct()
    {
        $database = 'vga_' . $this->year();
        $this->connection = "legacy-$database";

        if (!Config::get("database.connections.$this->connection")) {
            $config = Config::get('database.connections.legacy');
            $config['database'] = $database;
            Config::set("database.connections.$this->connection", $config);
        }

        $this->db = DB::connection($this->connection);
//        $this->show = $this->show();
    }

    abstract protected function year(): string;

//    public function show(): Show
//    {
//        $show = Show::updateOrCreate(
//            ['id' => $this->year()],
//            [
//                'year' => $this->year(),
//                'name' => $this->year() . " Vidya Gaem Awards",
//                'read_only' => true
//            ]
//        );
//        return $show;
//    }

    public function database(): void
    {
    }

    public function awards()
    {
    }

    public function nominees(): void
    {
    }

    public function results(): void
    {
    }

    public function users(): void
    {
    }

    public function permissions(): void
    {
    }

    public function files(): void
    {
    }


    /**
     * Converts a column name from its original form into snake_case.
     *
     * The main thing this function adds over just using Str::snake is that
     * columns like "AwardID" will be converted to "award_id" instead of "award_i_d".
     *
     * @param string $name Column name
     * @return string Normalised column name
     */
    protected function normaliseColumnName(string $name): string
    {
        return Str::snake(preg_replace_callback('/([A-Z])([A-Z])[^A-Z]?/', function ($matches) {
            return $matches[1] . mb_strtolower($matches[2]);
        }, $name));
    }

    /**
     * Processes the result of a select query by normalising all column names
     * and converting the result into a collection of collections.
     *
     * @param stdClass[]|Collection $rows
     * @return Collection<Collection>
     */
    protected function processResult(array|Collection $rows): Collection
    {
        if (is_array($rows)) {
            $rows = collect($rows);
        }

        return $rows->map(function ($row) {
            return collect((array)$row)->mapWithKeys(function ($value, $key) {
                return [$this->normaliseColumnName($key) => $value];
            });
        });
    }

    protected function query(string $query): Collection
    {
        return $this->processResult($this->db->select($query));
    }
}
