<?php

namespace Loaf\Pages\Database\Seeds;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create permissions
        $permissions = [

            'index page',
            'create page',
            'update page',
            'delete page',

            'index element',
            'create element',
            'update element',
            'delete element',

        ];

        foreach( $permissions as $permission ) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'loaf']);
        }

        // create roles
        $page_admin_roles = [
            'index page',
            'create page',
            'update page',
            'delete page',

            'index element',
            'create element',
            'update element',
            'delete element',

        ];
        $page_super_admin_roles = array_merge( $page_admin_roles, [ ] );
        $roles = [
            'super-admin' => $page_super_admin_roles,
            'admin' => $page_admin_roles,
            'page-admin' => $page_admin_roles,
        ];

        foreach( $roles as $role => $permissions ) {
            $role = Role::firstOrCreate(['name'=>$role, 'guard_name'=>'loaf']);
            foreach( $permissions as $permission ) {
                if( !$role->hasPermissionTo( $permission) ){
                    $role->givePermissionTo( $permission );
                }
            }
        }
    }
}