<?php

namespace Loaf\Settings\Models;

use Loaf\Settings\Configuration\Field as FieldConfig;

abstract class BaseSetting implements SettingModel {

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

    /**
     * @inheritdoc
     */
    public function editView()
    {
        return view('loaf/settings::types/base/edit', ['field' => $this->field, 'model' => $this]);
    }

}