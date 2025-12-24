<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'slug',
        'thumbnail',
        'is_active',
        'order',
    ];

    protected $appends = [
        'translated_name',
        'translated_description',
        'thumbnail_url',
        'unique_students_count',
        'total_students_count',
    ];

    protected function casts(): array
    {
        return [
            'program_id' => 'integer',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($track) {
            if (empty($track->slug)) {
                $track->slug = static::generateUniqueSlug($track->name);
            }
        });

        static::updating(function ($track) {
            if ($track->isDirty('name') && !$track->isDirty('slug')) {
                $track->slug = static::generateUniqueSlug($track->name, $track->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
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

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationships
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function courses()
    {
        return $this->hasMany(\Modules\Courses\Models\Course::class)->orderBy('order')->orderBy('id');
    }

    /**
     * Get published courses only
     */
    public function publishedCourses()
    {
        return $this->hasMany(\Modules\Courses\Models\Course::class)
            ->where('is_published', true)
            ->orderBy('order')
            ->orderBy('id');
    }

    /**
     * Get active courses only
     */
    public function activeCourses()
    {
        return $this->hasMany(\Modules\Courses\Models\Course::class)
            ->where('is_published', true)
            ->orderBy('order')
            ->orderBy('id');
    }

    public function trackProgress()
    {
        return $this->hasMany(TrackProgress::class);
    }

    /**
     * Get courses count
     */
    public function getCoursesCountAttribute()
    {
        return $this->courses()->count();
    }

    /**
     * Get published courses count
     */
    public function getPublishedCoursesCountAttribute()
    {
        return $this->publishedCourses()->count();
    }

    /**
     * Get unique students count from all courses in this track
     * Counts distinct students across all courses and batches in this track
     */
    public function getUniqueStudentsCountAttribute(): int
    {
        $courseIds = $this->courses()->pluck('id')->toArray();
        
        if (empty($courseIds)) {
            return 0;
        }

        $batchIds = \App\Models\Batch::whereIn('course_id', $courseIds)
            ->pluck('id')
            ->toArray();

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
     * Get total students count for this track
     * This is an alias for getUniqueStudentsCountAttribute for consistency
     */
    public function getTotalStudentsCountAttribute(): int
    {
        return $this->getUniqueStudentsCountAttribute();
    }

    // Translation helpers
    public function getTranslatedNameAttribute()
    {
        return app()->getLocale() === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }

    public function getTranslatedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }

    // Thumbnail URL accessor
    public function getThumbnailUrlAttribute()
    {
        $defaultImage = 'images/default-course.avif';
        
        $thumbnail = $this->attributes['thumbnail'] ?? null;
        
        if (empty($thumbnail)) {
            return asset($defaultImage);
        }

        if (filter_var($thumbnail, FILTER_VALIDATE_URL)) {
            return $thumbnail;
        }

        $thumbnail = ltrim($thumbnail, '/');
        
        if (Storage::disk('public')->exists($thumbnail)) {
            return asset('storage/' . $thumbnail);
        }
        
        $publicPath = public_path($thumbnail);
        if (file_exists($publicPath)) {
            return asset($thumbnail);
        }
        
        return asset($defaultImage);
    }
}

