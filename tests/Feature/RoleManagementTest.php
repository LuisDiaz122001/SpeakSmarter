<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_users_without_role_permissions_cannot_manage_roles(): void
    {
        $user = User::factory()->create();
        $role = Role::findByName('editor', 'web');

        $this->actingAs($user);

        $this->getJson(route('roles.index'))->assertForbidden();
        $this->getJson(route('roles.create'))->assertForbidden();
        $this->postJson(route('roles.store'), [
            'name' => 'teacher',
            'permissions' => ['read lessons'],
        ])->assertForbidden();
        $this->getJson(route('roles.edit', $role))->assertForbidden();
        $this->putJson(route('roles.update', $role), [
            'name' => 'teacher',
            'permissions' => ['read lessons'],
        ])->assertForbidden();
        $this->deleteJson(route('roles.destroy', $role))->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_roles(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin);

        $this->get(route('roles.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Roles/Index'));

        $this->get(route('roles.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Roles/Create'));

        $this->post(route('roles.store'), [
            'name' => 'teacher',
            'permissions' => ['create lessons', 'read lessons'],
        ])->assertRedirect(route('roles.index'));

        $role = Role::findByName('teacher', 'web');

        $this->assertEqualsCanonicalizing(
            ['create lessons', 'read lessons'],
            $role->permissions->pluck('name')->all(),
        );

        $this->get(route('roles.edit', $role))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Roles/Edit'));

        $this->put(route('roles.update', $role), [
            'name' => 'senior teacher',
            'permissions' => ['read lessons'],
        ])->assertRedirect(route('roles.index'));

        $updatedRole = Role::findByName('senior teacher', 'web');

        $this->assertEquals(['read lessons'], $updatedRole->permissions->pluck('name')->all());

        $this->delete(route('roles.destroy', $updatedRole))
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseMissing('roles', ['id' => $updatedRole->id]);
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
