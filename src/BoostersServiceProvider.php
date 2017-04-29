<?php
namespace GGuney\Boosters;

use Illuminate\Support\ServiceProvider;

class BoostersServiceProvider extends ServiceProvider
{

    protected $commands = [
        'GGuney\Boosters\Commands\MakeBoostedController'
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Publish/config/boosters.php', 'boosters');
        $this->commands($this->commands);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Publish/config/boosters.php' => config_path('boosters.php'),
        ]);

        $this->publishes([
            __DIR__ . '/Publish/views/bapp.blade.php' => resource_path('views/layouts/bapp.blade.php'),
        ], 'boosters');

        $this->publishes([
            __DIR__ . '/Publish/js/booster.js' => public_path('js/booster.js'),
        ], 'boosters');

        $this->publishes([
            __DIR__ . '/Publish/views/bodies' => resource_path('views/vendor/boosters/bodies'),
        ], 'boosters');

        $this->publishes([
            __DIR__ . '/Publish/views/components' => resource_path('views/vendor/boosters/components'),
        ], 'boosters');
    }
}
