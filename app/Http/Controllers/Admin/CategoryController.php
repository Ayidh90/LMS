<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $categories = $this->categoryService->getPaginated(15);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return Inertia::render('Admin/Categories/Create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = $this->categoryService->createCategory($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', __('categories.created_successfully'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(int $id)
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            abort(404);
        }

        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, int $id)
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug,' . $category->id],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->categoryService->updateCategory($category, $validated);

        return redirect()->route('admin.categories.index')
            ->with('success', __('categories.updated_successfully'));
    }

    /**
     * Remove the specified category
     */
    public function destroy(int $id)
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            abort(404);
        }

        try {
            $this->categoryService->deleteCategory($category);
            
            return redirect()->route('admin.categories.index')
                ->with('success', __('categories.deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', $e->getMessage());
        }
    }
}

