<?php

namespace Loaf\Settings\Configuration;

use Illuminate\Contracts\Validation\Factory;
use Loaf\Base\Contracts\Settings\ConfigElement;
use Loaf\Settings\ConfigurableElement;

abstract class BaseConfigElement extends ConfigurableElement implements ConfigElement
{
    protected $map = [];

    public function __construct(Factory $validation_factory, array $config = null)
    {
        parent::__construct($validation_factory, $config);

        foreach( $this->map as $key => $model ) {

            if( isset( $config[ $model ] ) )
                $this->$model = (new $model( $validation_factory, $config[ $model ] ));

            $models = str_plural( $key );

            if( isset( $config[ $models ] ) ){
                $instances = collect();
                foreach( $config[ $models ] as $model_key => $model_config )
                    $instances[ $model_key ] = (new $model( $validation_factory, $model_config ));
                $config[ $models ] = $instances;
            }

        }

        // Update config
        $this->config = $config;

        foreach( $this->getPublicTypes() as $type )
            $this->$type = $config[ $type ] ?? null;
    }

    public function getLabel(): string
    {
        if( $label = $this->label ?? null )
            return $label;

        return 'unknown label';
    }
}