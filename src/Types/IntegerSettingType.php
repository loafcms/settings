<?php

namespace Loaf\Settings\Types;

use Loaf\Settings\Models\SettingModel;
use Loaf\Settings\Models\IntegerSetting;

class IntegerSettingType extends BaseSettingType
{
    public function makeModel(): SettingModel
    {
        return new IntegerSetting( $this->field );
    }

    public function getEditValidationRules(): array
    {
        return [
            '_value' => 'nullable|integer'
        ];
    }
}