<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($program) {
            if (empty($program->slug)) {
                $program->slug = static::generateUniqueSlug($program->name);
            }
        });

        static::updating(function ($program) {
            if ($program->isDirty('name') && !$program->isDirty('slug')) {
                $program->slug = static::generateUniqueSlug($program->name, $program->id);
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
    public function tracks()
    {
        return $this->hasMany(Track::class)->orderBy('order');
    }

    /**
     * Get all courses through tracks
     */
    public function courses()
    {
        return \Modules\Courses\Models\Course::whereHas('track', function($query) {
            $query->where('program_id', $this->id);
        })->orderBy('order');
    }

    /**
     * Get active tracks only
     */
    public function activeTracks()
    {
        return $this->hasMany(Track::class)->where('is_active', true)->orderBy('order');
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

