<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Lesson;
use App\Models\Level;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function dashboard()
    {
        $user = request()->user();
        $lessonCount = Lesson::count();
        $categoryCount = Category::count();
        $roleCount = Role::count();

        $checklist = [
            [
                'title' => 'Organize content with categories',
                'description' => 'Group lessons by topic so teachers can find the right material faster.',
                'complete' => $categoryCount > 0,
            ],
            [
                'title' => 'Build your lesson library',
                'description' => 'Keep reusable content ready for class instead of starting from scratch.',
                'complete' => $lessonCount > 0,
            ],
            [
                'title' => 'Assign learning levels',
                'description' => 'Use A1 to C2 levels to make progression easier to understand.',
                'complete' => $lessonCount > 0 && Lesson::whereNotNull('level_id')->count() === $lessonCount,
            ],
            [
                'title' => 'Keep access under control',
                'description' => 'Define who can read, create, edit and manage the platform.',
                'complete' => $roleCount > 1,
            ],
        ];

        $completedSteps = collect($checklist)->where('complete', true)->count();

        return Inertia::render('Dashboard', [
            'overview' => [
                'lessons' => $lessonCount,
                'categories' => $categoryCount,
                'levels' => Level::count(),
                'permissions' => $user?->getAllPermissions()->count() ?? 0,
                'completion' => (int) round(($completedSteps / max(count($checklist), 1)) * 100),
            ],
            'recentLessons' => Lesson::query()
                ->with(['level:id,name', 'categories:id,name'])
                ->latest()
                ->take(4)
                ->get()
                ->map(fn (Lesson $lesson) => [
                    'id' => $lesson->id,
                    'name' => $lesson->name,
                    'description' => Str::limit($lesson->description, 100),
                    'level' => $lesson->level?->name,
                    'categories' => $lesson->categories->pluck('name')->values()->all(),
                    'is_free' => (bool) $lesson->is_free,
                    'updated_at' => $lesson->updated_at->diffForHumans(),
                ]),
            'recentCategories' => Category::query()
                ->withCount('lessons')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'lessons_count' => $category->lessons_count,
                    'updated_at' => $category->updated_at->diffForHumans(),
                ]),
            'checklist' => $checklist,
        ]);
    }
}
