<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
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

        Date::use(CarbonImmutable::class);
        Model::shouldBeStrict();
    }
}
