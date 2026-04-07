<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        /* * Roles
        */
        $role_admin = Role::create(['name' => 'admin']);
        $role_editor = Role::create(['name' => 'editor']);

        /* * Permissions for Role
        */
        $permissions_create_role = Permission::create(['name' => 'create roles']);
        $permissions_read_role = Permission::create(['name' => 'read roles']);
        $permissions_edit_role = Permission::create(['name' => 'edit roles']);
        $permissions_delete_role = Permission::create(['name' => 'delete roles']);
        $permissions_update_role = Permission::create(['name' => 'update roles']);

        /* * Permissions for Lessons
        */
        $permissions_create_lesson = Permission::create(['name' => 'create lessons']);
        $permissions_read_lesson = Permission::create(['name' => 'read lessons']);
        $permissions_edit_lesson = Permission::create(['name' => 'edit lessons']);
        $permissions_delete_lesson = Permission::create(['name' => 'delete lessons']);
        $permissions_update_lesson = Permission::create(['name' => 'update lessons']);

        /* * Permissions for Category
        */
        $permissions_create_category = Permission::create(['name' => 'create categories']);
        $permissions_read_category = Permission::create(['name' => 'read categories']);
        $permissions_edit_category = Permission::create(['name' => 'edit categories']);
        $permissions_delete_category = Permission::create(['name' => 'delete categories']);
        $permissions_update_category = Permission::create(['name' => 'update categories']);

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
            $permissions_update_category
        ];

        $permissions_editor = [
            $permissions_create_lesson,
            $permissions_read_lesson,
            $permissions_edit_lesson,
            $permissions_create_category,
            $permissions_read_category,
            $permissions_edit_category,
            $permissions_delete_category,
            $permissions_update_category
        ];

        /* * Assign Permissions to Role
        */
        $role_admin->syncPermissions($permissions_admin);
        $role_editor->syncPermissions($permissions_editor);

    }
}