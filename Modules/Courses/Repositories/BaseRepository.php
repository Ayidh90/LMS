<?php

namespace Modules\Courses\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll(array $relations = []): Collection
    {
        return $this->model->with($relations)->latest()->get();
    }

    public function getPaginated(int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->latest()->paginate($perPage);
    }

    public function findById(int $id, array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    public function findByIdOrFail(int $id, array $relations = []): Model
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function count(array $filters = []): int
    {
        $query = $this->model->newQuery();
        
        foreach ($filters as $field => $value) {
            $query->where($field, $value);
        }
        
        return $query->count();
    }
}

