<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use App\Services\ImageService;
use App\Models\Settings;
use Illuminate\Http\Request;
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
        // Initialize default settings if they don't exist
        $this->settingsService->initializeDefaults();
        
        $settings = $this->settingsService->getAll();
        
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Instructor permissions
            'instructor_can_create_sections' => 'nullable|boolean',
            'instructor_can_create_lessons' => 'nullable|boolean',
            'instructor_can_create_questions' => 'nullable|boolean',
            
            // Website settings
            'website_name' => 'nullable|string|max:255',
            'website_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'website_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,svg|max:512',
            'website_info' => 'nullable|string|max:1000',
            'website_email' => 'nullable|email|max:255',
            'website_mobile' => 'nullable|string|max:50',
        ]);

        $settingsToUpdate = [];

        // Handle instructor permissions
        if (isset($validated['instructor_can_create_sections'])) {
            $settingsToUpdate['instructor_can_create_sections'] = $validated['instructor_can_create_sections'];
        }
        if (isset($validated['instructor_can_create_lessons'])) {
            $settingsToUpdate['instructor_can_create_lessons'] = $validated['instructor_can_create_lessons'];
        }
        if (isset($validated['instructor_can_create_questions'])) {
            $settingsToUpdate['instructor_can_create_questions'] = $validated['instructor_can_create_questions'];
        }

        // Handle website settings
        if (isset($validated['website_name'])) {
            $settingsToUpdate['website_name'] = $validated['website_name'];
        }
        if (isset($validated['website_info'])) {
            $settingsToUpdate['website_info'] = $validated['website_info'];
        }
        if (isset($validated['website_email'])) {
            $settingsToUpdate['website_email'] = $validated['website_email'];
        }
        if (isset($validated['website_mobile'])) {
            $settingsToUpdate['website_mobile'] = $validated['website_mobile'];
        }

        // Handle logo upload
        if ($request->hasFile('website_logo')) {
            $oldLogo = Settings::websiteLogo();
            $logoPath = $this->imageService->upload(
                $request->file('website_logo'),
                'settings',
                $oldLogo,
                'logo.' . $request->file('website_logo')->getClientOriginalExtension()
            );
            if ($logoPath) {
                $settingsToUpdate['website_logo'] = $logoPath;
            }
        }

        // Handle favicon upload
        if ($request->hasFile('website_favicon')) {
            $oldFavicon = Settings::websiteFavicon();
            $faviconPath = $this->imageService->upload(
                $request->file('website_favicon'),
                'settings',
                $oldFavicon,
                'favicon.' . $request->file('website_favicon')->getClientOriginalExtension()
            );
            if ($faviconPath) {
                $settingsToUpdate['website_favicon'] = $faviconPath;
            }
        }

        // Update settings
        if (!empty($settingsToUpdate)) {
            $this->settingsService->update($settingsToUpdate);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', __('Settings updated successfully.'));
    }
}
