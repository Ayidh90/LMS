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
        'title',
        'title_ar',
        'description',
        'description_ar',
        'slug',
        // 'instructor_id', // Removed - instructors are assigned to batches now
        // 'category_id', // Removed - categories are removed from system
        'thumbnail',
        'level',
        'price',
        'is_published',
        'duration_hours',
        'students_count',
    ];

    protected $appends = [
        'translated_title',
        'translated_description',
        'thumbnail_url',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_published' => 'boolean',
            'duration_hours' => 'integer',
            'students_count' => 'integer',
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

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'course_id', 'user_id')
            ->withTimestamps();
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

