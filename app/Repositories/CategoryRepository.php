<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * Get paginated categories
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Category::withCount('courses')
            ->orderBy('order')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get all active categories
     */
    public function getAllActive(): Collection
    {
        return Category::where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get category by ID
     */
    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * Get category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    /**
     * Create a new category
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update category
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Delete category
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}

