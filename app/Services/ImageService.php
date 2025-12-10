<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Exception;

class ImageService
{
    /**
     * Upload an image file to storage
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $oldPath
     * @param string|null $specificFilename If provided, use this filename (for replacing existing files)
     * @return string|null
     */
    public function upload(UploadedFile $file, string $directory = 'courses', ?string $oldPath = null, ?string $specificFilename = null): ?string
    {
        try {
            // Delete old file if exists
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Use specific filename if provided (for replacing), otherwise generate unique filename
            if ($specificFilename) {
                $filename = $directory . '/' . $specificFilename;
            } else {
                $filename = $directory . '/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            }

            // Store file
            $path = $file->storeAs($directory, basename($filename), 'public');

            return $path;
        } catch (Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download image from URL and save to storage
     *
     * @param string $url
     * @param string $directory
     * @param string|null $filename
     * @param string|null $oldPath Old path to delete if replacing
     * @return string|null
     */
    public function downloadFromUrl(string $url, string $directory = 'courses', ?string $filename = null, ?string $oldPath = null): ?string
    {
        try {
            // Delete old file if exists (for replacement)
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Download image
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                Log::warning("Failed to download image from URL: {$url}. HTTP Status: {$response->status()}");
                return null;
            }

            $imageContent = $response->body();

            // Validate image content
            if (!$this->isValidImage($imageContent)) {
                Log::warning("Invalid image content from URL: {$url}");
                return null;
            }

            // Generate filename if not provided
            if (!$filename) {
                $extension = $this->getImageExtension($imageContent, $url);
                $filename = $directory . '/' . Str::random(40) . '.' . $extension;
            } else {
                $filename = $directory . '/' . $filename;
            }

            // Store file (this will replace if filename matches)
            Storage::disk('public')->put($filename, $imageContent);

            return $filename;
        } catch (Exception $e) {
            Log::error("Failed to download image from URL {$url}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an image from storage
     *
     * @param string|null $path
     * @return bool
     */
    public function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        // Don't delete external URLs
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
            return false;
        } catch (Exception $e) {
            Log::error("Failed to delete image {$path}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get full URL for an image path
     *
     * @param string|null $path
     * @return string|null
     */
    public function getUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // If it's already a URL, return as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Return storage URL
        return asset('storage/' . $path);
    }

    /**
     * Validate if content is a valid image
     *
     * @param string $content
     * @return bool
     */
    private function isValidImage(string $content): bool
    {
        $imageInfo = @getimagesizefromstring($content);
        return $imageInfo !== false;
    }

    /**
     * Get image extension from content or URL
     *
     * @param string $content
     * @param string $url
     * @return string
     */
    private function getImageExtension(string $content, string $url): string
    {
        // Try to get extension from image info
        $imageInfo = @getimagesizefromstring($content);
        if ($imageInfo && isset($imageInfo[2])) {
            $mimeTypes = [
                IMAGETYPE_JPEG => 'jpg',
                IMAGETYPE_PNG => 'png',
                IMAGETYPE_GIF => 'gif',
                IMAGETYPE_WEBP => 'webp',
            ];
            if (isset($mimeTypes[$imageInfo[2]])) {
                return $mimeTypes[$imageInfo[2]];
            }
        }

        // Fallback to URL extension
        $urlExtension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        if ($urlExtension && in_array(strtolower($urlExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return strtolower($urlExtension);
        }

        // Default to jpg
        return 'jpg';
    }
}

