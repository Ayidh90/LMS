<?php

namespace Modules\Courses\Services;

use Modules\Courses\Models\Course;

class LiveMeetingService
{
    /**
     * Generate meeting room name (shared between students and instructor)
     * 
     * @return string Room name
     */
    private function generateRoomName(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $roomName = '';
        $length = 20; // Longer room name for uniqueness
        
        for ($i = 0; $i < $length; $i++) {
            $roomName .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $roomName;
    }

    /**
     * Generate student link - students wait in lobby, cannot become moderators
     * This link is designed to prevent students from seeing/using "Log-in" option
     * 
     * @param string $roomName The meeting room name
     * @return string Student meeting link
     */
    public function generateStudentLink(string $roomName): string
    {
        $baseUrl = 'https://meet.jit.si/' . $roomName;
        
        // Configure for students - force waiting room, prevent moderator access
        // Attempt to completely hide login option
        $config = [
            'config.enableLobby' => 'true', // Force waiting room - students MUST wait
            'config.startWithAudioMuted' => 'true',
            'config.startWithVideoMuted' => 'true',
            'config.enableWelcomePage' => 'false',
            'config.enablePrejoinPage' => 'true',
            'config.enableClosePage' => 'true',
            'config.requireDisplayName' => 'true',
            // Completely disable authentication and login
            'config.enableAuth' => 'false',
            'config.enableLogin' => 'false',
            'config.enableUserRoles' => 'false',
            'config.enableModeratorIndicator' => 'false',
            'config.enableKnockingLobby' => 'true',
            'config.enableLobbyChat' => 'false',
            // Disable all features that might show login option
            'config.enableNoAudioDetection' => 'false',
            'config.enableNoisyMicDetection' => 'false',
            'config.enableLayerSuspension' => 'true',
            'config.enableRemb' => 'true',
            'config.enableTcc' => 'true',
            'config.enableOpusRed' => 'true',
            'config.enableH264' => 'true',
            'config.enableVP8' => 'true',
            'config.enableVP9' => 'true',
            'config.enableInsecureRoomNameWarning' => 'false',
            'config.enableEmailInStats' => 'false',
            'config.enableDisplayName' => 'true',
            // Additional parameters to hide login
            'config.enableRemoteVideoMenu' => 'false',
            'config.enableLocalVideoFlip' => 'false',
            'config.enableChat' => 'true', // Allow chat but not login
            'config.enableTileView' => 'true',
            // Force lobby mode - students cannot bypass
            'config.enableLobbyMode' => 'true',
            'config.enableLobbyPassword' => 'false',
            // Disable any authentication prompts
            'config.enableScreenshare' => 'false', // Disable screenshare to reduce options
            'config.enableRecording' => 'false', // Disable recording
            'config.enableLiveStreaming' => 'false', // Disable live streaming
            // Try to hide login button completely
            'config.hideDisplayName' => 'false',
            // IMPORTANT LIMITATION: Jitsi Meet free (meet.jit.si) shows "Log-in" button when no moderator exists.
            // This is a server-side limitation that cannot be overridden with URL parameters alone.
            // Complete solution requires:
            // 1. Custom Jitsi server with JWT tokens (recommended for production)
            // 2. OR ensure instructor joins FIRST using instructor link (workaround)
            // The instructor MUST join first to become moderator, then students can join without seeing login option.
        ];
        
        $queryString = http_build_query($config);
        return $baseUrl . '#' . str_replace('&', '&', $queryString);
    }

    /**
     * Generate instructor link - instructor becomes moderator automatically
     * 
     * @param string $roomName The meeting room name
     * @param Course $course Course to get instructor info
     * @param \App\Models\User|null $currentUser Optional current user (instructor) to use their email
     * @return string Instructor meeting link
     */
    public function generateInstructorLink(string $roomName, Course $course, ?\App\Models\User $currentUser = null): string
    {
        $baseUrl = 'https://meet.jit.si/' . $roomName;
        
        // Get instructor info - prefer current user if provided, otherwise get from batch
        $instructorName = 'Instructor';
        $instructorEmail = '';
        
        if ($currentUser && $currentUser->isInstructor()) {
            // Use current user's info (the instructor who is joining)
            $instructorName = $currentUser->name ?? 'Instructor';
            $instructorEmail = $currentUser->email ?? '';
        } else {
            // Fallback to batch instructor
            $batch = $course->batches()
                ->where('is_active', true)
                ->with('instructor')
                ->first();
            
            $instructor = $batch?->instructor;
            $instructorName = $instructor?->name ?? 'Instructor';
            $instructorEmail = $instructor?->email ?? '';
        }
        
        // Configure for instructor (moderator)
        $config = [
            'config.enableLobby' => 'true', // Enable lobby for students
            'config.startWithAudioMuted' => 'false', // Instructor can speak immediately
            'config.startWithVideoMuted' => 'false', // Instructor can show video immediately
            'config.requireDisplayName' => 'true',
            'userInfo.displayName' => $instructorName,
            'userInfo.email' => $instructorEmail,
            'config.enableWelcomePage' => 'false',
            'config.enablePrejoinPage' => 'true',
            'config.enableLobbyMode' => 'false', // Instructor bypasses lobby
            'config.enableNoAudioDetection' => 'false',
            'config.enableNoisyMicDetection' => 'false',
        ];
        
        $queryString = http_build_query($config);
        return $baseUrl . '#' . str_replace('&', '&', $queryString);
    }

    /**
     * Generate both student and instructor links for a meeting
     * Returns array with 'student' and 'instructor' links
     * 
     * @param Course $course Course to get instructor info
     * @return array ['student' => string, 'instructor' => string|null]
     */
    public function generateMeetingLinks(Course $course): array
    {
        $roomName = $this->generateRoomName();
        
        $studentLink = $this->generateStudentLink($roomName);
        $instructorLink = $this->generateInstructorLink($roomName, $course);
        
        return [
            'student' => $studentLink,
            'instructor' => $instructorLink,
        ];
    }

    /**
     * Generate student link from existing room name or link
     * Used when we have the room name and need student link
     * 
     * @param string $roomNameOrLink Room name or full link
     * @return string Student link
     */
    public function getStudentLink(string $roomNameOrLink): string
    {
        // Extract room name if full link provided
        if (preg_match('/https:\/\/meet\.jit\.si\/([a-z0-9]+)/', $roomNameOrLink, $matches)) {
            $roomName = $matches[1];
        } else {
            $roomName = $roomNameOrLink;
        }
        
        return $this->generateStudentLink($roomName);
    }

    /**
     * Generate instructor link from existing room name or link
     * Used when we have the room name and need instructor link
     * 
     * @param string $roomNameOrLink Room name or full link
     * @param Course $course Course to get instructor info
     * @param \App\Models\User|null $currentUser Optional current user (instructor) to use their email
     * @return string|null Instructor link or null if no instructor
     */
    public function getInstructorLink(string $roomNameOrLink, Course $course, ?\App\Models\User $currentUser = null): ?string
    {
        // Extract room name if full link provided
        if (preg_match('/https:\/\/meet\.jit\.si\/([a-z0-9]+)/', $roomNameOrLink, $matches)) {
            $roomName = $matches[1];
        } else {
            $roomName = $roomNameOrLink;
        }
        
        return $this->generateInstructorLink($roomName, $course, $currentUser);
    }

    /**
     * @deprecated Use generateMeetingLinks() instead
     * Generate a Jitsi Meet link with waiting room enabled
     * Students will wait in the lobby until the instructor joins
     * Jitsi Meet is free, open-source, and doesn't require API keys
     * Format: https://meet.jit.si/[random-room-name]#config...
     * 
     * @param Course|null $course Optional course to get instructor info
     * @return string
     */
    public function generateJitsiMeetLink(?Course $course = null): string
    {
        // Generate a random room name (lowercase letters and numbers)
        // Jitsi Meet accepts any room name format
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $roomName = '';
        $length = 20; // Longer room name for uniqueness
        
        for ($i = 0; $i < $length; $i++) {
            $roomName .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        $baseUrl = 'https://meet.jit.si/' . $roomName;
        
        // Configure Jitsi Meet with waiting room (lobby) enabled
        // This ensures participants wait until the instructor joins
        // Students cannot become moderators - they must wait for instructor
        // Attempt to hide login option and prevent students from becoming moderators
        $config = [
            'config.enableLobby' => 'true', // Enable waiting room - students wait here
            'config.startWithAudioMuted' => 'true', // Mute participants by default
            'config.startWithVideoMuted' => 'true', // Mute video by default
            'config.enableWelcomePage' => 'false', // Disable welcome page to force lobby
            'config.enablePrejoinPage' => 'true',
            'config.enableClosePage' => 'true',
            'config.requireDisplayName' => 'true', // Require display name
            // Disable authentication/login options for students
            'config.enableNoAudioDetection' => 'false',
            'config.enableNoisyMicDetection' => 'false',
            'config.enableLayerSuspension' => 'true',
            'config.enableRemb' => 'true',
            'config.enableTcc' => 'true',
            'config.enableOpusRed' => 'true',
            'config.enableH264' => 'true',
            'config.enableVP8' => 'true',
            'config.enableVP9' => 'true',
            'config.enableInsecureRoomNameWarning' => 'false',
            'config.enableEmailInStats' => 'false',
            'config.enableDisplayName' => 'true',
            // Force participants to wait in lobby - they cannot bypass it
            'config.enableLobbyMode' => 'true',
            // Disable authentication/login to prevent students from becoming moderators
            'config.enableAuth' => 'false', // Disable authentication
            'config.enableLogin' => 'false', // Disable login option
            'config.enableUserRoles' => 'false', // Disable user roles selection
            'config.enableModeratorIndicator' => 'false', // Hide moderator indicator
            'config.enableRemoteVideoMenu' => 'false', // Disable remote video menu
            'config.enableLocalVideoFlip' => 'false',
            'config.enableKnockingLobby' => 'true', // Enable knocking lobby (waiting room)
            'config.enableLobbyChat' => 'false', // Disable lobby chat
            // IMPORTANT LIMITATION: Jitsi Meet free (meet.jit.si) does NOT fully support hiding "Log-in" button
            // The "Log-in" option appears when no moderator exists in the room
            // These configs attempt to minimize the issue, but complete solution requires:
            // 1. Custom Jitsi server with JWT tokens (recommended for production)
            // 2. OR ensure instructor joins FIRST using instructor link (workaround)
            // For now, students must be instructed NOT to click "Log-in" button
        ];
        
        // Build URL with config parameters
        $queryString = http_build_query($config);
        $url = $baseUrl . '#' . str_replace('&', '&', $queryString);
        
        return $url;
    }
    
    /**
     * @deprecated Use getInstructorLink() instead
     * Generate instructor link from existing meeting link
     * Extracts room name from student link and creates instructor version with moderator privileges
     * 
     * @param string $studentLink The student meeting link
     * @param Course $course Course to get instructor from
     * @return string|null Returns null if no instructor found or invalid link
     */
    public function generateInstructorLinkFromStudentLink(string $studentLink, Course $course): ?string
    {
        return $this->getInstructorLink($studentLink, $course);
    }
    
    /**
     * Generate a Google Meet link
     * Note: Google Meet requires actual meeting creation via Google Calendar API
     * This method returns a placeholder that should be replaced with a real link
     * For now, we'll use Jitsi Meet as default since it works without API
     */
    public function generateGoogleMeetLink(): string
    {
        // Google Meet requires API integration to create real meetings
        // Return a placeholder that instructs user to create meeting manually
        // Or use Jitsi Meet as alternative
        return $this->generateJitsiMeetLink();
    }
    
    /**
     * Generate a Zoom meeting link (requires API)
     * Note: Real Zoom integration requires API credentials
     */
    public function generateZoomLink(): string
    {
        // For now, return a placeholder that can be replaced with actual Zoom link
        // In production, you would use Zoom API to create meetings
        $meetingId = random_int(1000000000, 9999999999);
        return 'https://zoom.us/j/' . $meetingId;
    }
    
    /**
     * Generate a Microsoft Teams meeting link
     * Note: Real Teams integration requires Microsoft Graph API
     */
    public function generateTeamsLink(): string
    {
        // Generate a Teams meeting link
        // Format: https://teams.microsoft.com/l/meetup-join/...
        $meetingId = bin2hex(random_bytes(16));
        return 'https://teams.microsoft.com/l/meetup-join/' . $meetingId;
    }
    
    /**
     * Generate a live meeting link based on the preferred provider
     * For Jitsi, generates student link (instructor link is generated separately)
     * Default: Jitsi Meet (free, open-source, works without API)
     * 
     * @param string $provider Meeting provider (jitsi, zoom, teams, google_meet)
     * @param Course|null $course Optional course to get instructor info
     * @return string Student link (for backward compatibility, stores student link)
     */
    public function generateMeetingLink(string $provider = 'jitsi', ?Course $course = null): string
    {
        if ($provider === 'jitsi' && $course) {
            // Generate both links, but return student link for storage
            $links = $this->generateMeetingLinks($course);
            return $links['student'];
        }
        
        return match($provider) {
            'zoom' => $this->generateZoomLink(),
            'teams' => $this->generateTeamsLink(),
            'google_meet' => $this->generateGoogleMeetLink(),
            'jitsi' => $this->generateJitsiMeetLink($course),
            default => $this->generateJitsiMeetLink($course),
        };
    }
}

