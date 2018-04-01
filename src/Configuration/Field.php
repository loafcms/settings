<?php

namespace Loaf\Settings\Configuration;

class Field extends BaseConfigElement
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var Group
     */
    protected $parent;

    public function getConfigValidationRules()
    {
        return parent::getConfigValidationRules() + [
            'type'        => 'required|string',
            'label'       => 'required|string',
            'description' => 'nullable|string',
        ];
    }

    public function getPath() : string
    {
        return $this->parent->getPath().'.'.$this->key;
    }

    public function get($default = null)
    {
        return $this->getManager()->get($this->getPath(), $default);
    }
}
