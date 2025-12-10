<?php

namespace App\Http\Controllers;

// use App\Models\Category; // Commented out - categories removed
use App\Models\FAQ;
use Modules\Courses\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Categories removed from system
        // $categories = Category::where('is_active', true)
        //     ->withCount('courses')
        //     ->orderBy('order')
        //     ->get()
        //     ->map(function ($category) {
        //         return [
        //             'id' => $category->id,
        //             'name' => $category->name,
        //             'name_ar' => $category->name_ar,
        //             'translated_name' => $category->translated_name,
        //             'slug' => $category->slug,
        //             'icon' => $category->icon,
        //             'courses_count' => $category->courses_count,
        //         ];
        //     });
        $categories = [];

        $courses = Course::where('is_published', true)
            // ->with(['instructor', 'category']) // Removed instructor and category
            ->latest()
            ->take(12)
            ->get()
            ->map(function ($course) {
                // Use thumbnail_url accessor which handles URL conversion
                return [
                    'id' => $course->id,
                    'slug' => $course->slug,
                    'title' => $course->title,
                    'title_ar' => $course->title_ar,
                    'translated_title' => $course->translated_title,
                    'description' => $course->description,
                    'description_ar' => $course->description_ar,
                    'translated_description' => $course->translated_description,
                    'thumbnail' => $course->thumbnail_url,
                    'thumbnail_url' => $course->thumbnail_url,
                    'level' => $course->level,
                    'price' => $course->price,
                    // 'instructor' => $course->instructor ? [
                    //     'id' => $course->instructor->id,
                    //     'name' => $course->instructor->name,
                    // ] : null, // Removed - instructors are on batches
                    // 'category' => $course->category ? [
                    //     'id' => $course->category->id,
                    //     'name' => $course->category->name,
                    //     'name_ar' => $course->category->name_ar,
                    //     'translated_name' => $course->category->translated_name,
                    // ] : null, // Removed - categories removed
                ];
            });

        $faqs = FAQ::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'question_ar' => $faq->question_ar,
                    'translated_question' => $faq->translated_question,
                    'answer' => $faq->answer,
                    'answer_ar' => $faq->answer_ar,
                    'translated_answer' => $faq->translated_answer,
                ];
            });

        $user = Auth::user();
        $enrolledCourseIds = $user ? $user->enrolledCourses()->pluck('courses.id')->toArray() : [];

        return Inertia::render('Welcome', [
            'categories' => $categories,
            'courses' => $courses,
            'faqs' => $faqs,
            'enrolledCourseIds' => $enrolledCourseIds,
        ]);
    }
}
