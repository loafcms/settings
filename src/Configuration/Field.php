<?php

namespace Loaf\Settings\Configuration;

class Field extends BaseConfigElement {

    public $type;

    public function getConfigValidationRules()
    {
        return [
            'type' => 'required|string',
            'label' => 'required|string',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ];
    }

}