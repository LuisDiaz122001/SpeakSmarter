<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->middleware('can:read categories')
        ->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])
        ->middleware('can:create categories')
        ->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])
        ->middleware('can:create categories')
        ->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->middleware('can:edit categories')
        ->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->middleware('can:update categories')
        ->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('can:delete categories')
        ->name('categories.destroy');

    Route::get('/lessons', [LessonController::class, 'index'])
        ->middleware('can:read lessons')
        ->name('lessons.index');
    Route::get('/lessons/create', [LessonController::class, 'create'])
        ->middleware('can:create lessons')
        ->name('lessons.create');
    Route::post('/lessons', [LessonController::class, 'store'])
        ->middleware('can:create lessons')
        ->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])
        ->middleware('can:edit lessons')
        ->name('lessons.edit');
    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])
        ->middleware('can:update lessons')
        ->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])
        ->middleware('can:delete lessons')
        ->name('lessons.destroy');

    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('can:read roles')
        ->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('can:create roles')
        ->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('can:create roles')
        ->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('can:edit roles')
        ->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('can:update roles')
        ->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('can:delete roles')
        ->name('roles.destroy');

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('can:read users')
        ->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('can:create users')
        ->name('users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('can:create users')
        ->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('can:edit users')
        ->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('can:update users')
        ->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('can:delete users')
        ->name('users.destroy');
});
