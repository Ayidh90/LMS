<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        private CategoryRepository $repository
    ) {}

    /**
     * Get paginated categories
     */
    public function getPaginated(int $perPage = 15)
    {
        return $this->repository->getPaginated($perPage);
    }

    /**
     * Get all active categories
     */
    public function getAllActive()
    {
        return $this->repository->getAllActive();
    }

    /**
     * Get category by ID
     */
    public function getCategoryById(int $id): ?Category
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data): Category
    {
        // Generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Set default order if not provided
        if (!isset($data['order'])) {
            $maxOrder = Category::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
        }

        // Set is_active default to true
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        return $this->repository->create($data);
    }

    /**
     * Update category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        // Generate slug if name changed and slug not provided
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $this->repository->update($category, $data);
        
        return $category->fresh();
    }

    /**
     * Delete category
     */
    public function deleteCategory(Category $category): bool
    {
        // Check if category has courses
        if ($category->courses()->count() > 0) {
            throw new \Exception('Cannot delete category with associated courses');
        }

        return $this->repository->delete($category);
    }
}

