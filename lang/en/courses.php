<?php

return [
    'title' => 'Courses',
    'create' => 'Create Course',
    'edit' => 'Edit Course',
    'show' => 'Course Details',
    'created_successfully' => 'Course created successfully!',
    'updated_successfully' => 'Course updated successfully!',
    'deleted_successfully' => 'Course deleted successfully!',
    
    'fields' => [
        'title' => 'Title',
        'title_ar' => 'Title (Arabic)',
        'description' => 'Description',
        'description_ar' => 'Description (Arabic)',
        'level' => 'Level',
        'price' => 'Price',
        'duration_hours' => 'Duration (Hours)',
        'thumbnail' => 'Thumbnail URL',
        'is_published' => 'Published',
        'instructor' => 'Instructor',
    ],
    
    'levels' => [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
    ],
    
    'validation' => [
        'title_required' => 'Course title is required',
        'level_required' => 'Course level is required',
        'price_required' => 'Course price is required',
        'duration_required' => 'Course duration is required',
        'thumbnail_image' => 'Thumbnail must be an image file',
        'thumbnail_mimes' => 'Thumbnail must be a jpeg, jpg, png, gif, or webp file',
        'thumbnail_max' => 'Thumbnail size must not exceed 2MB',
        'thumbnail_url' => 'Thumbnail URL must be a valid URL',
    ],
    
    'actions' => [
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'create' => 'Create',
        'update' => 'Update',
        'cancel' => 'Cancel',
        'back' => 'Back',
        'continue' => 'Continue Learning',
        'view_content' => 'View Course Content',
        'to_view' => 'to View',
    ],
    
    'status' => [
        'published' => 'Published',
        'draft' => 'Draft',
    ],
    
    'instructor' => 'Instructor',
    'instructor_dashboard' => 'Instructor Dashboard',
    'unknown_instructor' => 'Unknown Instructor',
    'students' => 'Students',
];

