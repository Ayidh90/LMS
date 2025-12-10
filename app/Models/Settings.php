<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'description_ar',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'string',
        ];
    }

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'boolean'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
                'type' => $type,
            ]
        );
    }

    /**
     * Cast value based on type
     */
    private static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Check if instructor can create sections
     */
    public static function instructorCanCreateSections(): bool
    {
        return self::get('instructor_can_create_sections', true);
    }

    /**
     * Check if instructor can create lessons
     */
    public static function instructorCanCreateLessons(): bool
    {
        return self::get('instructor_can_create_lessons', true);
    }

    /**
     * Check if instructor can create questions
     */
    public static function instructorCanCreateQuestions(): bool
    {
        return self::get('instructor_can_create_questions', true);
    }
}
