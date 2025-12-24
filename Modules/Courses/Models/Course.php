<?php

namespace Modules\Courses\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'track_id',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'slug',
        'thumbnail',
        'level',
        'course_type',
        'price',
        'is_published',
        'duration_hours',
        'students_count',
        'order',
    ];

    protected $appends = [
        'translated_title',
        'translated_description',
        'thumbnail_url',
        'unique_students_count',
    ];

    protected function casts(): array
    {
        return [
            'track_id' => 'integer',
            'price' => 'decimal:2',
            'is_published' => 'boolean',
            'duration_hours' => 'integer',
            'students_count' => 'integer',
            'order' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = static::generateUniqueSlug($course->title);
            }
        });

        static::updating(function ($course) {
            // Update slug if title changed and slug is not manually set
            if ($course->isDirty('title') && !$course->isDirty('slug')) {
                $course->slug = static::generateUniqueSlug($course->title, $course->id);
            }
        });
    }

    /**
     * Generate a unique slug from title
     *
     * @param string $title
     * @param int|null $excludeId Course ID to exclude from uniqueness check
     * @return string
     */
    protected static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }


    // Relationships
    // public function instructor() // Removed - instructors are assigned to batches now
    // {
    //     return $this->belongsTo(User::class, 'instructor_id');
    // }

    // public function category() // Removed - categories are removed from system
    // {
    //     return $this->belongsTo(\App\Models\Category::class);
    // }

    public function track()
    {
        return $this->belongsTo(\App\Models\Track::class);
    }

    /**
     * Get program through track
     */
    public function program()
    {
        return $this->hasOneThrough(
            \App\Models\Program::class,
            \App\Models\Track::class,
            'id', // Foreign key on tracks table
            'id', // Foreign key on programs table
            'track_id', // Local key on courses table
            'program_id' // Local key on tracks table
        );
    }

    public function batches()
    {
        return $this->hasMany(\App\Models\Batch::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    // Get enrollments through batches
    public function enrollments()
    {
        return \App\Models\Enrollment::whereHas('batch', function($query) {
            $query->where('course_id', $this->id);
        });
    }

    // Get students through batches
    public function students()
    {
        return User::whereHas('enrollments.batch', function($query) {
            $query->where('course_id', $this->id);
        });
    }

    /**
     * Get unique students count from all batches
     * Counts distinct students across all batches of this course
     * This ensures that if a student is enrolled in multiple batches of the same course,
     * they are only counted once
     */
    public function getUniqueStudentsCountAttribute(): int
    {
        $batchIds = $this->batches()->pluck('id')->toArray();
        
        if (empty($batchIds)) {
            return 0;
        }

        // Use select distinct with count for accurate unique student count
        return \App\Models\Enrollment::whereIn('batch_id', $batchIds)
            ->select('student_id')
            ->distinct()
            ->count('student_id');
    }

    /**
     * Get students count - always returns the actual unique count from enrollments
     * This ensures accurate count regardless of database value
     */
    public function getStudentsCount(): int
    {
        // Always calculate unique students count from actual enrollments
        // This ensures accuracy even if database value is outdated
        return $this->getUniqueStudentsCountAttribute();
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'course_id', 'user_id')
            ->withTimestamps();
    }

    public function trackCourseProgress()
    {
        return $this->hasMany(\App\Models\TrackCourseProgress::class);
    }

    // Translation helpers
    public function getTranslatedTitleAttribute()
    {
        return app()->getLocale() === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    public function getTranslatedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }

    // Thumbnail URL accessor - always return full URL
    public function getThumbnailUrlAttribute()
    {
        // Default course image path (in public/images/)
        $defaultImage = 'images/default-course.avif';
        
        // Get thumbnail from attributes (raw database value)
        $thumbnail = $this->attributes['thumbnail'] ?? null;
        
        // Use default image if thumbnail is null or empty
        if (empty($thumbnail)) {
            return asset($defaultImage);
        }

        // If it's already a full URL, return as is
        if (filter_var($thumbnail, FILTER_VALIDATE_URL)) {
            return $thumbnail;
        }

        // Clean the path (remove leading slash if present)
        $thumbnail = ltrim($thumbnail, '/');
        
        // Check if file exists in storage, if not use default
        if (Storage::disk('public')->exists($thumbnail)) {
            // File exists in storage, return storage URL
            return asset('storage/' . $thumbnail);
        }
        
        // File doesn't exist in storage, check if it's a public image path
        $publicPath = public_path($thumbnail);
        if (file_exists($publicPath)) {
            return asset($thumbnail);
        }
        
        // Neither storage nor public file exists, return default image
        return asset($defaultImage);
    }
}

