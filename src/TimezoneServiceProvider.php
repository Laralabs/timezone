<?php

namespace Laralabs\Timezone;

use Illuminate\Support\ServiceProvider;
use Laralabs\Timezone\Facades\TimezoneFacade;

class TimezoneServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();

        $this->mergeConfigFrom(
            __DIR__.'/../config/timezone.php', 'timezone'
        );
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishConfig();
    }

    /**
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->app->bind('timezone', function () {
            return new Timezone();
        });

        $this->app->bind('TimezoneFacade', function () {
            return new TimezoneFacade();
        });
    }

    /**
     * @return void
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/timezone.php'  => config_path('timezone.php'),
        ], 'timezone-config');
    }
}
