<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all categories for a user.
     */
    public function getCategoriesForUser(User $user): Collection
    {
        return $user->categories()->latest()->get();
    }

    /**
     * Create a new category for a user.
     */
    public function createCategoryForUser(User $user, array $data): Category
    {
        return $user->categories()->create($data);
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh();
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }
}
