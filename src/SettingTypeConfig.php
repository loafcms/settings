<?php

namespace Loaf\Settings;

use Illuminate\Contracts\Validation\Factory;
use Loaf\Settings\Models\SimpleSetting;

class SettingTypeConfig extends ConfigurableElement {

    /**
     * @var string
     */
    public $type;

    public function getConfigValidationRules()
    {
        return [
            'type' => 'required|string',
            'model' => 'required|string'
        ];
    }

    public function getDefaults(): array
    {
        return [
            'validation' => null,
            'model' => SimpleSetting::class,
        ];
    }

    public function __construct( Factory $validation_factory, string $type, array $config = [] )
    {
        $config['type'] = $type;
        parent::__construct( $validation_factory, $config );
    }

}