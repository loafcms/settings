<?php

namespace Loaf\Settings\Models;

class BooleanSetting extends BaseSetting {

    /**
     * Convert the value to a bool
     * 
     * @inheritdoc
     */
    public function serialize( $value )
    {
        return (bool) $value;
    }

    public function editView()
    {
        return view('loaf/settings::types/boolean/edit', ['field' => $this->field, 'model' => $this]);
    }

}