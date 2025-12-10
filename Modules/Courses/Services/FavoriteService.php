<?php

namespace Modules\Courses\Services;

use Modules\Courses\Models\Favorite;
use Modules\Courses\Models\Course;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    /**
     * Toggle favorite status for a course
     *
     * @param Course $course
     * @return array
     */
    public function toggleFavorite(Course $course): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [
                'success' => false, 
                'message' => __('messages.favorites.must_login'),
                'redirect' => route('login')
            ];
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return ['success' => true, 'is_favorited' => false, 'message' => __('messages.favorites.removed_successfully')];
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            return ['success' => true, 'is_favorited' => true, 'message' => __('messages.favorites.added_successfully')];
        }
    }

    /**
     * Check if course is favorited by user
     *
     * @param Course $course
     * @return bool
     */
    public function isFavorited(Course $course): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return Favorite::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Get user's favorite courses
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserFavorites(int $perPage = 12)
    {
        $user = Auth::user();
        
        return Course::whereHas('favorites', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->latest() // Removed 'instructor' and 'category' relations
        ->paginate($perPage);
    }
}

