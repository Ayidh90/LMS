<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use App\Services\ImageService;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function __construct(
        private SettingsService $settingsService,
        private ImageService $imageService
    ) {}

    /**
     * Display the settings page
     */
    public function index()
    {
        $this->settingsService->initializeDefaults();
        
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $this->settingsService->getAll(),
        ]);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $this->validateRequest($request);

        try {
            $settingsToUpdate = $this->prepareSettingsToUpdate($validated, $request);
            
            if (!empty($settingsToUpdate)) {
                $this->settingsService->update($settingsToUpdate);
            }

            return redirect()
                ->route('admin.settings.index')
                ->with('success', __('Settings updated successfully.'));
        } catch (\Exception $e) {
            Log::error('Settings update failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->except(['website_logo', 'website_favicon']),
            ]);

            return back()
                ->withErrors(['general' => __('An error occurred while updating settings. Please try again.')])
                ->withInput();
        }
    }

    /**
     * Validate the incoming request
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            // Instructor permissions
            'instructor_can_create_sections' => ['nullable', 'boolean'],
            'instructor_can_create_lessons' => ['nullable', 'boolean'],
            'instructor_can_create_questions' => ['nullable', 'boolean'],
            
            // Website settings
            'website_name' => ['nullable', 'string', 'max:255'],
            'website_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'website_favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,ico,svg', 'max:512'],
            'website_info' => ['nullable', 'string', 'max:1000'],
            'website_email' => ['nullable', 'email', 'max:255'],
            'website_mobile' => ['nullable', 'string', 'max:50'],
        ], [
            'website_logo.image' => __('The logo must be an image file.'),
            'website_logo.mimes' => __('The logo must be a file of type: jpeg, png, jpg, gif, svg.'),
            'website_logo.max' => __('The logo may not be greater than 2MB.'),
            'website_favicon.image' => __('The favicon must be an image file.'),
            'website_favicon.mimes' => __('The favicon must be a file of type: jpeg, png, jpg, gif, ico, svg.'),
            'website_favicon.max' => __('The favicon may not be greater than 512KB.'),
            'website_email.email' => __('The email must be a valid email address.'),
            'website_name.max' => __('The website name may not be greater than 255 characters.'),
            'website_info.max' => __('The website info may not be greater than 1000 characters.'),
            'website_mobile.max' => __('The mobile number may not be greater than 50 characters.'),
        ]);
    }

    /**
     * Prepare settings array for update
     */
    private function prepareSettingsToUpdate(array $validated, Request $request): array
    {
        $settingsToUpdate = [];

        // Process instructor permissions
        $permissionFields = [
            'instructor_can_create_sections',
            'instructor_can_create_lessons',
            'instructor_can_create_questions',
        ];

        foreach ($permissionFields as $field) {
            if (array_key_exists($field, $validated)) {
                $settingsToUpdate[$field] = $validated[$field];
            }
        }

        // Process website text fields
        $websiteFields = [
            'website_name',
            'website_info',
            'website_email',
            'website_mobile',
        ];

        foreach ($websiteFields as $field) {
            if (array_key_exists($field, $validated)) {
                $settingsToUpdate[$field] = $validated[$field];
            }
        }

        // Handle logo upload
        if ($request->hasFile('website_logo')) {
            $logoPath = $this->handleLogoUpload($request->file('website_logo'));
            if ($logoPath) {
                $settingsToUpdate['website_logo'] = $logoPath;
            }
        }

        // Handle favicon upload
        if ($request->hasFile('website_favicon')) {
            $faviconPath = $this->handleFaviconUpload($request->file('website_favicon'));
            if ($faviconPath) {
                $settingsToUpdate['website_favicon'] = $faviconPath;
            }
        }

        return $settingsToUpdate;
    }

    /**
     * Handle logo upload
     */
    private function handleLogoUpload($file): ?string
    {
        try {
            $oldLogo = Settings::websiteLogo();
            $extension = $file->getClientOriginalExtension();
            
            return $this->imageService->upload(
                $file,
                'settings',
                $oldLogo,
                'logo.' . $extension
            );
        } catch (\Exception $e) {
            Log::error('Logo upload failed: ' . $e->getMessage());
            throw new \RuntimeException(__('Failed to upload logo. Please try again.'), 0, $e);
        }
    }

    /**
     * Handle favicon upload
     */
    private function handleFaviconUpload($file): ?string
    {
        try {
            $oldFavicon = Settings::websiteFavicon();
            $extension = $file->getClientOriginalExtension();
            
            return $this->imageService->upload(
                $file,
                'settings',
                $oldFavicon,
                'favicon.' . $extension
            );
        } catch (\Exception $e) {
            Log::error('Favicon upload failed: ' . $e->getMessage());
            throw new \RuntimeException(__('Failed to upload favicon. Please try again.'), 0, $e);
        }
    }
}
