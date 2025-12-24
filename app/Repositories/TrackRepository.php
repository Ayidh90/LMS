<?php

namespace App\Repositories;

use App\Models\Track;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrackRepository
{
    public function getAll(array $relations = []): Collection
    {
        $query = Track::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->orderBy('order')->latest()->get();
    }

    public function getPaginated(int $perPage = 12, array $relations = [], array $filters = []): LengthAwarePaginator
    {
        $query = Track::query();

        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

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

    public function findByProgram(int $programId, array $relations = []): Collection
    {
        $query = Track::where('program_id', $programId);

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->orderBy('order')->get();
    }

    public function findById(int $id, array $relations = []): ?Track
    {
        $query = Track::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    public function findBySlug(string $slug, array $relations = []): ?Track
    {
        $query = Track::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->where('slug', $slug)->first();
    }

    public function create(array $data): Track
    {
        return Track::create($data);
    }

    public function update(Track $track, array $data): bool
    {
        return $track->update($data);
    }

    public function delete(Track $track): bool
    {
        return $track->delete();
    }
}

