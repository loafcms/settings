<?php

namespace Loaf\Settings\Database\Seeds;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create permissions
        $permissions = [
            'view settings section',
            'update settings section',
        ];

        $super_admin = Role::firstOrCreate(['name'=>'super-admin', 'guard_name'=>'loaf']);
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'loaf']);
            if (!$super_admin->hasPermissionTo($permission)) {
                $super_admin->givePermissionTo($permission);
            }
        }
    }
}
