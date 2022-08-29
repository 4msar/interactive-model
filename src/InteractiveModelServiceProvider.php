<?php

namespace MSAR\InteractiveModel;

/*
 *
 * @author Saiful Alam <packages@msar.me>
 */

use Illuminate\Support\ServiceProvider;

class InteractiveModelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->registerModelGenerator();
    }

    private function registerModelGenerator()
    {
        $this->app->singleton('command.interactive.model', function ($app) {
            return $app['MSAR\InteractiveModel\Commands\InteractiveCommand'];
        });

        $this->commands('command.interactive.model');
    }
}