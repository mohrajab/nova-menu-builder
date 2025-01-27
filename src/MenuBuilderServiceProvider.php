<?php

namespace OptimistDigital\MenuBuilder;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use OptimistDigital\MenuBuilder\Http\Middleware\Authorize;
use OptimistDigital\MenuBuilder\Http\Resources\MenuResource;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class MenuBuilderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-menu');

        $this->app->booted(function () {
            $this->routes();
        });

        $this->publishMigrations();

        $this->publishViews();

        $this->publishConfig();

        Nova::serving(function (ServingNova $event) {
            //
        });

        Nova::resources([
            config('nova-menu.resource', MenuResource::class),
        ]);
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->namespace('OptimistDigital\MenuBuilder\Http\Controllers')
            ->prefix('nova-vendor/nova-menu')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Publish required migration
     */
    private function publishMigrations()
    {
        $this->publishes([
            __DIR__ . '/Migrations/create_menus_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_menus_table.php'),
        ], 'nova-menu-builder-migrations');
    }

    /**
     * Publish sidebar menu item template
     */
    private function publishViews()
    {
        $this->publishes([
            __DIR__.'/../resources/views/' => resource_path('views/vendor/nova-menu'),
        ], 'nova-menu-builder-views');
    }

    /**
     * Publish config
     */
    private function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/' => config_path(),
        ], 'nova-menu-builder-config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
