<?php

namespace Loaf\Settings\Configuration;

class Group extends BaseConfigElement {

    protected $map = [
        'field' => Field::class
    ];

    public function getConfigValidationRules()
    {
        return [
            'label' => 'nullable|string',
            'order' => 'nullable|integer',
            'fields' => 'nullable|array',
        ];
    }

}