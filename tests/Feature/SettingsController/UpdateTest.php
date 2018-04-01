<?php

namespace Loaf\Settings\Tests\Feature\SettingsController;

use Settings;

class UpdateTest extends SettingsControllerTestBase
{
    public function testUpdateSection()
    {
        $post_data = [];
        $type = $this->getSettingTypeForField('section.group.string');
        array_set($post_data, $type->getFormKey('_value'), 'updated');
        $type = $this->getSettingTypeForField('section.group.integer');
        array_set($post_data, $type->getFormKey('_value'), 200);

        $this->actingAs($this->user, 'loaf')
            ->post(route('admin.settings.updateSection', ['section'=>'section']), $post_data)
            ->assertRedirect(route('admin.settings.editSection', ['section'=>'section']))
            ->assertSessionMissing('errors');

        $this->assertFalse(Settings::get('section.group.boolean'));
        $this->assertEquals('updated', Settings::get('section.group.string'));
        $this->assertEquals(200, Settings::get('section.group.integer'));
    }

    public function testValidation()
    {
        $original_value = Settings::get('section.group.integer');

        $post_data = [];
        $type = $this->getSettingTypeForField('section.group.integer');
        array_set($post_data, $type->getFormKey('_value'), 'invalid');

        $this->actingAs($this->user, 'loaf')
            ->post(route('admin.settings.updateSection', ['section'=>'section']), $post_data)
            ->assertRedirect()
            ->assertSessionHasErrors($type->getValidationKey());

        // Assert not changed
        $this->assertEquals($original_value, Settings::get('section.group.integer'));
    }
}
