<?php

namespace Loaf\Settings\Tests\Unit\Settings;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Loaf\Base\Contracts\Settings\SettingsException;
use Loaf\Base\Contracts\Settings\SettingsManager;
use Loaf\Settings\Tests\TestCase;

class ConfigTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var SettingsManager
     */
    protected $manager;

    /**
     * @var Repository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = $this->getRepository();
        $this->manager = $this->getManager($this->repository);
    }

    public function testMergeConfig()
    {
        $this->manager->mergeConfig($merge = $this->getMergeConfig(), true);

        // Check that the extra_field is present
        $this->assertEquals(
            $merge['general']['groups']['website']['fields']['extra_field'],
            $this->repository['general.groups.website.fields.extra_field']
        );

        // Check that the original website name is still present
        $this->assertTrue(
            $this->repository->has('general.groups.website.fields.website_name')
        );
    }

    public function testWontMerge()
    {
        $this->expectException(SettingsException::class);
        $this->manager->mergeConfig($merge = $this->getMergeConfig());
    }

    protected function getRepository() : Repository
    {
        return new Repository($this->getConfig());
    }

    protected function getManager(Repository $config) : SettingsManager
    {
        return app()->makeWith(\Loaf\Settings\SettingsManager::class, [
            'config' => $config,
        ]);
    }

    protected function getMergeConfig() : array
    {
        return [
            'general' => [
                'groups' => [
                    'website' => [
                        'fields' => [
                            'extra_field' => [
                                'label' => 'Website Name',
                                'type'  => 'string',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getConfig() : array
    {
        return [
            'general' => [
                'label'  => 'General',
                'order'  => 100,
                'groups' => [
                    'website' => [
                        'label'  => 'Website',
                        'order'  => 10,
                        'fields' => [
                            'website_name' => [
                                'label'   => 'Website Name',
                                'comment' => 'This is the name shown to the user',
                                'type'    => 'string',
                                'order'   => 10,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
