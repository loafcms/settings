<?php

namespace Loaf\Settings\Types;

use Loaf\Settings\Models\BooleanSetting;
use Loaf\Settings\Models\SettingModel;

class BooleanSettingType extends BaseSettingType
{
    public function makeModel(): SettingModel
    {
        return new BooleanSetting( $this->field );
    }

    public function getEditView()
    {
        return view('loaf/settings::types/boolean/edit', [
            'field' => $this->getField(),
            'model' => $this->getModel()
        ]);
    }
}