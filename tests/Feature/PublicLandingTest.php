<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Lesson;
use App\Models\Level;
use Database\Seeders\LevelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_landing_shows_catalog_data_and_pricing(): void
    {
        $this->seed(LevelSeeder::class);

        $category = Category::create(['name' => 'Speaking']);
        $level = Level::query()->firstOrFail();

        $paidLesson = Lesson::create([
            'name' => 'Interview Confidence',
            'description' => 'Prepare confident answers for real work interviews.',
            'is_free' => false,
            'price' => 29.90,
            'level_id' => $level->id,
        ]);
        $paidLesson->categories()->sync([$category->id]);

        $freeLesson = Lesson::create([
            'name' => 'Warm-up Pronunciation',
            'description' => 'A quick lesson to improve rhythm and clarity.',
            'is_free' => true,
            'price' => null,
            'level_id' => $level->id,
        ]);
        $freeLesson->categories()->sync([$category->id]);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Welcome')
                ->where('overview.lessons', 2)
                ->where('overview.categories', 1)
                ->where('overview.free_lessons', 1)
                ->where('overview.paid_lessons', 1)
                ->where('overview.starting_price', 29.9)
                ->has('featuredLessons', 2)
                ->has('featuredCategories', 1));
    }
}
