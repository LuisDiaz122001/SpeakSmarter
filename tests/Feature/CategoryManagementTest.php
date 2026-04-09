<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_users_without_category_permissions_cannot_manage_categories(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Grammar']);

        $this->actingAs($user);

        $this->getJson(route('categories.index'))->assertForbidden();
        $this->getJson(route('categories.create'))->assertForbidden();
        $this->postJson(route('categories.store'), ['name' => 'Listening'])->assertForbidden();
        $this->getJson(route('categories.edit', $category))->assertForbidden();
        $this->putJson(route('categories.update', $category), ['name' => 'Pronunciation'])->assertForbidden();
        $this->deleteJson(route('categories.destroy', $category))->assertForbidden();
    }

    public function test_editor_can_create_update_and_delete_categories(): void
    {
        $user = $this->createUserWithRole('editor');

        $this->actingAs($user);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Categories/Index'));

        $this->post(route('categories.store'), ['name' => 'Grammar'])
            ->assertRedirect(route('categories.index'));

        $category = Category::query()->firstWhere('name', 'Grammar');

        $this->assertNotNull($category);

        $this->get(route('categories.edit', $category))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Categories/Edit'));

        $this->put(route('categories.update', $category), ['name' => 'Pronunciation'])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Pronunciation',
        ]);

        $this->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
