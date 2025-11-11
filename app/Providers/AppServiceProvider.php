<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Steam\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('steam', Provider::class);
        });

        Blueprint::macro('foreignIdString', function (string $column) {
            return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
                'type' => 'string',
                'name' => $column,
                'length' => 255,
            ]));
        });

        Gate::before(function (?User $user, $ability) {
            // TODO: this will be adjusted once anonymous users are properly supported
            if (!$user) {
                return false;
            }

            return $user->canDo($ability);
        });

        Blade::directive('year', fn () => '<?php echo year(); ?>');

        Date::use(CarbonImmutable::class);
        Model::shouldBeStrict();
    }
}
