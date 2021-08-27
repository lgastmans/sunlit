<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create roles
        $role_users = Role::create(['name' => 'user']);
        $role_staff = Role::create(['name' => 'staff']);
        $role_admin = Role::create(['name' => 'admin']);

        // create permissions
        Permission::create(['name' => 'list taxes']);
        Permission::create(['name' => 'view taxes']);
        Permission::create(['name' => 'edit taxes']);
        Permission::create(['name' => 'delete taxes']);

        Permission::create(['name' => 'list categories']);
        Permission::create(['name' => 'view categories']);
        Permission::create(['name' => 'edit categories']);
        Permission::create(['name' => 'delete categories']);

        Permission::create(['name' => 'list suppliers']);
        Permission::create(['name' => 'view suppliers']);
        Permission::create(['name' => 'edit suppliers']);
        Permission::create(['name' => 'delete suppliers']);

        Permission::create(['name' => 'list products']);
        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'edit products']);
        Permission::create(['name' => 'delete products']);

        Permission::create(['name' => 'list dealers']);
        Permission::create(['name' => 'view dealers']);
        Permission::create(['name' => 'edit dealers']);
        Permission::create(['name' => 'delete dealers']);

        Permission::create(['name' => 'list warehouses']);
        Permission::create(['name' => 'view warehouses']);
        Permission::create(['name' => 'edit warehouses']);
        Permission::create(['name' => 'delete warehouses']);

        Permission::create(['name' => 'list users']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);


        Permission::create(['name' => 'list settings']);
        Permission::create(['name' => 'edit settings']);
        Permission::create(['name' => 'add settings']);
        Permission::create(['name' => 'delete settings']);
        

        // assign permissions to roles

        $role_users->givePermissionTo(['list taxes', 'view taxes']);
        $role_staff->givePermissionTo(['list taxes', 'view taxes', 'edit taxes', ]);
        $role_admin->givePermissionTo(['list taxes', 'view taxes', 'edit taxes', 'delete taxes']);

        $role_users->givePermissionTo(['list categories', 'view categories']);
        $role_staff->givePermissionTo(['list categories', 'view categories', 'edit categories', ]);
        $role_admin->givePermissionTo(['list categories', 'view categories', 'edit categories', 'delete categories']);

        $role_users->givePermissionTo(['list suppliers', 'view suppliers']);
        $role_staff->givePermissionTo(['list suppliers', 'view suppliers', 'edit suppliers', ]);
        $role_admin->givePermissionTo(['list suppliers', 'view suppliers', 'edit suppliers', 'delete suppliers']);

        $role_users->givePermissionTo(['list products', 'view products']);
        $role_staff->givePermissionTo(['list products', 'view products', 'edit products', ]);
        $role_admin->givePermissionTo(['list products', 'view products', 'edit products', 'delete products']);

        $role_users->givePermissionTo(['list dealers', 'view dealers']);
        $role_staff->givePermissionTo(['list dealers', 'view dealers', 'edit dealers', ]);
        $role_admin->givePermissionTo(['list dealers', 'view dealers', 'edit dealers', 'delete dealers']);

        $role_users->givePermissionTo(['list warehouses', 'view warehouses']);
        $role_staff->givePermissionTo(['list warehouses', 'view warehouses', 'edit warehouses', ]);
        $role_admin->givePermissionTo(['list warehouses', 'view warehouses', 'edit warehouses', 'delete warehouses']);

        $role_users->givePermissionTo(['list users', 'view users']);
        $role_staff->givePermissionTo(['list users', 'view users', 'edit users', ]);
        $role_admin->givePermissionTo(['list users', 'view users', 'edit users', 'delete users']);

        $role_staff->givePermissionTo(['list settings']);
        $role_admin->givePermissionTo(['list settings', 'edit settings']);

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

    }
}