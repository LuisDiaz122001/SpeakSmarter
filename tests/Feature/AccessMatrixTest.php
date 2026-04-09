<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\User;
use Database\Seeders\LevelSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AccessMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleSeeder::class,
            LevelSeeder::class,
        ]);
    }

    public function test_admin_can_access_all_management_areas(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Categories/Index')
                ->where('user.roles.0', 'admin')
                ->has('user.permissions', 15));

        $this->get(route('lessons.index'))->assertOk();
        $this->get(route('roles.index'))->assertOk();
        $this->get(route('categories.create'))->assertOk();
        $this->get(route('lessons.create'))->assertOk();
        $this->get(route('roles.create'))->assertOk();
    }

    public function test_editor_can_manage_content_but_cannot_access_roles_or_delete_lessons(): void
    {
        $editor = $this->createUserWithRole('editor');
        $lesson = $this->createLesson();

        $this->actingAs($editor);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Categories/Index')
                ->where('user.roles.0', 'editor'));

        $this->get(route('lessons.index'))->assertOk();
        $this->getJson(route('roles.index'))->assertForbidden();
        $this->getJson(route('roles.create'))->assertForbidden();
        $this->deleteJson(route('lessons.destroy', $lesson))->assertForbidden();
    }

    public function test_client_has_read_only_access_to_lessons_and_categories(): void
    {
        $client = $this->createUserWithRole('client');
        $category = Category::create(['name' => 'Listening']);
        $lesson = $this->createLesson();

        $this->actingAs($client);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Categories/Index')
                ->where('user.roles.0', 'client')
                ->has('user.permissions', 2));

        $this->get(route('lessons.index'))->assertOk();
        $this->getJson(route('categories.create'))->assertForbidden();
        $this->postJson(route('categories.store'), ['name' => 'Grammar'])->assertForbidden();
        $this->getJson(route('lessons.create'))->assertForbidden();
        $this->putJson(route('lessons.update', $lesson), [
            'name' => 'Edited lesson',
            'description' => 'Restricted update.',
            'image_uri' => '',
            'content_uri' => '',
            'pdf_uri' => '',
            'is_free' => false,
            'level_id' => Level::query()->firstOrFail()->id,
            'category_ids' => [$category->id],
        ])->assertForbidden();
        $this->getJson(route('roles.index'))->assertForbidden();
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function createLesson(): Lesson
    {
        return Lesson::create([
            'name' => 'Starter Lesson',
            'description' => 'A lesson used to validate the access matrix.',
            'is_free' => false,
            'level_id' => Level::query()->firstOrFail()->id,
        ]);
    }
}
