<?php

namespace Loaf\Settings\Configuration;

use Illuminate\Contracts\Validation\Factory;
use Loaf\Base\Contracts\Settings\ConfigElement;
use Loaf\Settings\Configurable;
use Loaf\Settings\SettingsManager;

abstract class BaseConfigElement extends Configurable implements ConfigElement
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
        $config['key'] = $key;

        parent::__construct($validation_factory, $config);

        $this->parent = $parent;
        $this->key = $key;

        foreach ($this->map as $key => $model) {
            if (isset($this->config[$model])) {
                $this->$model = (new $model($validation_factory, $this->config[$model], $key, $this));
            }

            $models = str_plural($key);

            if (isset($this->config[$models])) {
                $instances = collect();
                foreach ($this->config[$models] as $model_key => $model_config) {
                    $instances[$model_key] = (new $model($validation_factory, $model_config, $model_key, $this));
                }
                $this->config[$models] = $instances;
            }
        }

        foreach ($this->getPublicTypes() as $type) {
            $this->$type = $this->config[$type] ?? null;
        }
    }

    public function getLabel(): string
    {
        if ($label = $this->label ?? null) {
            return $label;
        }

        return 'unknown label';
    }

    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    /**
     * Get the identifying key of the element.
     *
     * @return null|string
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * Default configuration values.
     *
     * @return array
     */
    public function getDefaults(): array
    {
        return [
            'order' => 0,
        ];
    }

    /**
     * Validation rules for configuration.
     *
     * @return array
     */
    public function getConfigValidationRules()
    {
        return [
            'order' => 'nullable|integer',
            'key'   => 'required',
        ];
    }

    /**
     * Return a reference to manager.
     *
     * @return SettingsManager
     */
    protected function getManager()
    {
        return app(SettingsManager::class);
    }
}
