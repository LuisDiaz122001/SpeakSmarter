<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\Level;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class LessonController extends Controller
{
    private const ITEMS_PER_PAGE = 25;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $lessons = Lesson::query()
            ->with(['categories:id,name', 'level:id,name'])
            ->orderBy('name')
            ->paginate(self::ITEMS_PER_PAGE)
            ->through(fn (Lesson $lesson) => $this->lessonPayload($lesson));

        return inertia('Lessons/Index', [
            'lessons' => $lessons,
            'overview' => [
                'total' => Lesson::count(),
                'free' => Lesson::query()->where('is_free', true)->count(),
                'with_level' => Lesson::query()->whereNotNull('level_id')->count(),
                'categorized' => Lesson::query()->has('categories')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);
        $levels = Level::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return inertia('Lessons/Create', [
            'categories' => $categories,
            'levels' => $levels,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $lesson = Lesson::create($this->lessonAttributes($validated));
            $lesson->categories()->sync($validated['category_ids'] ?? []);
        });

        return redirect()->route('lessons.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson): Response
    {
        $lesson->load(['categories:id,name', 'level:id,name']);

        return inertia('Lessons/Edit', [
            'lesson' => $this->lessonPayload($lesson),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'levels' => Level::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LessonRequest $request, Lesson $lesson): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $lesson): void {
            $lesson->update($this->lessonAttributes($validated));
            $lesson->categories()->sync($validated['category_ids'] ?? []);
        });

        return redirect()->route('lessons.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return redirect()->route('lessons.index');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function lessonAttributes(array $validated): array
    {
        return collect($validated)
            ->except('category_ids')
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function lessonPayload(Lesson $lesson): array
    {
        return [
            'id' => $lesson->id,
            'name' => $lesson->name,
            'description' => $lesson->description,
            'image_uri' => $lesson->image_uri,
            'content_uri' => $lesson->content_uri,
            'pdf_uri' => $lesson->pdf_uri,
            'is_free' => (bool) $lesson->is_free,
            'updated_at' => $lesson->updated_at->diffForHumans(),
            'level_id' => $lesson->level_id,
            'level' => $lesson->level
                ? [
                    'id' => $lesson->level->id,
                    'name' => $lesson->level->name,
                ]
                : null,
            'category_ids' => $lesson->categories->pluck('id')->values()->all(),
            'categories' => $lesson->categories
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                ])
                ->values()
                ->all(),
        ];
    }
}
