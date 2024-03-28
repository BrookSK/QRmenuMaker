<?php

namespace Modules\Stripelinks\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as Provider;
use Illuminate\Database\Eloquent\Factory;

class Main extends Provider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadConfig();
        $this->loadViews();
        $this->loadViewComponents();
        $this->loadTranslations();
        $this->loadMigrations();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadRoutes();
    }

    /**
     * Load config.
     *
     * @return void
     */
    protected function loadConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('stripelinks.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'stripelinks'
        );
    }

    /**
     * Load views.
     *
     * @return void
     */
    public function loadViews()
    {
        $viewPath = resource_path('views/modules/stripelinks');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/stripelinks';
        }, \Config::get('view.paths')), [$sourcePath]), 'stripelinks');
    }

    /**
     * Load view components.
     *
     * @return void
     */
    public function loadViewComponents()
    {
        Blade::componentNamespace('Modules\Stripelinks\View\Components', 'stripelinks');
    }

    /**
     * Load translations.
     *
     * @return void
     */
    public function loadTranslations()
    {
        $langPath = resource_path('lang/modules/stripelinks');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'stripelinks');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang/en', 'stripelinks');
        }
    }

    /**
     * Load migrations.
     *
     * @return void
     */
    public function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Load routes.
     *
     * @return void
     */
    public function loadRoutes()
    {
        if (app()->routesAreCached()) {
            return;
        }

        $routes = [
            'web.php',
            'api.php',
        ];

        foreach ($routes as $route) {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/' . $route);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
