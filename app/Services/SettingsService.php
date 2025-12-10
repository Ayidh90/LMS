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
            return [$setting->key => [
                'value' => Settings::get($setting->key),
                'type' => $setting->type,
                'description' => $setting->description,
                'description_ar' => $setting->description_ar,
            ]];
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
                'description_ar' => 'السماح للمدربين بإنشاء الفصول',
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
        ];

        foreach ($defaults as $default) {
            Settings::firstOrCreate(
                ['key' => $default['key']],
                $default
            );
        }
    }
}

