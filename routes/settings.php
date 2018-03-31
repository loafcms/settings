<?php

Route::group([
    'prefix'        => config('admin.route_prefix'),
    'middleware'    => ['admin', 'auth:loaf'],
    'namespace'     => '\Loaf\Settings\Http\Controllers',
    'as'            => 'admin.',
], function () {

    Route::get('settings/{section}/edit', 'SettingsController@editSection')->name('settings.editSection');
    Route::post('settings/{section}', 'SettingsController@updateSection')->name('settings.updateSection');
});