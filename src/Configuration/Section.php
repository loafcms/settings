<?php

namespace Loaf\Settings\Configuration;

use Illuminate\Support\Collection;

class Section extends BaseConfigElement
{
    protected $map = [
        'group' => Group::class,
    ];

    /**
     * @var Collection
     */
    public $groups;

    /**
     * @return string e.g. section
     */
    public function getPath() : string
    {
        return $this->key;
    }

    public function getConfigValidationRules()
    {
        return parent::getConfigValidationRules() + [
            'label'  => 'nullable|string',
            'groups' => 'nullable|array',
        ];
    }
}
