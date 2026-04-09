<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_create_update_and_delete_users_with_roles_and_permissions(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin);

        $this->get(route('users.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Users/Index'));

        $this->get(route('users.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Users/Create'));

        $this->post(route('users.store'), [
            'first_name' => 'Laura',
            'last_name' => 'Mendez',
            'phone' => '3001112233',
            'email' => 'laura@speaksmarter.com',
            'password' => 'teacher1234',
            'password_confirmation' => 'teacher1234',
            'roles' => ['editor'],
            'permissions' => ['read users'],
        ])->assertRedirect(route('users.index'));

        $user = User::query()->firstWhere('email', 'laura@speaksmarter.com');

        $this->assertNotNull($user);
        $this->assertSame('Laura Mendez', $user->name);
        $this->assertSame('Laura', $user->first_name);
        $this->assertSame('Mendez', $user->last_name);
        $this->assertSame('3001112233', $user->phone);
        $this->assertTrue(Hash::check('teacher1234', $user->password));
        $this->assertTrue($user->hasRole('editor'));
        $this->assertTrue($user->hasDirectPermission('read users'));

        $this->get(route('users.edit', $user))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Users/Edit'));

        $this->put(route('users.update', $user), [
            'first_name' => 'Laura',
            'last_name' => 'Perez',
            'phone' => '3009998877',
            'email' => 'laura.perez@speaksmarter.com',
            'password' => '',
            'password_confirmation' => '',
            'roles' => ['client'],
            'permissions' => ['read roles'],
        ])->assertRedirect(route('users.index'));

        $user->refresh();

        $this->assertSame('Laura Perez', $user->name);
        $this->assertSame('Perez', $user->last_name);
        $this->assertSame('3009998877', $user->phone);
        $this->assertSame('laura.perez@speaksmarter.com', $user->email);
        $this->assertTrue($user->hasRole('client'));
        $this->assertFalse($user->hasRole('editor'));
        $this->assertTrue($user->hasDirectPermission('read roles'));

        $this->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_users_without_user_permissions_cannot_manage_users(): void
    {
        $editor = $this->createUserWithRole('editor');
        $managedUser = User::factory()->create();

        $this->actingAs($editor);

        $payload = [
            'first_name' => 'Nuevo',
            'last_name' => 'Usuario',
            'phone' => '3000000000',
            'email' => 'nuevo@speaksmarter.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'roles' => ['client'],
            'permissions' => [],
        ];

        $this->getJson(route('users.index'))->assertForbidden();
        $this->getJson(route('users.create'))->assertForbidden();
        $this->postJson(route('users.store'), $payload)->assertForbidden();
        $this->getJson(route('users.edit', $managedUser))->assertForbidden();
        $this->putJson(route('users.update', $managedUser), $payload)->assertForbidden();
        $this->deleteJson(route('users.destroy', $managedUser))->assertForbidden();
    }

    public function test_admin_cannot_delete_own_account_from_user_module(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin);

        $this->delete(route('users.destroy', $admin))->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
