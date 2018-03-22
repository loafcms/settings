<?php

namespace Loaf\Settings\Configuration;

class Group extends BaseConfigElement
{

    /**
     * @var Section
     */
    protected $parent;

    /**
     * @var array Map config key to model
     */
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

    public function getPath() : string
    {
        return $this->parent->getPath().'.'.$this->key;
    }

}