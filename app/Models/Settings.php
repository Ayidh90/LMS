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
        $setting = self::where('key', $key)->first();
        $finalType = $setting?->type ?? $type;
        
        $stringValue = match (true) {
            is_bool($value) => $value ? '1' : '0',
            is_null($value) => '',
            default => (string) $value,
        };
        
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $finalType,
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

    /**
     * Get website name
     */
    public static function websiteName(): string
    {
        return self::get('website_name', config('app.name', 'LMS'));
    }

    /**
     * Get website logo path
     */
    public static function websiteLogo(): ?string
    {
        return self::get('website_logo', null);
    }

    /**
     * Get website favicon path
     */
    public static function websiteFavicon(): ?string
    {
        return self::get('website_favicon', null);
    }

    /**
     * Get website info/description
     */
    public static function websiteInfo(): ?string
    {
        return self::get('website_info', null);
    }

    /**
     * Get website email
     */
    public static function websiteEmail(): ?string
    {
        return self::get('website_email', null);
    }

    /**
     * Get website mobile/phone
     */
    public static function websiteMobile(): ?string
    {
        return self::get('website_mobile', null);
    }
}
