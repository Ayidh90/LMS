<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * Get paginated users with filters
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('roles');

        // Search functionality
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filtering
        if (!empty($filters['role'])) {
            $roleSlug = $filters['role'];
            $query->where(function($q) use ($roleSlug) {
                $q->whereHas('roles', function($subQ) use ($roleSlug) {
                    $subQ->where('slug', $roleSlug)
                         ->orWhere('name', $roleSlug);
                })->orWhere('role', $roleSlug);
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        // Validate sort column
        $allowedSortColumns = ['name', 'email', 'created_at', 'is_active'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get user by ID with relations
     */
    public function findById(int $id, array $relations = []): ?User
    {
        $query = User::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->find($id);
    }

    /**
     * Get user by ID or fail
     */
    public function findByIdOrFail(int $id, array $relations = []): User
    {
        $query = User::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->findOrFail($id);
    }

    /**
     * Get user by email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update user
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Delete user
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Get all users with relations
     */
    public function getAll(array $relations = []): Collection
    {
        $query = User::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->latest()->get();
    }
}

