<?php

namespace Loaf\Settings\Types;

use Loaf\Settings\Configuration\Field;
use Loaf\Settings\Models\SettingModel;

/**
 * Class SettingTypeConfig.
 *
 * Class specifying a type of setting
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
     * {@inheritdoc}
     */
    public function __construct(string $type, Field $field)
    {
        $this->type = $type;
        $this->field = $field;
        $this->model = $this->makeModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel(): SettingModel
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormName($key = '_value'): string
    {
        $field_path = $this->field->getPath();
        $name = $field_path;

        if ($key) {
            $name .= ".$key";
        }

        return 'settings['.str_replace('.', '][', $name).']';
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationKey($key = '_value') : string
    {
        return $this->getFormKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($key = '_value'): string
    {
        return $this->field->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getId($key = '_value'): string
    {
        return $this->getValidationKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormKey($key = null): string
    {
        $field_path = $this->field->getPath();
        $name = $field_path;

        if ($key) {
            $name .= ".$key";
        }

        return "settings.$name";
    }

    /**
     * @return SettingModel return an instantiated settings model
     */
    abstract protected function makeModel() : SettingModel;

    /**
     * {@inheritdoc}
     */
    public function getEditView($value)
    {
        return view('loaf/settings::types/base/edit', [
            'type'  => $this,
            'value' => $value,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEditValidationRules() : array
    {
        return [
            '_value' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parseEditFormData(array $data) : array
    {
        // Always save the data but set it to null if not supplied
        return [true, $data['_value'] ?? null];
    }
}
