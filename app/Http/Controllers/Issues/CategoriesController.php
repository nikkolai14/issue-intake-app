<?php

namespace App\Http\Controllers\Issues;

use App\Http\Controllers\Controller;
use App\Http\Requests\Issues\CategoryStoreRequest;
use App\Http\Requests\Issues\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoriesController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    /**
     * Display a listing of categories in a modal.
     */
    public function index(Request $request): Response
    {
        $categories = $this->categoryService->getCategoriesForUser($request->user())
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'created_at' => $category->created_at->diffForHumans(),
            ]);

        return Inertia::render('categories/index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $this->categoryService->createCategoryForUser($request->user(), $request->validated());

        return back();
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Request $request, Category $category): Response
    {
        $this->authorize('update', $category);

        return Inertia::render('categories/edit', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(CategoryUpdateRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $this->categoryService->updateCategory($category, $request->validated());

        return back();
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $this->categoryService->deleteCategory($category);

        return back();
    }
}
