<?php

Route::group([
    'prefix'        => config('admin.route_prefix'),
    'middleware'    => ['admin', 'auth:loaf'],
    'namespace'     => '\Loaf\Settings\Http\Controllers',
    'as'            => 'admin.',
], function () {

    Route::get('settings/{group}', 'SettingsController@group')->name('settings');

});