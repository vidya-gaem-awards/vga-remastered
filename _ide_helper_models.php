<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @mixin IdeHelperAccess
 * @property int $id
 * @property \Carbon\CarbonImmutable $created_at
 * @property string $cookie_id
 * @property string $route
 * @property string $controller
 * @property string $request_string
 * @property string $request_method
 * @property string $ip
 * @property string $user_agent
 * @property string $filename
 * @property string|null $referer
 * @property array<array-key, mixed>|null $headers
 * @property int|null $user_id
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereController($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereCookieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereRequestMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereRequestString($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Access whereUserId($value)
 */
	class Access extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperAction
 * @property int $id
 * @property string $ip
 * @property string $page
 * @property string $action
 * @property string|null $data1
 * @property string|null $data2
 * @property int|null $table_history_id
 * @property int|null $user_id
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \App\Models\TableHistory|null $tableHistory
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action whereData1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action whereData2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action wherePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action whereTableHistoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Action whereUserId($value)
 */
	class Action extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperAutocompleter
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property array<array-key, mixed> $strings
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Award> $awards
 * @property-read int|null $awards_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter whereStrings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Autocompleter withoutTrashed()
 */
	class Autocompleter extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperAward
 * @property int $id
 * @property string $name
 * @property string $subtitle
 * @property string $slug
 * @property int $order
 * @property string|null $comments
 * @property bool $enabled
 * @property bool $nominations_enabled
 * @property bool $secret
 * @property int|null $winner_image_id
 * @property int|null $autocompleter_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Autocompleter|null $autocompleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AwardFeedback> $awardFeedback
 * @property-read int|null $award_feedback_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AwardSuggestion> $awardSuggestions
 * @property-read int|null $award_suggestions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Nominee> $nominees
 * @property-read int|null $nominees_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Result> $results
 * @property-read int|null $results_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserNominationGroup> $userNominationGroups
 * @property-read int|null $user_nomination_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserNomination> $userNominations
 * @property-read int|null $user_nominations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vote> $votes
 * @property-read int|null $votes_count
 * @property-read \App\Models\File|null $winnerImage
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award notSecret()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereAutocompleterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereNominationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award whereWinnerImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Award withoutTrashed()
 */
	class Award extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperAwardFeedback
 * @property int $id
 * @property int $award_id
 * @property string $opinion
 * @property string $fuzzy_user_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Award $award
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardFeedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardFeedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardFeedback query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardFeedback whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardFeedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardFeedback whereFuzzyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardFeedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardFeedback whereOpinion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardFeedback whereUpdatedAt($value)
 */
	class AwardFeedback extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperAwardSuggestion
 * @property int $id
 * @property string $fuzzy_user_id
 * @property string $suggestion
 * @property int|null $award_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Award|null $award
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardSuggestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardSuggestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardSuggestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardSuggestion whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardSuggestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardSuggestion whereFuzzyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardSuggestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardSuggestion whereSuggestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwardSuggestion whereUpdatedAt($value)
 */
	class AwardSuggestion extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperFile
 * @property int $id
 * @property string $subdirectory
 * @property string $filename
 * @property string $extension
 * @property string $entity
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereSubdirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereUpdatedAt($value)
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperGameRelease
 * @property int $id
 * @property string $list
 * @property string $name
 * @property int $notable
 * @property array<array-key, mixed> $platforms
 * @property string|null $url
 * @property string $source
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read mixed $mobile
 * @property-read mixed $nintendo
 * @property-read mixed $pc
 * @property-read mixed $playstation
 * @property-read mixed $vr
 * @property-read mixed $xbox
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease whereList($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease whereNotable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease wherePlatforms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameRelease withoutTrashed()
 */
	class GameRelease extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperIpAddress
 * @property int $id
 * @property string $ip
 * @property bool|null $whitelisted
 * @property int $abuse_score
 * @property string|null $country_code
 * @property string|null $domain
 * @property string|null $usage_type
 * @property string|null $isp
 * @property int $report_count
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereAbuseScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereIsp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereReportCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereUsageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereWhitelisted($value)
 */
	class IpAddress extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperLogin
 * @property int $id
 * @property int $user_id
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereUserId($value)
 */
	class Login extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperLootboxItem
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $css
 * @property bool $buddie
 * @property bool $music
 * @property string|null $css_contents
 * @property string $series
 * @property string|null $drop_chance
 * @property string|null $absolute_drop_chance
 * @property string|null $cached_drop_value_start
 * @property string|null $cached_drop_value_end
 * @property string|null $extra
 * @property int|null $image_id
 * @property int|null $music_file_id
 * @property int $lootbox_tier_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $additionalFiles
 * @property-read int|null $additional_files_count
 * @property-read \App\Models\File|null $image
 * @property-read \App\Models\LootboxTier $lootboxTier
 * @property-read \App\Models\File|null $musicFile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserInventoryItem> $userInventoryItems
 * @property-read int|null $user_inventory_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereAbsoluteDropChance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereBuddie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereCachedDropValueEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereCachedDropValueStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereCssContents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereDropChance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereLootboxTierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereMusic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereMusicFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereSeries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxItem whereUpdatedAt($value)
 */
	class LootboxItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperLootboxTier
 * @property int $id
 * @property string $name
 * @property string|null $color
 * @property string $drop_chance
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LootboxItem> $lootboxItems
 * @property-read int|null $lootbox_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxTier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxTier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxTier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxTier whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxTier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxTier whereDropChance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxTier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxTier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LootboxTier whereUpdatedAt($value)
 */
	class LootboxTier extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperNews
 * @property int $id
 * @property string|null $headline
 * @property string $text
 * @property int $user_id
 * @property \Carbon\CarbonImmutable $show_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read mixed $new
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereHeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereShowAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News withoutTrashed()
 */
	class News extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperNominee
 * @property int $id
 * @property string $name
 * @property string $subtitle
 * @property string $slug
 * @property string $flavor_text
 * @property int $award_id
 * @property int|null $image_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\Award $award
 * @property-read \App\Models\File|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserNominationGroup> $userNominationGroups
 * @property-read int|null $user_nomination_groups_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereFlavorText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nominee withoutTrashed()
 */
	class Nominee extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperPermission
 * @property string $id
 * @property string $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $parents
 * @property-read int|null $parents_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperResult
 * @property int $id
 * @property int $award_id
 * @property string $filter
 * @property string $algorithm
 * @property array<array-key, mixed> $results
 * @property array<array-key, mixed> $steps
 * @property array<array-key, mixed> $warnings
 * @property int $votes
 * @property string $time_key
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Award $award
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereAlgorithm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereFilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereTimeKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Result whereWarnings($value)
 */
	class Result extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperTableHistory
 * @property int $id
 * @property string $table
 * @property string $entry
 * @property array<array-key, mixed> $values
 * @property int|null $user_id
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \App\Models\Action|null $action
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableHistory whereEntry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableHistory whereTable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableHistory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableHistory whereValues($value)
 */
	class TableHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperTemplate
 * @property int $id
 * @property string $filename
 * @property string $name
 * @property string|null $details
 * @property string $source
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Template whereUpdatedAt($value)
 */
	class Template extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperUser
 * @property int $id
 * @property string $name
 * @property string $steam_id
 * @property bool $team_member
 * @property \Carbon\CarbonImmutable|null $first_login
 * @property \Carbon\CarbonImmutable|null $last_login
 * @property string|null $primary_role
 * @property string|null $email
 * @property string|null $website
 * @property string|null $avatar_url
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Login> $logins
 * @property-read int|null $logins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vote> $votes
 * @property-read int|null $votes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VotingCodeLog> $votingCodeLogs
 * @property-read int|null $voting_code_logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePrimaryRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSteamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTeamMember($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWebsite($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperUserInventoryItem
 * @property int $id
 * @property string $fuzzy_user_id
 * @property int $lootbox_item_id
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \App\Models\LootboxItem $lootboxItem
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInventoryItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInventoryItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInventoryItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInventoryItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInventoryItem whereFuzzyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInventoryItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInventoryItem whereLootboxItemId($value)
 */
	class UserInventoryItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperUserNomination
 * @property int $id
 * @property int|null $award_id
 * @property string $fuzzy_user_id
 * @property string $nomination
 * @property int $user_nomination_group_id
 * @property int|null $original_group_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Award|null $award
 * @property-read \App\Models\UserNominationGroup|null $originalGroup
 * @property-read \App\Models\UserNominationGroup $userNominationGroup
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination whereFuzzyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination whereNomination($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination whereOriginalGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNomination whereUserNominationGroupId($value)
 */
	class UserNomination extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperUserNominationGroup
 * @property int $id
 * @property int $award_id
 * @property string $name
 * @property bool $ignored
 * @property int|null $nominee_id
 * @property int|null $merged_into_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Award $award
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserNominationGroup> $mergedFrom
 * @property-read int|null $merged_from_count
 * @property-read UserNominationGroup|null $mergedInto
 * @property-read \App\Models\Nominee|null $nominee
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserNomination> $userNominations
 * @property-read int|null $user_nominations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup whereIgnored($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup whereMergedIntoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup whereNomineeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNominationGroup whereUpdatedAt($value)
 */
	class UserNominationGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperVote
 * @property int $id
 * @property int $award_id
 * @property int|null $user_id
 * @property string $cookie_id
 * @property array<array-key, mixed> $preferences
 * @property string $ip
 * @property string|null $voting_code
 * @property int|null $voting_group
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Award $award
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereCookieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote wherePreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereVotingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereVotingGroup($value)
 */
	class Vote extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperVotingCodeLog
 * @property int $id
 * @property string $cookie_id
 * @property \Carbon\CarbonImmutable $created_at
 * @property string $ip
 * @property string $code
 * @property string|null $referer
 * @property int|null $user_id
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog whereCookieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VotingCodeLog whereUserId($value)
 */
	class VotingCodeLog extends \Eloquent {}
}

