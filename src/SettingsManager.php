<?php

namespace Loaf\Settings;

use Illuminate\Contracts\Logging\Log;
use Loaf\Base\Contracts\Settings\SettingsManager as SettingsManagerContract;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

use Loaf\Settings\Configuration\Field;
use Loaf\Settings\Configuration\Group;
use Loaf\Settings\Configuration\Section;
use Loaf\Settings\Models\SettingModel;

use Illuminate\Support\Collection;

class SettingsManager implements SettingsManagerContract
{
    /**
     * @const string default scope (level)-(name)
     */
    protected const DEFAULT_SCOPE = '0-default';

    /**
     * @var array types, with native types already registered
     */
    protected $types = [];

    /**
     * @var Collection
     */
    protected $sections;

    /**
     * @var bool Whether the config is already parsed
     */
    protected $parsed = false;

    /**
     * @var ConfigRepository for storing configuration
     */
    protected $config;

    /**
     * @var CacheRepository repository
     */
    protected $cache;

    /**
     * @var Log logger
     */
    protected $log;

    /**
     * @var array of configs to merge, files are filenames that must be requires and merged
     */
    protected $config_queue = [];

    /**
     * @var ValidationFactory
     */
    protected $validation_factory;

    public function __construct(
        ValidationFactory $factory,
        ConfigRepository $config,
        CacheRepository $cache,
        Log $log
    )
    {
        $this->validation_factory = $factory;
        $this->config = $config;
        $this->cache = $cache;
        $this->log = $log;
    }

    /**
     * @inheritdoc
     */
    public function get( string $path, $default = null )
    {
        $field = $this->getField( $path );

        if( !($setting = Setting::wherePath( $path )->first()) )
            return $default;

        return $this->getModel( $field )
            ->deserialize( $setting->value );
    }

    /**
     * @inheritdoc
     */
    public function set( string $path, $value )
    {
        $field = $this->getField( $path );

        $value = $this->getModel( $field )
            ->serialize( $value );

        Setting::firstOrNew([
                'path' => $path
            ],[
                'scope' => self::getDefaultScope(),
                'type'  => $this->getField( $path )->type
            ])
            ->fill(['value' => $value])
            ->save();
    }

    /**
     * @inheritdoc
     */
    public function forget( string $path )
    {
        if( $setting = Setting::wherePath( $path )->first() )
            $setting->delete();
    }

    /**
     * @inheritdoc
     */
    public function registerType( string $type, array $config = [] )
    {
        $this->types[ $type ] = app()
            ->makeWith( SettingTypeConfig::class, compact('type', 'config'));
    }

    /**
     * @inheritdoc
     */
    public function mergeConfigFrom(string $path, bool $force = false)
    {
        $this->config_queue[] = [ 'file', $path ];

        if( !$force )
            $this->guardAlreadyParsed();

        if( $force && $this->parsed )
            $this->parseConfig();
    }

