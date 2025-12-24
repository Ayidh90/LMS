<?php

namespace App\Services;

use App\Repositories\TrackRepository;
use App\Models\Track;
use App\Services\ImageService;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrackService
{
    public function __construct(
        private TrackRepository $repository,
        private ImageService $imageService
    ) {}

    public function getPaginatedTracks(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $relations = ['program', 'courses'];
        $tracks = $this->repository->getPaginated($perPage, $relations, $filters);

        foreach ($tracks->items() as $track) {
            $track->makeVisible(['slug', 'thumbnail_url', 'translated_name', 'translated_description', 'unique_students_count', 'total_students_count']);
            $track->thumbnail = $track->thumbnail_url;
            // Ensure program and courses are loaded
            if (!$track->relationLoaded('program')) {
                $track->load('program');
            }
            if (!$track->relationLoaded('courses')) {
                $track->load('courses');
            }
            
            // Update courses students_count to use unique count
            if ($track->relationLoaded('courses')) {
                foreach ($track->courses as $course) {
                    $course->makeVisible(['unique_students_count']);
                    // Always use unique_students_count for accurate count
                    $uniqueCount = $course->getUniqueStudentsCountAttribute();
                    $course->students_count = $uniqueCount;
                    $course->unique_students_count = $uniqueCount;
                }
            }
        }

        return $tracks;
    }

    public function getTrackById(int $id): ?Track
    {
        $track = $this->repository->findById($id, ['program', 'courses']);
        
        if ($track) {
            $track->makeVisible(['slug', 'thumbnail_url', 'translated_name', 'translated_description', 'unique_students_count', 'total_students_count']);
            $track->thumbnail = $track->thumbnail_url;
            
            // Load courses with their unique students count
            if ($track->relationLoaded('courses')) {
                foreach ($track->courses as $course) {
                    $course->makeVisible(['unique_students_count']);
                    // Always use unique_students_count for accurate count
                    $uniqueCount = $course->getUniqueStudentsCountAttribute();
                    $course->students_count = $uniqueCount;
                    $course->unique_students_count = $uniqueCount;
                }
            }
        }

        return $track;
    }

    public function getTrackBySlug(string $slug): ?Track
    {
        $track = $this->repository->findBySlug($slug, ['program', 'courses']);
        
        if ($track) {
            $track->thumbnail = $this->imageService->getUrl($track->thumbnail);
        }

        return $track;
    }

    public function getTracksByProgram(int $programId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->findByProgram($programId, ['courses']);
    }

    public function createTrack(array $data, ?UploadedFile $thumbnailFile = null): Track
    {
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($thumbnailFile) {
            $filename = ($data['slug'] ?? Str::slug($data['name'])) . '.' . $thumbnailFile->getClientOriginalExtension();
            $data['thumbnail'] = $this->imageService->upload($thumbnailFile, 'tracks', null, $filename);
        } elseif (isset($data['thumbnail_url']) && filter_var($data['thumbnail_url'], FILTER_VALIDATE_URL)) {
            $slug = $data['slug'] ?? Str::slug($data['name']);
            $filename = $slug . '.jpg';
            $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail_url'], 'tracks', $filename);
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

    public function updateTrack(Track $track, array $data, ?UploadedFile $thumbnailFile = null): bool
    {
        if ($thumbnailFile) {
            $slug = $track->slug;
            $filename = $slug . '.' . $thumbnailFile->getClientOriginalExtension();
            $data['thumbnail'] = $this->imageService->upload($thumbnailFile, 'tracks', $track->thumbnail, $filename);
        } elseif (isset($data['thumbnail_url']) && filter_var($data['thumbnail_url'], FILTER_VALIDATE_URL)) {
            $slug = $track->slug;
            $filename = $slug . '.jpg';
            $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail_url'], 'tracks', $filename, $track->thumbnail);
            unset($data['thumbnail_url']);
        } elseif (isset($data['thumbnail'])) {
            if (filter_var($data['thumbnail'], FILTER_VALIDATE_URL)) {
                $slug = $track->slug;
                $filename = $slug . '.jpg';
                $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail'], 'tracks', $filename, $track->thumbnail);
            } elseif (empty($data['thumbnail']) && $track->thumbnail) {
                $this->imageService->delete($track->thumbnail);
                $data['thumbnail'] = null;
            } else {
                unset($data['thumbnail']);
            }
        }

        if (isset($data['name']) && $data['name'] !== $track->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['name_ar']) && empty($data['name_ar'])) {
            $data['name_ar'] = null;
        }
        if (isset($data['description_ar']) && empty($data['description_ar'])) {
            $data['description_ar'] = null;
        }

        return $this->repository->update($track, $data);
    }

    public function deleteTrack(Track $track): bool
    {
        $this->imageService->delete($track->thumbnail);
        return $this->repository->delete($track);
    }
}

