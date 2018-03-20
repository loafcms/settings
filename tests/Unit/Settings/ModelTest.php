<?php

namespace Loaf\Settings\Tests\Unit\Settings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Loaf\Base\Models\Setting;
use Loaf\Base\Tests\TestCase;

class ModelTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp()
    {
        parent::setUp();


    }

    public function testScope()
    {
        $path = 'some.path';

        // Setting with different scope
        factory( Setting::class )->create([
            'path' => $path,
            'scope' => '0-other-scope'
        ]);

        $setting = factory( Setting::class )->create([
            'path' => $path,
        ]);

        // Setting with different scope
        factory( Setting::class )->create([
            'path' => $path,
            'scope' => '1-other-scope'
        ]);

        $this->assertEquals(
            $setting->id,
            Setting::wherePath( $path )->first()->id
        );
    }

    public function testArraySetting()
    {
        $setting = factory( Setting::class )->create([
            'type' => 'array',
            'value' => $value = ['a' => 1, 'b' => 2]
        ]);

        $this->assertSame(
            $value,
            Setting::wherePath( $setting->path )->first()->value
        );
    }

    public function testStringSetting()
    {
        $setting = factory( Setting::class )->create([
            'type' => 'string',
            'value' => $value = 'some value'
        ]);

        $this->assertSame(
            $value,
            Setting::wherePath( $setting->path )->first()->value
        );
    }

    public function testIntegerSetting()
    {
        $setting = factory( Setting::class )->create([
            'type' => 'integer',
            'value' => $value = 123
        ]);

        $this->assertSame(
            $value,
            Setting::wherePath( $setting->path )->first()->value
        );
    }

    public function testDeleteSetting()
    {
        $setting = factory( Setting::class )->create();

        $this->assertEquals(
            $setting->id,
            Setting::wherePath( $setting->path )->first()->id
        );

        $setting->delete();

        $this->assertNull( Setting::wherePath( $setting->path )->first() );
    }

}
