<?php

Route::group([
    'prefix'        => config('admin.route_prefix'),
    'middleware'    => ['admin', 'auth:loaf'],
    'namespace'     => '\Loaf\Settings\Http\Controllers',
    'as'            => 'admin.',
], function () {

    Route::get('settings/{section}', 'SettingsController@group')->name('settings');

});