<?php

namespace Loaf\Settings;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Cache\Repository as CacheRepository;

use Illuminate\Contracts\Logging\Log;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

use Loaf\Base\Contracts\Menu\AdminMenu;
use Loaf\Base\Contracts\Menu\Builder;
use Loaf\Base\Contracts\Settings\SettingsManager as SettingsManagerContract;

use Loaf\Settings\Models\BooleanSetting;

class SettingsServiceProvider extends \Illuminate\Support\ServiceProvider {

    /**
     * @var AdminMenu
     */
    protected $admin_menu;

    /**
     * @var SettingsManager
     */
    protected $settings_manager;

    public function boot( AdminMenu $admin_menu, SettingsManagerContract $settings_manager )
    {
        $this->admin_menu = $admin_menu;
        $this->settings_manager = $settings_manager;

        $namespace = 'loaf/settings';

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/settings.php');
        $this->loadViewsFrom( __DIR__ . '/../resources/views', $namespace);
        $this->loadTranslationsFrom( __DIR__ . '/../resources/lang', $namespace);
        $this->registerMenu();
    }

    public function register()
    {
        $this->app->bind('settings.config', function(){
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

    public function registerMenu()
    {

        $this->admin_menu->registerCallback('settings', 'main', function(Builder $m) {

            $m->group(array('prefix' => 'settings'), function(Builder $n)
            {
                $n->add(ucfirst(trans('loaf/admin::adminmenu.settings')))
                    ->nickname('settings')
                    ->data('cleanup', true)
                    ->data('order', 1010)
                    ->link->href('#');

                foreach( $this->settings_manager->getSections() as $key => $section )
                    $n->settings->add( $section->getLabel(),  ['route'=>['admin.settings', 'section' => $key ]]);

            });

        });
    }

}
