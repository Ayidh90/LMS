<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function __construct(
        private SettingsService $settingsService
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
            'instructor_can_create_sections' => 'boolean',
            'instructor_can_create_lessons' => 'boolean',
            'instructor_can_create_questions' => 'boolean',
        ]);

        $this->settingsService->update($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', __('Settings updated successfully.'));
    }
}
