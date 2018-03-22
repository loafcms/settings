<?php

namespace Loaf\Settings\Configuration;

use Illuminate\Contracts\Validation\Factory;
use Loaf\Base\Contracts\Settings\ConfigElement;
use Loaf\Settings\ConfigurableElement;
use Loaf\Settings\SettingsManager;

abstract class BaseConfigElement extends ConfigurableElement implements ConfigElement
{
    protected $map = [];

    /**
     * @var BaseConfigElement
     */
    protected $parent;

    /**
     * @var string
     */
    protected $key;

    public function __construct(Factory $validation_factory, array $config = null, string $key = null, $parent = null)
    {
        parent::__construct($validation_factory, $config);

        $this->parent = $parent;
        $this->key = $key;

        foreach( $this->map as $key => $model ) {

            if( isset( $config[ $model ] ) )
                $this->$model = (new $model( $validation_factory, $config[ $model ], $key, $this ));

            $models = str_plural( $key );

            if( isset( $config[ $models ] ) ){
                $instances = collect();
                foreach( $config[ $models ] as $model_key => $model_config )
                    $instances[ $model_key ] = (new $model( $validation_factory, $model_config, $model_key, $this ));
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

    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * Return a reference to manager
     * @return SettingsManager
     */
    protected function getManager()
    {
        return app( SettingsManager::class );
    }
}