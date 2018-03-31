<?php

namespace Loaf\Settings\Models;

class BooleanSetting extends BaseSetting
{
    /**
     * Convert the value to a bool
     * 
     * @inheritdoc
     */
    public function serialize( $value )
    {
        return (bool) $value;
    }
}