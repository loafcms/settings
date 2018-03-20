<?php

namespace Loaf\Settings\Http\Controllers;

use Loaf\Admin\Http\Controllers\Controller;
use Loaf\Base\Contracts\Menu\AdminMenu;
use Loaf\Base\Contracts\Settings\SettingsManager;

class SettingsController extends Controller
{
    /**
     * @var SettingsManager
     */
    protected $settings;

    public function __construct(AdminMenu $menu_manager, SettingsManager $settings )
    {
        parent::__construct($menu_manager);

        $this->settings = $settings;
    }

    public function group( string $group )
    {

    }
}
