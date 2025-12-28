<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
     * Get website logo URL for frontend
     */
    public static function websiteLogoUrl(): ?string
    {
        $logo = self::websiteLogo();
        if (!$logo) {
            return null;
        }
        return self::getFileUrl($logo);
    }

    /**
     * Set website logo path
     */
    public static function setWebsiteLogo(?string $path): void
    {
        self::set('website_logo', $path, 'string');
    }

    /**
     * Get website favicon path
     */
    public static function websiteFavicon(): ?string
    {
        return self::get('website_favicon', null);
    }

    /**
     * Get website favicon URL for frontend
     */
    public static function websiteFaviconUrl(): ?string
    {
        $favicon = self::websiteFavicon();
        if (!$favicon) {
            return null;
        }
        return self::getFileUrl($favicon);
    }

    /**
     * Set website favicon path
     */
    public static function setWebsiteFavicon(?string $path): void
    {
        self::set('website_favicon', $path, 'string');
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

    /**
     * Get file URL for frontend display
     * Handles both storage paths and external URLs
     * Similar to Course model's thumbnail URL method
     * Returns URLs like: http://localhost:8000/images/settings/favicon.png
     *
     * @param string|null $path
     * @return string|null
     */
    public static function getFileUrl(?string $path): ?string
    {
        if (!$path || $path === '' || $path === null) {
            return null;
        }

        // If it's already a full URL (http/https), return as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Clean the path (remove leading slash if present)
        $cleanPath = ltrim($path, '/');
        
        // Extract filename from path (e.g., "settings/favicon.png" -> "favicon.png")
        $filename = basename($cleanPath);
        $imagesPath = 'images/settings/' . $filename;
        
        // Check if file exists in public/images/settings/ (preferred location)
        $publicImagesPath = public_path($imagesPath);
        if (file_exists($publicImagesPath)) {
            return asset($imagesPath);
        }
        
        // Fallback: Check if file exists in storage
        if (Storage::disk('public')->exists($cleanPath)) {
            // File exists in storage, return storage URL
            return asset('storage/' . $cleanPath);
        }
        
        // File doesn't exist in either location, but still return URL
        // (might be uploaded or external) - prefer images path
        return asset($imagesPath);
    }

    /**
     * Check if a file exists in storage
     *
     * @param string|null $path
     * @return bool
     */
    public static function fileExists(?string $path): bool
    {
        if (!$path || $path === '') {
            return false;
        }

        // If it's an external URL, assume it exists
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return true;
        }

        // Check if file exists in storage
        return Storage::disk('public')->exists($path);
    }

    /**
     * Delete a file from storage
     *
     * @param string|null $path
     * @return bool
     */
    public static function deleteFile(?string $path): bool
    {
        if (!$path || $path === '') {
            return false;
        }

        // Don't delete external URLs
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Delete from storage
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Get file setting with URL for frontend
     * Returns both path and URL for convenience
     *
     * @param string $key
     * @return array{path: string|null, url: string|null, exists: bool}
     */
    public static function getFileSetting(string $key): array
    {
        $path = self::get($key, null);
        
        return [
            'path' => $path,
            'url' => $path ? self::getFileUrl($path) : null,
            'exists' => self::fileExists($path),
        ];
    }

    /**
     * Set file setting and optionally delete old file
     *
     * @param string $key
     * @param string|null $newPath
     * @param bool $deleteOld
     * @return void
     */
    public static function setFileSetting(string $key, ?string $newPath, bool $deleteOld = true): void
    {
        if ($deleteOld) {
            $oldPath = self::get($key, null);
            if ($oldPath && $oldPath !== $newPath) {
                self::deleteFile($oldPath);
            }
        }

        self::set($key, $newPath, 'string');
    }
}
