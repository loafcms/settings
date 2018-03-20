<?php

namespace Loaf\Settings;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Cache\Repository as CacheRepository;

use Illuminate\Contracts\Logging\Log;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

use Loaf\Base\Contracts\Settings\SettingsManager as SettingsManagerContract;

use Loaf\Settings\Models\BooleanSetting;

class SettingsServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register()
    {
        $this->app->singleton('settings.config', function(){
            return new ConfigRepository();
        } );

        $this->app->singleton(SettingsManagerContract::class, function ( $app ) {
            return (new SettingsManager(
                app( ValidationFactory::class ),
                app('settings.config'),
                app( CacheRepository::class ),
                app( Log::class )
            ));
        });

        $this->app->resolving( SettingsManager::class, function( SettingsManager $manager ){
            $manager->registerType('string', ['validation' => 'string']);
            $manager->registerType('integer', ['validation' => 'integer']);
            $manager->registerType('array', ['validation' => 'array']);
            $manager->registerType('boolean', ['validation' => 'boolean', 'model' => BooleanSetting::class]);

            $manager->mergeConfigFrom( __DIR__."/../config/settings.php" );
        } );

        $this->app->afterResolving( SettingsManager::class, function (SettingsManager $manager) {
            $manager->parseConfig();
        });

    }

}
