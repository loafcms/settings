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
     * @param Field $field
     */
    public function __construct( string $type, Field $field );

    /**
     * The appropriate database model
     *
     * @return SettingModel
     */
    public function getModel() : SettingModel;

    /**
     * Type of the setting
     *
     * @return string
     */
    public function getType() : string;

    /**
     * Retrieve the field
     *
     * @return Field
     */
    public function getField() : Field;

    /**
     * Returns the edit model for this type
     *
     * @return mixed
     */
    public function getEditView();

    /**
     * Array of validation rules for the edit view
     *
     * @return array
     */
    public function getEditValidationRules() : array;
}