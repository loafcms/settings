<?php

namespace Loaf\Settings\Tests;

use Loaf\Admin\Tests\TestCase as BaseTestCase;
use Loaf\Settings\SettingsFacade;
use Loaf\Settings\SettingsManager;
use Loaf\Settings\SettingsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->withFactories(realpath(dirname(__DIR__).'/vendor/loaf/base/src/database/factories'));
        $this->withFactories(realpath(dirname(__DIR__).'/database/factories'));
    }

    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            SettingsServiceProvider::class,
        ]);
    }

    protected function getPackageAliases($app)
    {
        return array_merge(parent::getPackageAliases($app), [
            'Settings' => SettingsFacade::class,
        ]);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->resolving(SettingsManager::class, function (SettingsManager $manager) {
            $manager->mergeConfig($this->getTestCaseSettingsConfig(), true);
        });
    }

    protected function getTestCaseSettingsConfig()
    {
        return [
            'section' => [
                'groups' => [
                    'group' => [
                        'label'  => 'a prototypical group',
                        'fields' => [
                            'boolean' => [
                                'label' => 'a two state switch',
                                'type'  => 'boolean',
                            ],
                            'string' => [
                                'label' => 'expressed in characters',
                                'type'  => 'string',
                            ],
                            'integer' => [
                                'label' => 'something countable',
                                'type'  => 'integer',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
