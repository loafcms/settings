<?php

namespace Loaf\Settings;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class Configurable
 *
 * Construction configurable with a configuration
 *
 * @package Loaf\Settings
 */
abstract class Configurable {

    protected $config;

    abstract protected function getConfigValidationRules();

    public static function make( array $config )
    {
        return app()->makeWith( static::class, compact('config' ) );
    }

    protected function getPublicTypes() : array
    {
        return collect( ( new \ReflectionClass( $this ) )
            ->getProperties( \ReflectionProperty::IS_PUBLIC ) )
            ->pluck('name')
            ->all();
    }

    protected function getDefaults() : array
    {
        return [];
    }

    public function __construct( Factory $validation_factory, array $config = null )
    {
        $config = array_merge( $this->getDefaults(), $config );

        $validator = $validation_factory->make( $config, $this->getConfigValidationRules() );

        if( $validator->invalid() )
            throw new SettingsException("Invalid element config");

        $this->config = $config;

        foreach( $this->getPublicTypes() as $type )
            $this->$type = $config[ $type ] ?? null;
    }

    public function __get( $key )
    {
        if( in_array( $key, $this->getPublicTypes() ) )
            return $this->$key;

        return $this->config[ $key ] ?? null;
    }

}