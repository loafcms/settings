<?php

namespace Loaf\Settings\Models;

use Loaf\Settings\Configuration\Field as FieldConfig;

/**
 * Class BaseSetting
 *
 * Database model for storing a setting
 *
 * @package Loaf\Settings\Models
 */
abstract class BaseSetting implements SettingModel
{
    /**
     * @var FieldConfig
     */
    protected $field;

    /**
     * @inheritdoc
     */
    public function __construct( FieldConfig $field = null )
    {
        $this->field = $field;
    }

    /**
     * @inheritdoc
     */
    public function serialize( $value )
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function deserialize( $value )
    {
        return $value;
    }
}