<?php

namespace Loaf\Settings\Types;

use Loaf\Settings\Models\SettingModel;
use Loaf\Settings\Models\SimpleSetting;

class IntegerSettingType extends BaseSettingType
{
    public function makeModel(): SettingModel
    {
        return new SimpleSetting( $this->field );
    }
}