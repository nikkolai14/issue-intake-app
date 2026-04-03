<?php

namespace App\Http\Controllers\Api\Issues;

use App\Http\Controllers\Controller;
use App\Http\Requests\Issues\CategoryStoreRequest;
use App\Http\Requests\Issues\CategoryUpdateRequest;
use App\Http\Resources\Issues\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    /**
     * Get all categories for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->getCategoriesForUser($request->user());

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Create a new category.
     */
    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategoryForUser($request->user(), $request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * Get a specific category.
     */
    public function show(Request $request, Category $category): JsonResponse
    {
        $this->authorize('view', $category);

        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Update a category.
     */
    public function update(CategoryUpdateRequest $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);

        $category = $this->categoryService->updateCategory($category, $request->validated());

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);

        $this->categoryService->deleteCategory($category);

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}
