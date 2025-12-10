# Attendance System Implementation

## Overview
This document describes the automatic attendance tracking system implemented for the LMS platform.

## Attendance Rules

### For Students (Auto-Attendance)

The system automatically marks attendance based on lesson type and student actions:

#### 1. Text Lesson (No Video, No Questions)
- **Trigger**: When student opens the lesson
- **Attendance**: Automatically marked as "Present"
- **Progress**: Lesson is marked as completed
- **Note**: "Auto-marked: Lesson opened"

#### 2. Video Lesson (Uploaded File)
- **Trigger**: After watching 80% of the video
- **Attendance**: Automatically marked as "Present"
- **Progress**: Lesson is marked as completed
- **Note**: "Auto-marked: Watched X% of video"
- **Requirement**: Student must watch at least 80% of the video

#### 3. Video Lesson (Link: YouTube/Google Drive)
- **Trigger**: When student opens the lesson
- **Attendance**: Automatically marked as "Present"
- **Progress**: Lesson is marked as completed
- **Note**: "Auto-marked: Lesson opened"
- **No Watch Requirement**: Links don't require 80% viewing

#### 4. Lesson with Questions
- **Trigger**: After answering all questions
- **Attendance**: Automatically marked as "Present"
- **Progress**: Lesson is marked as completed
- **Note**: "Auto-marked: All questions answered"
- **Requirement**: Student must answer ALL questions

#### 5. Combined (Video + Questions)
- **Requirement**: Student must satisfy BOTH conditions:
  - Watch 80% of uploaded video OR open video link
  - Answer all questions
- **Attendance**: Marked after meeting requirements
- **Progress**: Lesson marked as completed

### For Instructors (Manual Attendance)

#### Priority System
- **Instructor marking ALWAYS takes priority** over auto-marking
- Once instructor marks attendance, auto-marking is disabled for that lesson/student
- Instructor can override any auto-marked attendance

#### Attendance Statuses
1. **Present**: Student attended the lesson
2. **Absent**: Student did not attend
3. **Late**: Student attended but was late
4. **Excused**: Student was excused from attendance

#### Effects on Progress
- **Present/Late/Excused**: Lesson is marked as completed, progress increases
- **Absent**: Lesson is NOT marked as completed, no progress increase

## Implementation Details

### Backend Service: `LessonAttendanceService`

Located at: `app/Services/LessonAttendanceService.php`

#### Key Methods:

1. **`autoMarkAttendance()`**
   - Called when student opens a lesson
   - Checks lesson type and applies appropriate rules
   - Only marks if instructor hasn't already marked

2. **`markAttendanceAfterVideoWatch()`**
   - Called after student watches 80% of uploaded video
   - Marks attendance as present
   - Updates progress

3. **`markAttendanceAfterQuestions()`**
   - Called after student answers all questions
   - Marks attendance as present
   - Updates progress

4. **`instructorMarkAttendance()`**
   - Used by instructors to manually mark attendance
   - Overrides any existing auto-marked attendance
   - Can mark as present, absent, late, or excused

### Controller Updates

#### `CoursePlayerController`
- Added `LessonAttendanceService` dependency
- Auto-marks attendance when student opens lesson in `show()` method
- Updated `markLessonCompleted()` to use attendance service

#### `Instructor\LessonController`
- Added `LessonAttendanceService` dependency
- Updated `markAttendance()` to use attendance service
- Instructor marking takes priority over auto-marking

### Frontend Updates

#### `Player.vue`
- Removed automatic completion API calls for text lessons and video links
- Only tracks watch percentage for uploaded videos
- Sends completion request when:
  - Uploaded video reaches 80%
  - All questions are answered (for question-only or video link + questions)

## Database Schema

### `lesson_attendances` Table

```php
Schema::create('lesson_attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
    $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
    $table->foreignId('marked_by')->constrained('users')->onDelete('cascade'); // Who marked it
    $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
    $table->text('notes')->nullable();
    $table->timestamp('attended_at')->nullable();
    $table->timestamps();
    
    // One attendance record per lesson per student per batch
    $table->unique(['lesson_id', 'student_id', 'batch_id']);
});
```

## Benefits

### For Students
- Automatic attendance tracking - no manual check-in required
- Fair system based on actual engagement (watching videos, answering questions)
- Clear requirements for each lesson type
- Instant progress updates

### For Instructors
- Full control with manual override capability
- View attendance history for all students
- Mark attendance with notes
- Can set status as present, absent, late, or excused

### For Administrators
- Accurate attendance records
- Automated system reduces errors
- Clean separation between attendance and progress
- Audit trail with timestamps and notes

## Progress Calculation

Progress is calculated as:
```
Progress = (Completed Lessons / Total Lessons) × 100
```

Where "Completed Lessons" includes:
- Lessons auto-marked via attendance system
- Lessons marked as present/late/excused by instructor
- Does NOT include lessons marked as absent

## API Endpoints

### Student Endpoints
- `POST /courses/{course}/lessons/{lesson}/complete`
  - Marks lesson as completed (with watch percentage for videos)
  - Called automatically by frontend when requirements are met

### Instructor Endpoints
- `POST /instructor/courses/{course}/lessons/{lesson}/attendance`
  - Marks attendance for students manually
  - Overrides any auto-marked attendance

## Testing Checklist

### Student Tests
- [ ] Open text lesson → Auto-marked as present
- [ ] Open video link lesson → Auto-marked as present
- [ ] Watch uploaded video 80% → Auto-marked as present
- [ ] Answer all questions → Auto-marked as present
- [ ] Uploaded video + questions → Must do both
- [ ] Check progress updates correctly
- [ ] Check attendance shows in sidebar

### Instructor Tests
- [ ] View attendance list
- [ ] Mark attendance manually
- [ ] Override auto-marked attendance
- [ ] Mark as present/absent/late/excused
- [ ] Add notes to attendance
- [ ] Check progress updates for students

### Edge Cases
- [ ] Student opens lesson multiple times → No duplicate attendance
- [ ] Instructor marks after auto-mark → Instructor version wins
- [ ] Student in multiple batches → Attendance for all batches
- [ ] Lesson with no requirements → Auto-marked immediately

## Migration Notes

If you already have existing attendance data:
1. Auto-marked records have `marked_by` = student_id
2. Instructor-marked records have `marked_by` = instructor_id
3. System respects existing attendance records
4. No data migration needed - system is backward compatible

