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

class LessonManagementTest extends TestCase
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

    public function test_users_without_lesson_permissions_cannot_manage_lessons(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Grammar']);
        $lesson = Lesson::create([
            'name' => 'Past Perfect',
            'description' => 'Practice the past perfect.',
            'is_free' => false,
            'price' => 49.00,
            'level_id' => Level::query()->firstOrFail()->id,
        ]);

        $lesson->categories()->sync([$category->id]);

        $this->actingAs($user);

        $payload = [
            'name' => 'Reported Speech',
            'description' => 'Turn direct speech into reported speech.',
            'image_uri' => '',
            'content_uri' => 'https://example.com/reported-speech',
            'pdf_uri' => 'https://example.com/reported-speech.pdf',
            'is_free' => true,
            'price' => '',
            'level_id' => Level::query()->firstOrFail()->id,
            'category_ids' => [$category->id],
        ];

        $this->getJson(route('lessons.index'))->assertForbidden();
        $this->getJson(route('lessons.create'))->assertForbidden();
        $this->postJson(route('lessons.store'), $payload)->assertForbidden();
        $this->getJson(route('lessons.edit', $lesson))->assertForbidden();
        $this->putJson(route('lessons.update', $lesson), $payload)->assertForbidden();
        $this->deleteJson(route('lessons.destroy', $lesson))->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_lessons_with_categories(): void
    {
        $admin = $this->createUserWithRole('admin');
        $level = Level::query()->firstOrFail();
        $grammar = Category::create(['name' => 'Grammar']);
        $listening = Category::create(['name' => 'Listening']);

        $this->actingAs($admin);

        $this->get(route('lessons.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Lessons/Index'));

        $this->get(route('lessons.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Lessons/Create'));

        $this->post(route('lessons.store'), [
            'name' => 'Past Perfect',
            'description' => 'Practice the past perfect.',
            'image_uri' => '',
            'content_uri' => 'https://example.com/past-perfect',
            'pdf_uri' => 'https://example.com/past-perfect.pdf',
            'is_free' => false,
            'price' => 39.90,
            'level_id' => $level->id,
            'category_ids' => [$grammar->id, $listening->id],
        ])->assertRedirect(route('lessons.index'));

        $lesson = Lesson::query()->firstWhere('name', 'Past Perfect');

        $this->assertNotNull($lesson);
        $this->assertSame('39.90', $lesson->price);
        $this->assertDatabaseHas('category_lesson', [
            'lesson_id' => $lesson->id,
            'category_id' => $grammar->id,
        ]);
        $this->assertDatabaseHas('category_lesson', [
            'lesson_id' => $lesson->id,
            'category_id' => $listening->id,
        ]);

        $this->get(route('lessons.edit', $lesson))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Lessons/Edit'));

        $this->put(route('lessons.update', $lesson), [
            'name' => 'Reported Speech',
            'description' => 'Turn direct speech into reported speech.',
            'image_uri' => 'https://example.com/reported-speech.png',
            'content_uri' => 'https://example.com/reported-speech',
            'pdf_uri' => 'https://example.com/reported-speech.pdf',
            'is_free' => true,
            'price' => '',
            'level_id' => $level->id,
            'category_ids' => [$listening->id],
        ])->assertRedirect(route('lessons.index'));

        $lesson->refresh();

        $this->assertSame('Reported Speech', $lesson->name);
        $this->assertTrue((bool) $lesson->is_free);
        $this->assertNull($lesson->price);
        $this->assertDatabaseMissing('category_lesson', [
            'lesson_id' => $lesson->id,
            'category_id' => $grammar->id,
        ]);
        $this->assertDatabaseHas('category_lesson', [
            'lesson_id' => $lesson->id,
            'category_id' => $listening->id,
        ]);

        $this->delete(route('lessons.destroy', $lesson))
            ->assertRedirect(route('lessons.index'));

        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
