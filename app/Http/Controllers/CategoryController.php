<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    private const ITEMS_PER_PAGE = 25;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query()
            ->withCount('lessons')
            ->orderBy('name')
            ->paginate(self::ITEMS_PER_PAGE);

        return inertia('Categories/Index', [
            'categories' => $categories->through(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'lessons_count' => $category->lessons_count,
                'updated_at' => $category->updated_at->diffForHumans(),
            ]),
            'overview' => [
                'total' => Category::count(),
                'with_lessons' => Category::query()->has('lessons')->count(),
                'empty' => Category::query()->doesntHave('lessons')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CategoryRequest  $request
     * @return Response
     */
    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return inertia('Categories/Edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index');
    }
}
