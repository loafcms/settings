<?php

namespace Loaf\Settings\Tests\Unit\Settings;

use Loaf\Base\Contracts\Settings\SettingsException;
use Loaf\Settings\Configuration\Section;
use Loaf\Settings\Tests\TestCase;

class ConfigElementTest extends TestCase
{
    public function testConstructSectionElement()
    {
        $section = app()->makeWith( Section::class, [
            'config' => $this->getConfig()['general'],
            'key' => 'general'
        ]);

        $this->assertEquals( 'general', $section->getKey() );
        $this->assertEquals( 'website', $section->groups->first()->getKey() );
        $this->assertEquals( 'website_name', $section->groups->first()->fields->first()->getKey() );
    }

    public function testInvalidConfig()
    {
        $this->expectException( SettingsException::class );

        $config = $this->getConfig()['general'];

        unset( $config['groups']['website']['fields']['website_name']['label'] );

        app()->makeWith( Section::class, [
            'config' => $config,
            'key' => 'general'
        ]);
    }

    protected function getConfig() : array
    {
        return [
            'general' => [
                'label' => 'General',
                'order' => 100,
                'groups' => [
                    'website' => [
                        'label' => 'Website',
                        'order' => 10,
                        'fields' => [
                            'website_name' => [
                                'label' => 'Website Name',
                                'comment' => 'This is the name shown to the user',
                                'type' => 'string',
                                'order' => 10
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

}
