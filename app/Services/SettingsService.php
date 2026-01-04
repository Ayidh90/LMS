<?php

namespace App\Services;

use App\Models\Settings;

class SettingsService
{
    /**
     * Get all settings
     */
    public function getAll(): array
    {
        return Settings::all()->mapWithKeys(function ($setting) {
            $value = Settings::get($setting->key);
            $result = [
                'value' => $value,
                'type' => $setting->type,
                'description' => $setting->description,
                'description_ar' => $setting->description_ar,
            ];
            
            // For file settings (logo and favicon), include the full URL
            if (in_array($setting->key, ['website_logo', 'website_favicon']) && $value) {
                $result['url'] = Settings::getFileUrl($value);
            }
            
            return [$setting->key => $result];
        })->toArray();
    }

    /**
     * Get instructor permissions settings
     */
    public function getInstructorPermissions(): array
    {
        return [
            'can_create_sections' => Settings::instructorCanCreateSections(),
            'can_create_lessons' => Settings::instructorCanCreateLessons(),
            'can_create_questions' => Settings::instructorCanCreateQuestions(),
        ];
    }

    /**
     * Get website settings
     */
    public function getWebsiteSettings(): array
    {
        return [
            'name' => Settings::websiteName(),
            'logo' => Settings::websiteLogo(),
            'logo_url' => Settings::websiteLogoUrl(),
            'favicon' => Settings::websiteFavicon(),
            'favicon_url' => Settings::websiteFaviconUrl(),
            'info' => Settings::websiteInfo(),
            'email' => Settings::websiteEmail(),
            'mobile' => Settings::websiteMobile(),
        ];
    }

    /**
     * Update settings
     */
    public function update(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $setting = Settings::where('key', $key)->first();
            $type = $setting?->type ?? 'boolean';
            Settings::set($key, $value, $type);
        }
    }

    /**
     * Initialize default settings
     */
    public function initializeDefaults(): void
    {
        $defaults = [
            [
                'key' => 'instructor_can_create_sections',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow instructors to create sections/chapters',
                'description_ar' => 'السماح للمدربين بإنشاء الوحدات التعليمية',
            ],
            [
                'key' => 'instructor_can_create_lessons',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow instructors to create lessons',
                'description_ar' => 'السماح للمدربين بإنشاء الدروس',
            ],
            [
                'key' => 'instructor_can_create_questions',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow instructors to create questions',
                'description_ar' => 'السماح للمدربين بإنشاء الأسئلة',
            ],
            [
                'key' => 'website_name',
                'value' => config('app.name', 'LMS'),
                'type' => 'string',
                'description' => 'Website name',
                'description_ar' => 'اسم الموقع',
            ],
            [
                'key' => 'website_logo',
                'value' => null,
                'type' => 'string',
                'description' => 'Website logo',
                'description_ar' => 'شعار الموقع',
            ],
            [
                'key' => 'website_favicon',
                'value' => null,
                'type' => 'string',
                'description' => 'Website favicon',
                'description_ar' => 'أيقونة الموقع',
            ],
            [
                'key' => 'website_info',
                'value' => null,
                'type' => 'string',
                'description' => 'Website information/description',
                'description_ar' => 'معلومات الموقع',
            ],
            [
                'key' => 'website_email',
                'value' => null,
                'type' => 'string',
                'description' => 'Website contact email',
                'description_ar' => 'البريد الإلكتروني',
            ],
            [
                'key' => 'website_mobile',
                'value' => null,
                'type' => 'string',
                'description' => 'Website contact mobile/phone',
                'description_ar' => 'رقم الجوال',
            ],
        ];

        foreach ($defaults as $default) {
            Settings::firstOrCreate(
                ['key' => $default['key']],
                $default
            );
        }
    }
}

