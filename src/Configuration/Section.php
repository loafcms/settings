<?php

namespace Loaf\Settings\Configuration;

use Illuminate\Support\Collection;

class Section extends BaseConfigElement
{
    protected $map = [
        'group' => Group::class
    ];

    /**
     * @var Collection
     */
    public $groups;

    public function getPath() : string
    {
        return $this->key;
    }

    public function getConfigValidationRules()
    {
        return [
            'label' => 'nullable|string',
            'order' => 'nullable|integer',
            'groups' => 'nullable|array',
        ];
    }

}