<?php

namespace Loaf\Settings\Tests\Feature\SettingsController;

use Loaf\Base\Models\User;

class AuthorizationTest extends SettingsControllerTestBase
{
    public function testUnauthorized()
    {
        $unauthorized_user = factory( User::class )->create();

        $this->actingAs($unauthorized_user, 'loaf')
            ->get( route('admin.settings.editSection', ['section'=>'section']) )
            ->assertStatus( 302 );

        $this->actingAs($unauthorized_user, 'loaf')
            ->post( route('admin.settings.updateSection', ['section'=>'section']) )
            ->assertStatus( 302 );
    }

    public function testAuthorized()
    {
        $this->actingAs($this->user, 'loaf')
            ->followingRedirects()
            ->get( route('admin.settings.editSection', ['section'=>'section']) )
            ->assertSuccessful()
            ->assertViewIs('loaf/settings::settings')
            ->assertSee($this->test_config['section.groups.group.label'])
            ->assertSee($this->test_config['section.groups.group.fields.boolean.label'])
            ->assertSee($this->test_config['section.groups.group.fields.string.label'])
            ->assertSee($this->test_config['section.groups.group.fields.integer.label']);
    }
}