<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $role_editor = Role::firstOrCreate([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);
        $role_client = Role::firstOrCreate([
            'name' => 'client',
            'guard_name' => 'web',
        ]);

        $permissions_create_role = Permission::firstOrCreate(['name' => 'create roles', 'guard_name' => 'web']);
        $permissions_read_role = Permission::firstOrCreate(['name' => 'read roles', 'guard_name' => 'web']);
        $permissions_edit_role = Permission::firstOrCreate(['name' => 'edit roles', 'guard_name' => 'web']);
        $permissions_delete_role = Permission::firstOrCreate(['name' => 'delete roles', 'guard_name' => 'web']);
        $permissions_update_role = Permission::firstOrCreate(['name' => 'update roles', 'guard_name' => 'web']);

        $permissions_create_lesson = Permission::firstOrCreate(['name' => 'create lessons', 'guard_name' => 'web']);
        $permissions_read_lesson = Permission::firstOrCreate(['name' => 'read lessons', 'guard_name' => 'web']);
        $permissions_edit_lesson = Permission::firstOrCreate(['name' => 'edit lessons', 'guard_name' => 'web']);
        $permissions_delete_lesson = Permission::firstOrCreate(['name' => 'delete lessons', 'guard_name' => 'web']);
        $permissions_update_lesson = Permission::firstOrCreate(['name' => 'update lessons', 'guard_name' => 'web']);

        $permissions_create_category = Permission::firstOrCreate(['name' => 'create categories', 'guard_name' => 'web']);
        $permissions_read_category = Permission::firstOrCreate(['name' => 'read categories', 'guard_name' => 'web']);
        $permissions_edit_category = Permission::firstOrCreate(['name' => 'edit categories', 'guard_name' => 'web']);
        $permissions_delete_category = Permission::firstOrCreate(['name' => 'delete categories', 'guard_name' => 'web']);
        $permissions_update_category = Permission::firstOrCreate(['name' => 'update categories', 'guard_name' => 'web']);

        $permissions_create_user = Permission::firstOrCreate(['name' => 'create users', 'guard_name' => 'web']);
        $permissions_read_user = Permission::firstOrCreate(['name' => 'read users', 'guard_name' => 'web']);
        $permissions_edit_user = Permission::firstOrCreate(['name' => 'edit users', 'guard_name' => 'web']);
        $permissions_delete_user = Permission::firstOrCreate(['name' => 'delete users', 'guard_name' => 'web']);
        $permissions_update_user = Permission::firstOrCreate(['name' => 'update users', 'guard_name' => 'web']);

        $permissions_admin = [
            $permissions_create_role,
            $permissions_read_role,
            $permissions_edit_role,
            $permissions_delete_role,
            $permissions_update_role,
            $permissions_create_lesson,
            $permissions_read_lesson,
            $permissions_edit_lesson,
            $permissions_delete_lesson,
            $permissions_update_lesson,
            $permissions_create_category,
            $permissions_read_category,
            $permissions_edit_category,
            $permissions_delete_category,
            $permissions_update_category,
            $permissions_create_user,
            $permissions_read_user,
            $permissions_edit_user,
            $permissions_delete_user,
            $permissions_update_user,
        ];

        $permissions_editor = [
            $permissions_create_lesson,
            $permissions_read_lesson,
            $permissions_edit_lesson,
            $permissions_update_lesson,
            $permissions_create_category,
            $permissions_read_category,
            $permissions_edit_category,
            $permissions_delete_category,
            $permissions_update_category,
        ];

        $permissions_client = [
            $permissions_read_lesson,
            $permissions_read_category,
        ];

        $role_admin->syncPermissions($permissions_admin);
        $role_editor->syncPermissions($permissions_editor);
        $role_client->syncPermissions($permissions_client);
    }
}
