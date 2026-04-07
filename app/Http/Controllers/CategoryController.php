<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Response;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate(10);
        return inertia('Categories/Index', ['categories' => $categories]);
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
     * @param App\Http\Requests\CategoryRequest $request
      * @return \Illuminate\hHttp\Response
     */
    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());
        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return inertia('Categories/Edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     * @param App\Http\Requests\CategoryRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
