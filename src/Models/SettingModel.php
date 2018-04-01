<?php

namespace Loaf\Settings\Models;

use Loaf\Settings\Configuration\Field;

/**
 * Interface SettingModel.
 *
 * Database model interface
 */
interface SettingModel
{
    /**
     * SettingModel constructor.
     * Initialized with a Field config, or none.
     *
     * @param Field|null $field
     */
    public function __construct(Field $field = null);

    /**
     * Serialize the setting before storage.
     *
     * @param $value
     *
     * @return mixed
     */
    public function serialize($value);

    /**
     * Deserialize the setting from storage (inflate).
     *
     * @param $value
     *
     * @return mixed
     */
    public function deserialize($value);
}