    /**
     * @inheritdoc
     */
    public function mergeConfig(array $config, bool $force = false)
    {
        $this->config_queue[] = [ 'array', $config ];

        if( !$force )
            $this->guardAlreadyParsed();

        if( $force && $this->parsed )
            $this->parseConfig();
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultScope() : string
    {
        return self::DEFAULT_SCOPE;
    }

    /**
     * @inheritdoc
     */
    public function parseConfig( )
    {
        $config_cache = $this->cache->get(
            $cache_key = $this->getCacheKey('config')
        );

        if( $config_cache ) {

            if( $this->validConfigCache( $config_cache ) ) {
                $this->sections = $config_cache['sections'];
                $this->parsed = true;
                return;
            }

            $this->cache->forget( $cache_key );

        }

        foreach( $this->config_queue as list( $type, $config ) ) {

            if( $type == 'file' ) {
                $config = require $config;
                $type = 'array';
            }

            if( $type !== 'array' )
                throw new SettingsException("Unsupported config type $type");

            foreach( $config as $section => $section_config ) {

                // Merge the existing section with the new section config
                $section_config = array_merge( $this->config->get( $section, [] ), $section_config );

                foreach( $section_config['groups'] ?? [] as $group => $group_config ) {

                    // Merge the existing group with the new group config
                    $group_config = array_merge( $this->config->get( "$section.groups.$group", [] ), $group_config );

                    $group_config['fields'] = array_merge( $this->config->get( "$section.groups.$group.fields", [] ), $group_config['fields'] ?? [] );

                    $section_config['groups'][ $group ] = $group_config;

                }

                $this->config->set( $section, $section_config );
            }

        }

        $this->loadSections();

        $this->cache->set($cache_key, [
            'fingerprint' => $this->getConfigFingerprint(),
            'sections' => $this->sections
        ], $this->getCacheTtl());

        $this->parsed = true;
    }

    /**
     * @inheritdoc
     */
    public function getSections( ) : Collection
    {
        $this->guardNotParsed();

        return $this->sections;
    }

    /**
     * @inheritdoc
     */
    public function getSection( string $section ) : Section
    {
        $this->guardNotParsed();

        if( !$this->sections->has( $section ) )
            throw new SettingsException("Section $section not found");

        return $this->sections->get( $section );
    }

    /**
     * @inheritdoc
     */
    public function getGroup( string $path ) : Group
    {
        $parts = explode('.', $path);

        if( count($parts) !== 2 )
            throw new SettingsException("Invalid group path $path");

        list( $section, $group ) = $parts;

        $section = $this->getSection( $section );

        if( !$section->groups->has( $group ) )
            throw new SettingsException("Group $path not found");

        return $section->groups->get( $group );
    }

    /**
     * @inheritdoc
     */
    public function getField( string $path ) : Field
    {
        $parts = explode('.', $path);

        if( count($parts) !== 3 )
            throw new SettingsException("Invalid field path $path");

        list( $section, $group, $field ) = $parts;

        $group = $this->getGroup("$section.$group");

        if( !$group->fields->has( $field ) )
            throw new SettingsException("Field $path not found");

        return $group->fields->get( $field );
    }

    /**
     * Get setting model from field
     *
     * @param Field $field
     * @return SettingModel
     * @throws SettingsException
     */
    protected function getModel( Field $field ) : SettingModel
    {
        $type = $field->type;

        if( !($type_config = $this->getTypeConfig( $type )) )
            throw new SettingsException("Setting type $type not registered");

        $model = $type_config->model;

        return new $model( $field );
    }

    /**
     * Reload all sections from configuration
     */
    private function loadSections()
    {
        $sections = collect();
        foreach( $this->config->all() as $key => $section_config )
            $sections[ $key ] = app()->makeWith( Section::class, ['config' => $section_config]);

        $this->sections = $sections;
    }

    /**
     * Get SettingTypeConfig for type id
     *
     * @param string $type
     * @return SettingTypeConfig|null
     */
    protected function getTypeConfig( string $type ) : ?SettingTypeConfig
    {
        return $this->types[ $type ] ?? null;
    }


    protected function guardAlreadyParsed()
    {
        if( !$this->parsed )
            return;

        throw new SettingsException("Won't merge settings configs when already parsed.");
    }

    protected function guardNotParsed()
    {
        if( $this->parsed )
            return;

        throw new SettingsException("Config is not parsed yet");
    }

    /**
     * @TODO implement this
     */
    protected function getCacheTtl()
    {
        // Minutes
        return 10;
    }

    /**
     * Return a cache key for something
     *
     * @param string $id
     * @return string
     */
    protected function getCacheKey( string $id ) : string
    {
        return "settingsmanager.$id";
    }

    /**
     * Check if $cache is valid config cache
     *
     * @param $cache
     * @return bool
     */
    protected function validConfigCache( $cache ) : bool
    {
        if( !$cache )
            return false;

        if( !is_array($cache) )
            return false;

        if( !array_key_exists('fingerprint', $cache) || !array_key_exists('sections', $cache) )
            return false;

        if( $cache['fingerprint'] !== $this->getConfigFingerprint() )
            return false;

        return true;
    }

    /**
     * Return a fingerprint that identifies the currently registered config files and arrays
     *
     * @return string
     */
    protected function getConfigFingerprint() : string
    {
        return md5( serialize( $this->config_queue ) );
    }

}