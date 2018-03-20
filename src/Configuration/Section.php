<?php

namespace Loaf\Settings\Configuration;

class Section extends BaseConfigElement {

    protected $map = [
        'group' => Group::class
    ];

    public function getConfigValidationRules()
    {
        return [
            'label' => 'nullable|string',
            'order' => 'nullable|integer',
            'groups' => 'nullable|array',
        ];
    }

}