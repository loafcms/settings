<?php

namespace Loaf\Settings\Tests;

use Loaf\Admin\Tests\TestCase as BaseTestCase;
use Loaf\Settings\SettingsFacade;
use Loaf\Settings\SettingsServiceProvider;

abstract class TestCase extends BaseTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->withFactories(realpath(dirname(__DIR__).'/vendor/loaf/base/src/database/factories'));
        $this->withFactories(realpath(dirname(__DIR__).'/database/factories'));
    }

    protected function getPackageProviders($app){
        return array_merge( parent::getPackageProviders($app), [
            SettingsServiceProvider::class,
        ] );
    }

    protected function getPackageAliases($app){
        return array_merge( parent::getPackageAliases($app), [
            'Settings' => SettingsFacade::class
        ]);
    }

}
