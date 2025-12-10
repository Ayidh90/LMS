<?php

namespace Modules\Courses\Controllers;

use App\Http\Controllers\Controller;
use Modules\Courses\Models\Course;
use Modules\Courses\Services\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    public function __construct(
        private FavoriteService $favoriteService
    ) {}

    /**
     * Toggle favorite status
     */
    public function toggle(Request $request, Course $course)
    {
        $result = $this->favoriteService->toggleFavorite($course);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        // Return back with updated favorite status for Inertia
        return back()->with([
            'message' => $result['message'],
            'isFavorited' => $result['is_favorited'],
        ]);
    }

    /**
     * Get user's favorite courses
     */
    public function index()
    {
        $favorites = $this->favoriteService->getUserFavorites(12);

        // Process courses for frontend - transform each item
        foreach ($favorites->items() as $course) {
            $course->makeVisible(['thumbnail_url', 'translated_title', 'translated_description']);
            $course->thumbnail = $course->thumbnail_url;
        }

        return Inertia::render('Courses/Favorites', [
            'favorites' => $favorites,
        ]);
    }
}

