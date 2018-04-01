<?php

namespace Loaf\Settings\Models;

class IntegerSetting extends BaseSetting
{
    /**
     * Convert the value to an integer
     * 
     * @inheritdoc
     */
    public function serialize( $value )
    {
        return (int) $value;
    }

    /**
     * Serialize the value to an integer
     *
     * @inheritdoc
     */
    public function deserialize($value)
    {
        return (int) $value;
    }
}