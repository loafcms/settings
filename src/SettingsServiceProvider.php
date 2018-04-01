<?php

namespace Loaf\Settings;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Cache\Repository as CacheRepository;

use Illuminate\Contracts\Logging\Log;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

use Illuminate\Support\ServiceProvider;
use Loaf\Base\Contracts\Menu\AdminMenu;
use Loaf\Base\Contracts\Menu\Builder;
use Loaf\Base\Contracts\Settings\SettingsManager as SettingsManagerContract;

use Loaf\Settings\Configuration\Section;
use Loaf\Settings\Policies\SectionPolicy;

use Gate;
use Loaf\Settings\Types\BooleanSettingType;
use Loaf\Settings\Types\IntegerSettingType;
use Loaf\Settings\Types\StringSettingType;

class SettingsServiceProvider extends ServiceProvider {

    /**
     * @var AdminMenu
     */
    protected $admin_menu;

    /**
     * @var SettingsManager
     */
    protected $settings_manager;

    /**
     * Policy mappings for settings
     *
     * @var array
     */
    protected $policies = [
        Section::class => SectionPolicy::class
    ];

    public function boot( AdminMenu $admin_menu, SettingsManagerContract $settings_manager )
    {
        $this->admin_menu = $admin_menu;
        $this->settings_manager = $settings_manager;

        $namespace = 'loaf/settings';

        $this->registerPolicies();

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

        // Now hooking into SettingsManager because otherwise it hooks twice
        $this->app->resolving( SettingsManager::class, function( SettingsManager $manager ){
            $manager->registerType('string', StringSettingType::class);
            $manager->registerType('integer', IntegerSettingType::class);
            $manager->registerType('boolean', BooleanSettingType::class);

            $manager->mergeConfigFrom( __DIR__."/../config/settings.php" );
        } );

        $this->app->afterResolving( SettingsManager::class, function (SettingsManager $manager) {
            $manager->parseConfig();
        });

    }

    protected function registerMenu()
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
                    $n->settings->add( $section->getLabel(),  ['route'=>['admin.settings.editSection', 'section' => $key ]])
                        ->data('permission', ['view', $section]);

            });

        });
    }

    /**
     * Register the application's policies.
     *
     * @return void
     */
    protected function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

}
