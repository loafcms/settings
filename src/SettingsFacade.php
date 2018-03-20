<?php

namespace Loaf\Settings;

use Illuminate\Support\Facades\Facade;
use Loaf\Base\Settings\SettingsManager;

class SettingsFacade extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return SettingsManager::class; }
}