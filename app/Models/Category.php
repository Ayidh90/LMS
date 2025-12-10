<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'icon',
        'order',
        'is_active',
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

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relationships
    public function courses()
    {
        return $this->hasMany(\Modules\Courses\Models\Course::class);
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
}
