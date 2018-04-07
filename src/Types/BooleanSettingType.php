<?php

namespace Loaf\Settings\Types;

use Loaf\Settings\Models\BooleanSetting;
use Loaf\Settings\Models\SettingModel;

class BooleanSettingType extends BaseSettingType
{
    /**
     * {@inheritdoc}
     */
    public function makeModel(): SettingModel
    {
        return new BooleanSetting($this->field);
    }

    /**
     * Get the description for a checkbox.
     *
     * @return string
     */
    public function getDescription() : ?string
    {
        return $this->field->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getEditView($value)
    {
        return view('loaf/settings::types/boolean/edit', [
            'type'  => $this,
            'value' => $value,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEditValidationRules(): array
    {
        return [
            '_value' => 'nullable|boolean',
        ];
    }

    /**
     * Make a boolean from the from data.
     *
     * {@inheritdoc}
     */
    public function parseEditFormData(array $data) : array
    {
        return [true, isset($data['_value']) ? true : false];
    }
}
