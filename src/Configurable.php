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
        $this->config = $config;

        // Validate the input
        $validator = $validation_factory->make( $config, $this->getConfigValidationRules() );
        if( $validator->fails() )
            throw new SettingsException("Invalid element config");

        // Set all values on the public properties of the class
        foreach( $this->getPublicTypes() as $type ){
            if( isset($config[ $type ]) ){
                $this->$type = $config[ $type ];
                unset( $config[ $type ] );
            }
        }
    }

    public function __get( $key )
    {
        return $this->config[ $key ] ?? null;
    }

}