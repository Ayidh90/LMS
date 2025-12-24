<?php

namespace App\Services;

use App\Repositories\ProgramRepository;
use App\Models\Program;
use App\Services\ImageService;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProgramService
{
    public function __construct(
        private ProgramRepository $repository,
        private ImageService $imageService
    ) {}

    public function getPaginatedPrograms(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $relations = ['tracks', 'tracks.courses'];
        $programs = $this->repository->getPaginated($perPage, $relations, $filters);

        foreach ($programs->items() as $program) {
            $program->makeVisible(['slug', 'thumbnail_url', 'translated_name', 'translated_description']);
            $program->thumbnail = $program->thumbnail_url;
            // Load tracks count
            if (!$program->relationLoaded('tracks')) {
                $program->load('tracks');
            }
        }

        return $programs;
    }

    public function getProgramById(int $id): ?Program
    {
        $program = $this->repository->findById($id, ['tracks.courses']);
        
        if ($program) {
            $program->makeVisible(['slug', 'thumbnail_url', 'translated_name', 'translated_description']);
            $program->thumbnail = $program->thumbnail_url;
        }

        return $program;
    }

    public function getProgramBySlug(string $slug): ?Program
    {
        $program = $this->repository->findBySlug($slug, ['tracks.courses']);
        
        if ($program) {
            $program->thumbnail = $this->imageService->getUrl($program->thumbnail);
        }

        return $program;
    }

    public function createProgram(array $data, ?UploadedFile $thumbnailFile = null): Program
    {
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($thumbnailFile) {
            $filename = ($data['slug'] ?? Str::slug($data['name'])) . '.' . $thumbnailFile->getClientOriginalExtension();
            $data['thumbnail'] = $this->imageService->upload($thumbnailFile, 'programs', null, $filename);
        } elseif (isset($data['thumbnail_url']) && filter_var($data['thumbnail_url'], FILTER_VALIDATE_URL)) {
            $slug = $data['slug'] ?? Str::slug($data['name']);
            $filename = $slug . '.jpg';
            $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail_url'], 'programs', $filename);
            unset($data['thumbnail_url']);
        } elseif (isset($data['thumbnail']) && empty($data['thumbnail'])) {
            $data['thumbnail'] = null;
        } else {
            unset($data['thumbnail']);
        }

        $data['name_ar'] = $data['name_ar'] ?? null;
        $data['description_ar'] = $data['description_ar'] ?? null;

        return $this->repository->create($data);
    }

    public function updateProgram(Program $program, array $data, ?UploadedFile $thumbnailFile = null): bool
    {
        if ($thumbnailFile) {
            $slug = $program->slug;
            $filename = $slug . '.' . $thumbnailFile->getClientOriginalExtension();
            $data['thumbnail'] = $this->imageService->upload($thumbnailFile, 'programs', $program->thumbnail, $filename);
        } elseif (isset($data['thumbnail_url']) && filter_var($data['thumbnail_url'], FILTER_VALIDATE_URL)) {
            $slug = $program->slug;
            $filename = $slug . '.jpg';
            $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail_url'], 'programs', $filename, $program->thumbnail);
            unset($data['thumbnail_url']);
        } elseif (isset($data['thumbnail'])) {
            if (filter_var($data['thumbnail'], FILTER_VALIDATE_URL)) {
                $slug = $program->slug;
                $filename = $slug . '.jpg';
                $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail'], 'programs', $filename, $program->thumbnail);
            } elseif (empty($data['thumbnail']) && $program->thumbnail) {
                $this->imageService->delete($program->thumbnail);
                $data['thumbnail'] = null;
            } else {
                unset($data['thumbnail']);
            }
        }

        if (isset($data['name']) && $data['name'] !== $program->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['name_ar']) && empty($data['name_ar'])) {
            $data['name_ar'] = null;
        }
        if (isset($data['description_ar']) && empty($data['description_ar'])) {
            $data['description_ar'] = null;
        }

        return $this->repository->update($program, $data);
    }

    public function deleteProgram(Program $program): bool
    {
        $this->imageService->delete($program->thumbnail);
        return $this->repository->delete($program);
    }
}

