<?php

namespace Loaf\Settings\Types;

use Loaf\Settings\Configuration\Field;
use Loaf\Settings\Models\SettingModel;

/**
 * Class SettingTypeConfig
 *
 * Class specifying a type of setting
 *
 * @package Loaf\Settings
 */
abstract class BaseSettingType implements SettingType
{
    /**
     * @var string name of this type
     */
    protected $type;

    /**
     * @var Field that this setting corresponds to
     */
    protected $field;

    /**
     * @var SettingModel model of this type
     */
    protected $model;

    /**
     * @inheritdoc
     */
    public function __construct( string $type, Field $field )
    {
        $this->type = $type;
        $this->field = $field;
        $this->model = $this->makeModel();
    }

    /**
     * @inheritdoc
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getModel(): SettingModel
    {
        return $this->model;
    }

    /**
     * @return SettingModel return an instantiated settings model
     */
    protected abstract function makeModel() : SettingModel;

    /**
     * @inheritdoc
     */
    public function getEditView()
    {
        return view('loaf/settings::types/base/edit', [
            'field' => $this->getField(),
            'model' => $this->getModel()
        ]);
    }

    public function getEditValidationRules() : array
    {
        return [];
    }

}