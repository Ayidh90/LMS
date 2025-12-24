<?php

namespace App\Repositories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProgramRepository
{
    public function getAll(array $relations = []): Collection
    {
        $query = Program::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->orderBy('order')->latest()->get();
    }

    public function getPaginated(int $perPage = 12, array $relations = [], array $filters = []): LengthAwarePaginator
    {
        $query = Program::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%");
            });
        }

        $sortBy = $filters['sort'] ?? 'order';
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'order':
            default:
                $query->orderBy('order')->latest();
                break;
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id, array $relations = []): ?Program
    {
        $query = Program::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    public function findBySlug(string $slug, array $relations = []): ?Program
    {
        $query = Program::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->where('slug', $slug)->first();
    }

    public function create(array $data): Program
    {
        return Program::create($data);
    }

    public function update(Program $program, array $data): bool
    {
        return $program->update($data);
    }

    public function delete(Program $program): bool
    {
        return $program->delete();
    }
}

