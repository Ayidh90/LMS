<?php

namespace App\Http\Controllers;

use Modules\Courses\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('messages.auth.must_login'),
                    'redirect' => route('login')
                ], 401);
            }
            return redirect()->route('login')->with('error', __('messages.auth.must_login'));
        }

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('student_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return back()->with('error', __('messages.courses.already_enrolled'));
        }

        // Create enrollment (no payment required)
        DB::transaction(function () use ($user, $course) {
            Enrollment::create([
                'student_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'enrolled',
                'progress' => 0,
                'enrolled_at' => now(),
            ]);

            // Update course students count
            $course->increment('students_count');
        });

        return back()->with('success', __('messages.courses.enrolled_successfully'));
    }

    public function destroy(Course $course)
    {
        $user = Auth::user();

        $enrollment = Enrollment::where('student_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            $enrollment->delete();
            $course->decrement('students_count');
            
            return back()->with('success', __('messages.courses.unenrolled_successfully'));
        }

        return back()->with('error', __('messages.courses.enrollment_not_found'));
    }
}
