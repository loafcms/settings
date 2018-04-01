<?php

namespace Loaf\Settings\Tests\Feature\SettingsController;

use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Loaf\Base\Models\User;
use Loaf\Settings\SettingsManager;
use Loaf\Settings\Tests\TestCase;
use Loaf\Settings\Types\SettingType;

abstract class SettingsControllerTestBase extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array
     */
    protected $test_config;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var SettingsManager
     */
    protected $settings_manager;

    public function setUp()
    {
        parent::setUp();

        User::disableSearchSyncing();

        Artisan::call('db:seed', ['--class' => 'Loaf\Base\Database\Seeds\RolesAndPermissionsSeeder']);
        Artisan::call('db:seed', ['--class' => 'Loaf\Settings\Database\Seeds\RolesAndPermissionsSeeder']);

        $this->test_config = array_dot($this->getTestCaseSettingsConfig());

        $this->user = factory(User::class)->create();
        $this->user->assignRole('super-admin');

        $this->settings_manager = app(\Loaf\Base\Contracts\Settings\SettingsManager::class);

        $this->settings_manager->set('section.group.boolean', true);
        $this->settings_manager->set('section.group.string', 'initial');
        $this->settings_manager->set('section.group.integer', 100);
    }

    protected function getSettingTypeForField(string $path) : SettingType
    {
        return $this->settings_manager->getSettingType($this->settings_manager->getField($path));
    }
}
