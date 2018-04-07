<?php

namespace Loaf\Settings\Types;

use Loaf\Settings\Configuration\Field;
use Loaf\Settings\Models\SettingModel;

interface SettingType
{
    /**
     * SettingType constructor.
     *
     * Instantiate the type with the corresponding field
     *
     * @param string $type
     * @param Field  $field
     */
    public function __construct(string $type, Field $field);

    /**
     * The appropriate database model.
     *
     * @return SettingModel
     */
    public function getModel() : SettingModel;

    /**
     * Type of the setting.
     *
     * @return string
     */
    public function getType() : string;

    /**
     * Retrieve the field.
     *
     * @return Field
     */
    public function getField() : Field;

    /**
     * Returns the edit model for this type.
     *
     * @param mixed $value Current value of the setting
     *
     * @return mixed
     */
    public function getEditView($value);

    /**
     * Get the form name for a setting field.
     *
     * @param null $field dot notation
     *
     * @return string like 'settings[section][group][field][sub][field]'
     */
    public function getFormName($field = '_value') : string;

    /**
     * Get the dot notation key of the form data.
     *
     * @param null $field dot notation
     *
     * @return string like 'settings.section.group.field'
     */
    public function getFormKey($field = null) : string;

    /**
     * Get validation key for a settings field.
     *
     * @param null $field dot notation
     *
     * @return string like 'settings.section.group.field.sub.field'
     */
    public function getValidationKey($field = '_value') : string;

    /**
     * Get the label for a setting field.
     *
     * @param string $key
     *
     * @return string
     */
    public function getLabel($key = '_value') : string;

    /**
     * Get the ID of a setting field.
     *
     * @param string $key
     *
     * @return string
     */
    public function getId($key = '_value') : string;

    /**
     * Consume the edit form data before storing it in the model
     * Returns an array with whether the $parsed data should $save.
     *
     * @param array $data
     *
     * @return array [ $save, $parsed ]
     */
    public function parseEditFormData(array $data) : array;

    /**
     * Array of validation rules for the edit view.
     *
     * @return array associative array of validation rules, use _value for the default value
     */
    public function getEditValidationRules() : array;
}
