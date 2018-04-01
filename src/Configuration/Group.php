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

    /**
     * @var Field
     */
    public $fields;

    public function getConfigValidationRules()
    {
        return parent::getConfigValidationRules() + [
            'label' => 'nullable|string',
            'fields' => 'nullable|array',
        ];
    }

    public function getPath() : string
    {
        return $this->parent->getPath().'.'.$this->key;
    }

}