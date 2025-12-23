<template>
    <AdminLayout :page-title="t('admin.live_meetings') || 'Live Meetings'">
        <Head :title="t('admin.live_meetings') || 'Live Meetings'" />
        
        <div class="container-fluid px-3 px-md-4 px-lg-5 py-4 live-meetings-page">
            <!-- Page Header - Bootstrap Style -->
            <div class="card border-0 shadow-lg mb-4 header-card">
                <div class="card-body p-4 p-md-5 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 w-100 h-100 opacity-10 header-overlay"></div>
                    <div class="row align-items-center position-relative">
                        <div class="col-12 col-md-8 mb-3 mb-md-0">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="header-icon-wrapper">
                                    <i class="bi bi-camera-video fs-2"></i>
                                </div>
                                <div>
                                    <h1 class="h3 fw-bold mb-1 header-title">{{ t('admin.live_meetings') || 'Live Meetings' }}</h1>
                                    <p class="header-description">{{ t('admin.live_meetings_description') || 'Manage and join all live meeting sessions across courses' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-md-end">
                            <div class="header-badge">
                                <i class="bi bi-camera-video-fill me-2"></i>
                                {{ activeMeetings.length }} {{ t('admin.live_now') || 'Live Now' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row g-3 stats-row">
                <div class="col-12 col-md-4">
                    <div class="stat-card live">
                        <div class="stat-icon live">
                            <i class="bi bi-broadcast-pin"></i>
                        </div>
                        <div>
                            <p class="stat-label">{{ t('admin.live_now') || 'Live Now' }}</p>
                            <h4 class="stat-value mb-1">{{ activeMeetings.length }}</h4>
                            <p class="stat-sub mb-0">{{ t('admin.live_meetings_now') || 'Currently running sessions' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card upcoming">
                        <div class="stat-icon upcoming">
                            <i class="bi bi-calendar2-week"></i>
                        </div>
                        <div>
                            <p class="stat-label">{{ t('admin.upcoming_meetings') || 'Upcoming' }}</p>
                            <h4 class="stat-value mb-1">{{ upcomingMeetings.length }}</h4>
                            <p class="stat-sub mb-0">{{ t('admin.status_upcoming') || 'Scheduled next' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card past">
                        <div class="stat-icon past">
                            <i class="bi bi-archive"></i>
                        </div>
                        <div>
                            <p class="stat-label">{{ t('admin.past_meetings') || 'Completed' }}</p>
                            <h4 class="stat-value mb-1">{{ pastMeetings.length }}</h4>
                            <p class="stat-sub mb-0">{{ t('admin.status_past') || 'Finished sessions' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Meetings -->
            <div class="card border-0 shadow-sm mb-4 tab-wrapper">
                <div class="card-header bg-transparent border-0 pb-0">
                    <ul class="nav nav-pills tab-nav">
                        <li class="nav-item">
                            <button 
                                class="nav-link"
                                :class="{ active: activeTab === 'live' }"
                                @click="activeTab = 'live'"
                            >
                                <i class="bi bi-broadcast-pin me-2"></i>
                                {{ t('admin.live_meetings_now') || 'Live Now' }}
                                <span class="badge tab-badge live">{{ activeMeetings.length }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button 
                                class="nav-link"
                                :class="{ active: activeTab === 'upcoming' }"
                                @click="activeTab = 'upcoming'"
                            >
                                <i class="bi bi-calendar-event me-2"></i>
                                {{ t('admin.upcoming_meetings') || 'Upcoming' }}
                                <span class="badge tab-badge upcoming">{{ upcomingMeetings.length }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button 
                                class="nav-link"
                                :class="{ active: activeTab === 'past' }"
                                @click="activeTab = 'past'"
                            >
                                <i class="bi bi-clock-history me-2"></i>
                                {{ t('admin.past_meetings') || 'Past' }}
                                <span class="badge tab-badge past">{{ pastMeetings.length }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body pt-3">
                    <div v-if="currentTabMeetings.length > 0" class="row g-4">
                        <div 
                            v-for="meeting in currentTabMeetings" 
                            :key="meeting.id"
                            class="col-12 col-sm-6 col-lg-4"
                        >
                            <div class="card live-meeting-card h-100 border-0 shadow-sm cursor-pointer position-relative overflow-hidden" @click="openLiveMeeting(meeting)">
                                <!-- Enhanced Status Badge -->
                                <div class="position-absolute top-0 end-0 m-2 z-3">
                                    <span 
                                        class="status-badge status-badge-enhanced rounded-pill d-inline-flex align-items-center gap-1"
                                        :class="getMeetingStatus(meeting).badgeClass"
                                    >
                                        <i :class="getMeetingStatus(meeting).icon" class="badge-icon"></i>
                                        <span class="badge-text">{{ getMeetingStatus(meeting).text }}</span>
                                    </span>
                                </div>

                                <div class="card-body p-4">
                                    <!-- Lesson Header -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start gap-3 mb-2">
                                            <div class="meeting-icon-wrapper" :class="getMeetingStatus(meeting).iconClass">
                                                <i class="bi bi-camera-video fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <h5 class="meeting-title fw-bold mb-1 line-clamp-2">
                                                    {{ meeting.title }}
                                                </h5>
                                                <p v-if="meeting.course" class="course-name mb-1">
                                                    <i class="bi bi-book me-1"></i>
                                                    {{ meeting.course.title }}
                                                </p>
                                                <div v-if="meeting.description" class="lesson-description text-muted small line-clamp-2">
                                                    {{ meeting.description }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Meeting Date & Time -->
                                    <div v-if="meeting.live_meeting_date" class="meeting-info-section border-top pt-3 mb-3">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-calendar-event info-icon"></i>
                                            <span class="info-label">{{ t('lessons.live.meeting_date') || 'Meeting Date & Time' }}:</span>
                                        </div>
                                        <p class="meeting-date-time fw-bold mb-0">
                                            {{ formatDateTime(meeting.live_meeting_date) }}
                                        </p>
                                    </div>

                                    <!-- Duration -->
                                    <div v-if="meeting.duration_minutes" class="meeting-info-item mb-3">
                                        <p class="info-text mb-0 d-flex align-items-center">
                                            <i class="bi bi-clock me-2"></i>
                                            <span>{{ meeting.duration_minutes }} {{ t('common.minutes') || 'minutes' }}</span>
                                        </p>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="border-top pt-3">
                                        <button 
                                            class="join-button btn w-100 d-flex align-items-center justify-content-center gap-2"
                                            @click.stop="openLiveMeeting(meeting)"
                                        >
                                            <i :class="activeTab === 'upcoming' ? 'bi bi-calendar-check' : 'bi bi-box-arrow-up-right'"></i>
                                            {{ activeTab === 'upcoming' ? (t('lessons.live.view_details') || 'View Details') : (t('lessons.live.join_meeting') || 'Join Live Meeting') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="card-hover-overlay"></div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="card border-0 shadow-sm empty-card">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i :class="emptyState.icon" class="empty-icon"></i>
                            </div>
                            <h4 class="fw-bold mb-2">{{ emptyState.title }}</h4>
                            <p class="mb-0">{{ emptyState.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';

const { t } = useTranslation();
const { route } = useRoute();

const props = defineProps({
    liveMeetings: {
        type: Array,
        default: () => [],
    },
});

// Debug: Log the received data
console.log('Live Meetings Data:', props.liveMeetings);
console.log('Total Live Meetings:', props.liveMeetings?.length || 0);

// Parse date from database format (YYYY-MM-DD HH:mm:ss) without timezone conversion
// This matches EXACTLY the PHP parseDateFromDB function in LessonController::liveMeetingsCount()
const parseDateFromDB = (value) => {
    if (!value) return null;
    
    try {
        let dateStr = value;
        
        // Handle database format: "YYYY-MM-DD HH:mm:ss" (treat as local time)
        // Matches PHP regex: /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/
        if (typeof dateStr === 'string' && /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(dateStr)) {
            // Parse as local time by creating date components manually
            // Matches PHP: [$year, $month, $day] = array_map('intval', explode('-', $datePart));
            const [datePart, timePart] = dateStr.split(' ');
            const [year, month, day] = datePart.split('-').map(Number);
            const [hours, minutes, seconds] = timePart.split(':').map(Number);
            
            // Create date in local timezone (no timezone conversion)
            // JavaScript months are 0-based, so we use month - 1
            // PHP Carbon months are 1-based, so PHP uses month directly
            const date = new Date(year, month - 1, day, hours, minutes, seconds || 0);
            
            if (isNaN(date.getTime())) {
                return null;
            }
            
            return date;
        }
        
        // Fallback: try parsing as-is
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) {
            return null;
        }
        
        return date;
    } catch (e) {
        return null;
    }
};

// Safe date helper (backward compatibility)
const parseDate = (value) => {
    return parseDateFromDB(value);
};

// Get current timestamp (matches PHP: $nowTimestamp = $nowDate->timestamp)
const getNowTimestamp = () => {
    const now = new Date();
    return Math.floor(now.getTime() / 1000); // Convert to seconds (Unix timestamp)
};

// Get timestamp from date (matches PHP: $meetingTimestamp = $meetingDate->timestamp)
const getTimestamp = (date) => {
    if (!date || isNaN(date.getTime())) return null;
    return Math.floor(date.getTime() / 1000); // Convert to seconds (Unix timestamp)
};

const sortByDate = (list, direction = 'asc') => {
    const multiplier = direction === 'desc' ? -1 : 1;
    return [...list].sort((a, b) => {
        const dateA = parseDate(a.live_meeting_date);
        const dateB = parseDate(b.live_meeting_date);
        if (!dateA && !dateB) return 0;
        if (!dateA) return 1;
        if (!dateB) return -1;
        return (dateA - dateB) * multiplier;
    });
};

// Tabs state - default to live
const activeTab = ref('live');

// Computed to check if we have any meetings
const hasMeetings = computed(() => {
    return props.liveMeetings && props.liveMeetings.length > 0;
});

// Separate meetings: live (started but not ended), upcoming (not started yet), and past (ended)
// Matches EXACTLY PHP logic in LessonController::liveMeetingsCount()
const activeMeetings = computed(() => {
    if (!props.liveMeetings || props.liveMeetings.length === 0) {
        return [];
    }
    
    const nowTimestamp = getNowTimestamp();
    const activeIds = [];
    
    // First pass: Calculate active meetings (matches PHP exactly)
    const active = props.liveMeetings.filter(meeting => {
        if (!meeting || !meeting.live_meeting_date) return false;
        
        try {
            const meetingDate = parseDateFromDB(meeting.live_meeting_date);
            if (!meetingDate || isNaN(meetingDate.getTime())) {
                return false;
            }
            
            const meetingTimestamp = getTimestamp(meetingDate);
            if (!meetingTimestamp) return false;
            
            const duration = meeting.duration_minutes || 60;
            const endDate = new Date(meetingDate.getTime() + duration * 60000);
            const endTimestamp = getTimestamp(endDate);
            if (!endTimestamp) return false;
            
            // Active: meetingTimestamp <= nowTimestamp && endTimestamp >= nowTimestamp
            // Matches PHP: if ($meetingTimestamp <= $nowTimestamp && $endTimestamp >= $nowTimestamp)
            const isActive = meetingTimestamp <= nowTimestamp && endTimestamp >= nowTimestamp;
            
            if (isActive) {
                activeIds.push(meeting.id);
            }
            
            return isActive;
        } catch (e) {
            return false;
        }
    });
    
    return sortByDate(active, 'asc');
});

const upcomingMeetings = computed(() => {
    if (!props.liveMeetings || props.liveMeetings.length === 0) return [];
    
    const nowTimestamp = getNowTimestamp();
    const activeIds = new Set(activeMeetings.value.map(m => m.id));
    
    // Second pass: Calculate upcoming meetings (matches PHP exactly)
    const upcoming = props.liveMeetings.filter(meeting => {
        if (!meeting || !meeting.live_meeting_date) return false;
        
        // Exclude active meetings (matches PHP: isset($activeIdsMap[$lesson->id]))
        if (activeIds.has(meeting.id)) return false;
        
        try {
            const meetingDate = parseDateFromDB(meeting.live_meeting_date);
            if (!meetingDate || isNaN(meetingDate.getTime())) {
                return false;
            }
            
            const meetingTimestamp = getTimestamp(meetingDate);
            if (!meetingTimestamp) return false;
            
            // Upcoming: meetingTimestamp > nowTimestamp
            // Matches PHP: if ($meetingTimestamp > $nowTimestamp)
            return meetingTimestamp > nowTimestamp;
        } catch (e) {
            return false;
        }
    });
    
    return sortByDate(upcoming, 'asc');
});

const pastMeetings = computed(() => {
    if (!props.liveMeetings || props.liveMeetings.length === 0) {
        return [];
    }
    
    const nowTimestamp = getNowTimestamp();
    const activeIds = new Set(activeMeetings.value.map(m => m.id));
    const upcomingIds = new Set(upcomingMeetings.value.map(m => m.id));
    
    // Third pass: Calculate past meetings (matches PHP exactly)
    const past = props.liveMeetings.filter(meeting => {
        // Skip if already in active or upcoming (matches PHP: isset($activeIdsMap) || isset($upcomingIdsMap))
        if (activeIds.has(meeting.id) || upcomingIds.has(meeting.id)) {
            return false;
        }
        
        // If no date, consider it past (matches PHP: if (!$lesson->live_meeting_date) { $pastCount++; })
        if (!meeting || !meeting.live_meeting_date) {
            return true;
        }
        
        try {
            const meetingDate = parseDateFromDB(meeting.live_meeting_date);
            if (!meetingDate || isNaN(meetingDate.getTime())) {
                return false; // Skip invalid dates (matches PHP: continue)
            }
            
            const meetingTimestamp = getTimestamp(meetingDate);
            if (!meetingTimestamp) return false;
            
            const duration = meeting.duration_minutes || 60;
            const endDate = new Date(meetingDate.getTime() + duration * 60000);
            const endTimestamp = getTimestamp(endDate);
            if (!endTimestamp) return false;
            
            // Past: endTimestamp < nowTimestamp
            // Matches PHP: if ($endTimestamp < $nowTimestamp)
            return endTimestamp < nowTimestamp;
        } catch (e) {
            return false;
        }
    });
    
    return sortByDate(past, 'desc');
});

const currentTabMeetings = computed(() => {
    if (activeTab.value === 'live') return activeMeetings.value;
    if (activeTab.value === 'upcoming') return upcomingMeetings.value;
    return pastMeetings.value;
});

const emptyState = computed(() => {
    if (activeTab.value === 'live') {
        return {
            icon: 'bi bi-camera-video-off',
            title: t('admin.no_live_meetings_now') || 'No Live Meetings Now',
            description: t('admin.no_live_meetings_now_description') || 'There are no active live meeting sessions at the moment.',
        };
    }
    if (activeTab.value === 'upcoming') {
        return {
            icon: 'bi bi-calendar-x',
            title: t('admin.no_upcoming_meetings') || 'No Upcoming Meetings',
            description: t('admin.no_upcoming_meetings_description') || 'There are no upcoming meeting sessions scheduled.',
        };
    }
    return {
        icon: 'bi bi-clock-history',
        title: t('admin.no_past_meetings') || 'No Past Meetings',
        description: t('admin.no_past_meetings_description') || 'There are no past meeting sessions.',
    };
});

const pickFirstNonEmptyTab = () => {
    if (activeMeetings.value.length > 0) return 'live';
    if (upcomingMeetings.value.length > 0) return 'upcoming';
    if (pastMeetings.value.length > 0) return 'past';
    return 'live';
};

watch([activeMeetings, upcomingMeetings, pastMeetings], () => {
    if (currentTabMeetings.value.length === 0) {
        activeTab.value = pickFirstNonEmptyTab();
    }
}, { immediate: true });

// Format date and time - use the database date format parser
const formatDateTime = (dateTime) => {
    if (!dateTime) return '';
    
    try {
        // Use the same parser to ensure consistency
        const date = parseDateFromDB(dateTime);
        
        // Check if date is valid
        if (!date || isNaN(date.getTime())) {
            console.warn('Invalid date:', dateTime);
            return '';
        }
        
        // Format with locale-aware date and time
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
            timeZoneName: 'short'
        }).format(date);
    } catch (e) {
        console.error('Error formatting date:', e, dateTime);
        return '';
    }
};

// Get meeting status (upcoming, ongoing, past)
// Matches EXACTLY PHP logic in LessonController::liveMeetingsCount()
const getMeetingStatus = (meeting) => {
    if (!meeting.live_meeting_date) {
        return {
            class: 'bg-secondary',
            badgeClass: 'status-badge-unknown',
            icon: 'bi-question-circle-fill',
            iconClass: '',
            text: t('admin.status_unknown') || 'Unknown',
        };
    }
    
    const nowTimestamp = getNowTimestamp();
    const meetingDate = parseDateFromDB(meeting.live_meeting_date);
    if (!meetingDate || isNaN(meetingDate.getTime())) {
        return {
            class: 'bg-secondary',
            badgeClass: 'status-badge-unknown',
            icon: 'bi-question-circle-fill',
            iconClass: '',
            text: t('admin.status_unknown') || 'Unknown',
        };
    }
    
    const meetingTimestamp = getTimestamp(meetingDate);
    if (!meetingTimestamp) {
        return {
            class: 'bg-secondary',
            badgeClass: 'status-badge-unknown',
            icon: 'bi-question-circle-fill',
            iconClass: '',
            text: t('admin.status_unknown') || 'Unknown',
        };
    }
    
    const duration = meeting.duration_minutes || 60; // Default 60 minutes
    const endDate = new Date(meetingDate.getTime() + duration * 60000);
    const endTimestamp = getTimestamp(endDate);
    if (!endTimestamp) {
        return {
            class: 'bg-secondary',
            badgeClass: 'status-badge-unknown',
            icon: 'bi-question-circle-fill',
            iconClass: '',
            text: t('admin.status_unknown') || 'Unknown',
        };
    }
    
    // Active: meetingTimestamp <= nowTimestamp && endTimestamp >= nowTimestamp
    if (meetingTimestamp <= nowTimestamp && endTimestamp >= nowTimestamp) {
        return {
            class: 'bg-success',
            badgeClass: 'status-badge-ongoing',
            icon: 'bi-broadcast-pin-fill',
            iconClass: 'live-icon',
            text: t('admin.status_ongoing') || 'Live Now',
        };
    } 
    // Upcoming: meetingTimestamp > nowTimestamp
    else if (meetingTimestamp > nowTimestamp) {
        return {
            class: 'bg-info',
            badgeClass: 'status-badge-upcoming',
            icon: 'bi-calendar-event-fill',
            iconClass: 'upcoming-icon',
            text: t('admin.status_upcoming') || 'Upcoming',
        };
    } 
    // Past: endTimestamp < nowTimestamp
    else {
        return {
            class: 'bg-secondary',
            badgeClass: 'status-badge-past',
            icon: 'bi-check-circle-fill',
            iconClass: 'past-icon',
            text: t('admin.status_past') || 'Past',
        };
    }
};

// Open live meeting
const openLiveMeeting = (meeting) => {
    if (meeting.live_meeting_link) {
        // Open meeting link in new tab
        window.open(meeting.live_meeting_link, '_blank');
    } else {
        // Navigate to lesson page if no link
        router.visit(route('admin.courses.lessons.show', {
            course: meeting.course.slug || meeting.course.id,
            lesson: meeting.id,
        }));
    }
};
</script>

<style scoped>
.live-meetings-page {
    min-height: calc(100vh - 200px);
    padding-top: 2rem;
    padding-bottom: 2rem;
    background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
}

/* RTL Support */
[dir="rtl"] .live-meetings-page {
    direction: rtl;
}

[dir="rtl"] .me-2 {
    margin-left: 0.5rem !important;
    margin-right: 0 !important;
}

[dir="rtl"] .ms-2 {
    margin-right: 0.5rem !important;
    margin-left: 0 !important;
}

/* Header Styles */
.header-card {
    border-radius: 1rem;
    border: none;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.header-card .header-title,
.header-card .header-description {
    color: white;
}

.header-icon-wrapper {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

.header-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}


.header-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.header-description {
    font-size: 0.95rem;
    margin-bottom: 0;
}

.header-badge {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

.header-overlay {
    display: none;
}

.tab-wrapper {
    border-radius: 1rem;
}

.tab-nav {
    gap: 0.5rem;
    flex-wrap: wrap;
}

.tab-nav .nav-link {
    border-radius: 0.75rem;
    background: #f8fafc;
    color: #334155;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.65rem 0.95rem;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.tab-nav .nav-link:hover {
    background: #eef2ff;
    color: #4338ca;
    border-color: #c7d2fe;
}

.tab-nav .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.25);
}

.tab-badge {
    font-weight: 700;
    font-size: 0.8rem;
    padding: 0.25rem 0.55rem;
    border-radius: 0.5rem;
}

.tab-badge.live {
    background: rgba(16, 185, 129, 0.15);
    color: #065f46;
}

.tab-badge.upcoming {
    background: rgba(59, 130, 246, 0.15);
    color: #0b4f91;
}

.tab-badge.past {
    background: rgba(234, 179, 8, 0.18);
    color: #92400e;
}

.empty-card {
    border-radius: 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
}

.empty-icon {
    font-size: 3.5rem;
    opacity: 0.7;
    color: #475569;
}

.stats-row {
    margin-bottom: 2rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    height: 100%;
    padding: 1rem 1.25rem;
    border-radius: 1rem;
    background: #ffffff;
    border: 1px solid #e9ecef;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
}

.stat-card.live {
    background: linear-gradient(135deg, #eef2ff 0%, #ede9fe 100%);
    border-color: #c7d2fe;
}

.stat-card.upcoming {
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    border-color: #bae6fd;
}

.stat-card.past {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-color: #fcd34d;
}

.stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    background: #f8fafc;
    color: #4f46e5;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
}

.stat-icon.live {
    background: rgba(103, 117, 255, 0.12);
    color: #4338ca;
}

.stat-icon.upcoming {
    background: rgba(59, 130, 246, 0.12);
    color: #0ea5e9;
}

.stat-icon.past {
    background: rgba(234, 179, 8, 0.14);
    color: #d97706;
}

.stat-label {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #475569;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #111827;
}

.stat-sub {
    font-size: 0.85rem;
    color: #64748b;
}

/* Section Title */
.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

.section-title i {
    color: inherit;
}

/* Card Styles */
.live-meeting-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    border-radius: 1rem;
    background: white;
    border: 1px solid #e9ecef;
}

.live-meeting-card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.past-card {
    opacity: 0.85;
}

.upcoming-card {
    border-color: rgba(59, 130, 246, 0.3);
}

.live-meeting-card:hover {
    transform: translateY(-6px);
    opacity: 1;
}

.card-hover-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.live-meeting-card:hover .card-hover-overlay {
    opacity: 0.05;
}

/* Meeting Icon */
.meeting-icon-wrapper {
    width: 52px;
    height: 52px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
    color: #667eea;
    transition: all 0.3s ease;
}

.meeting-icon-wrapper i {
    color: inherit;
    font-size: 1.5rem;
}

.meeting-icon-wrapper.live-icon {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #059669;
}

.meeting-icon-wrapper.upcoming-icon {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
}

.meeting-icon-wrapper.past-icon {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    color: #6b7280;
}

/* Meeting Title */
.meeting-title {
    font-size: 1.1rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.course-name {
    font-size: 0.875rem;
    margin-bottom: 0;
    color: #667eea;
    font-weight: 600;
}

.course-name i {
    color: #667eea;
}

.lesson-description {
    font-size: 0.8rem;
    line-height: 1.5;
    margin-top: 0.5rem;
    color: #6b7280;
}

.meeting-meta-info {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 0.75rem;
    border: 1px solid #e9ecef;
}

/* Enhanced Status Badges */
.status-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.375rem 0.75rem;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.status-badge-enhanced {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.4rem 0.85rem;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    border: 1.5px solid rgba(255, 255, 255, 0.4);
    position: relative;
    overflow: hidden;
    z-index: 10;
    line-height: 1.2;
}

.status-badge-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
    z-index: 1;
}

.status-badge-enhanced:hover::before {
    left: 100%;
}

.status-badge-enhanced .badge-icon {
    font-size: 0.75rem;
    margin-right: 0.35rem;
    display: inline-block;
    position: relative;
    z-index: 2;
    vertical-align: middle;
}

.status-badge-enhanced .badge-text {
    font-weight: 700;
    letter-spacing: 0.4px;
    position: relative;
    z-index: 2;
    vertical-align: middle;
}

.status-badge-upcoming {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.5);
}

.status-badge-upcoming:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.6);
}

.status-badge-ongoing {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5);
    animation: badge-glow-pulse 2.5s ease-in-out infinite alternate;
}

.status-badge-ongoing .badge-icon {
    animation: pulse-icon-live 1.8s ease-in-out infinite;
}

.status-badge-ongoing:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.7);
    animation: badge-glow-pulse-hover 1s ease-in-out infinite alternate;
}

.status-badge-past {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
}

.status-badge-past:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 6px 16px rgba(107, 114, 128, 0.5);
}

@keyframes pulse-icon-live {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.15);
        opacity: 0.9;
    }
}

@keyframes badge-glow-pulse {
    from {
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.5);
    }
    to {
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.7);
    }
}

@keyframes badge-glow-pulse-hover {
    from {
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.7);
    }
    to {
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.9);
    }
}

/* Info Sections */
.meeting-info-section {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    padding-top: 1rem;
}

.info-icon {
    font-size: 1rem;
}

.info-label {
    font-weight: 600;
    font-size: 0.875rem;
}

.meeting-date-time {
    font-size: 0.95rem;
    font-weight: 700;
}

.meeting-info-item {
    margin-bottom: 0.75rem;
}

.info-text {
    font-size: 0.875rem;
    margin-bottom: 0;
}

.info-text i {
    color: inherit;
}

/* Join Button */
.join-button {
    border: none;
    padding: 0.75rem 1rem;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.join-button:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    color: white;
}

.join-button:hover {
    transform: translateY(-2px);
}

.join-button:active {
    transform: translateY(0);
}

.join-button i {
    font-size: 1rem;
}

/* No Live Meetings Card */
.no-live-card {
    border-radius: 1rem;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.no-live-icon {
    font-size: 4rem;
    opacity: 0.6;
    color: #6c757d;
}

.no-live-title {
    font-weight: 700;
    color: #495057;
}

.no-live-description {
    opacity: 0.8;
    color: #6c757d;
}

/* No Upcoming Meetings Card */
.no-upcoming-card {
    border-radius: 1rem;
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
}

.no-upcoming-icon {
    font-size: 4rem;
    opacity: 0.6;
    color: #0284c7;
}

.no-upcoming-title {
    font-weight: 700;
    color: #0c4a6e;
}

.no-upcoming-description {
    opacity: 0.8;
    color: #075985;
}

/* No Past Meetings Card */
.no-past-card {
    border-radius: 1rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
}

.no-past-icon {
    font-size: 4rem;
    opacity: 0.6;
    color: #d97706;
}

.no-past-title {
    font-weight: 700;
    color: #92400e;
}

.no-past-description {
    opacity: 0.8;
    color: #b45309;
}

/* Upcoming Meetings Section */
.upcoming-meetings-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    display: block !important;
    visibility: visible !important;
}

.toggle-upcoming-btn {
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
    background: #e0f2fe;
    color: #0284c7;
    border: 1px solid #bae6fd;
}

.toggle-upcoming-btn:hover {
    background: #bae6fd;
    color: #0c4a6e;
}

/* Past Meetings Section */
.past-meetings-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 2px solid #e9ecef;
    display: block !important;
    visibility: visible !important;
}

.toggle-past-btn {
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fde68a;
}

.toggle-past-btn:hover {
    background: #fde68a;
    color: #92400e;
}

/* Utility Classes */
.cursor-pointer {
    cursor: pointer;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Enhanced Card Hover Effects */
.live-meeting-card:hover .meeting-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.live-meeting-card:hover .status-badge-enhanced {
    transform: scale(1.05);
}

/* Meeting Meta Info Enhancements */
.meeting-meta-info .info-text {
    font-size: 0.85rem;
    color: #475569;
    transition: color 0.2s ease;
}

.meeting-meta-info .info-text:hover {
    color: #1e293b;
}

.meeting-meta-info .info-text i {
    color: #667eea;
    width: 18px;
    text-align: center;
}

/* Badge Animation for Live Status */
.status-badge-ongoing .badge-icon {
    animation: pulse-glow 2s ease-in-out infinite;
}

@keyframes pulse-glow {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.1);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .live-meeting-card {
        margin-bottom: 1rem;
    }
    
    .header-title {
        font-size: 1.5rem;
    }
    
    .header-description {
        font-size: 0.875rem;
    }
}
</style>

