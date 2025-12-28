<?php

return [
    'title' => 'الدورات',
    'create' => 'إنشاء دورة',
    'edit' => 'تعديل الدورة',
    'show' => 'تفاصيل الدورة',
    'created_successfully' => 'تم إنشاء الدورة بنجاح!',
    'updated_successfully' => 'تم تحديث الدورة بنجاح!',
    'deleted_successfully' => 'تم حذف الدورة بنجاح!',
    
    'fields' => [
        'title' => 'العنوان',
        'title_ar' => 'العنوان (عربي)',
        'description' => 'الوصف',
        'description_ar' => 'الوصف (عربي)',
        'level' => 'المستوى',
        'course_type' => 'نوع الدورة',
        'track' => 'المسار',
        'track_id' => 'المسار',
        'price' => 'السعر',
        'duration_hours' => 'المدة (ساعات)',
        'thumbnail' => 'رابط الصورة',
        'is_published' => 'منشور',
        'instructor' => 'المدرب',
    ],
    
    'types' => [
        'course' => 'دورة',
        'recurring' => 'مقرر',
    ],
    
    'placeholders' => [
        'title' => 'أدخل عنوان الدورة',
        'description' => 'أدخل وصف الدورة',
    ],
    
    'basic_info' => 'المعلومات الأساسية',
    'details' => 'تفاصيل الدورة',
    'thumbnail' => 'صورة الدورة المصغرة',
    
    'levels' => [
        'beginner' => 'مبتدئ',
        'intermediate' => 'متوسط',
        'advanced' => 'متقدم',
    ],
    
    'all_levels' => 'جميع المستويات',
    
    'validation' => [
        'title_required' => 'عنوان الدورة مطلوب',
        'level_required' => 'مستوى الدورة مطلوب',
        'price_required' => 'سعر الدورة مطلوب',
        'duration_required' => 'مدة الدورة مطلوبة',
        'thumbnail_image' => 'يجب أن تكون الصورة المصغرة ملف صورة',
        'thumbnail_mimes' => 'يجب أن تكون الصورة المصغرة ملف jpeg أو jpg أو png أو gif أو webp',
        'thumbnail_max' => 'يجب ألا يتجاوز حجم الصورة المصغرة 2 ميجابايت',
        'thumbnail_url' => 'يجب أن يكون رابط الصورة المصغرة رابطاً صالحاً',
    ],
    
    'actions' => [
        'view' => 'عرض',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'create' => 'إنشاء',
        'update' => 'تحديث',
        'cancel' => 'إلغاء',
        'back' => 'رجوع',
        'continue' => 'متابعة التعلم',
        'view_content' => 'عرض محتوى الدورة',
        'to_view' => 'للعرض',
    ],
    
    'status' => [
        'title' => 'الحالة',
        'published' => 'منشور',
        'draft' => 'مسودة',
    ],
    
    'all_status' => 'جميع الحالات',
    
    'no_courses' => 'لم يتم العثور على دورات',
    'no_courses_description' => 'ابدأ بإنشاء دورتك الأولى',
    
    'confirm_delete' => 'هل أنت متأكد أنك تريد حذف هذه الدورة؟ لا يمكن التراجع عن هذا الإجراء.',
    'delete_title' => 'حذف الدورة',
    'delete_error' => 'فشل في حذف الدورة',
    
    'instructor' => 'المدرب',
    'instructor_dashboard' => 'لوحة تحكم المدرب',
    'unknown_instructor' => 'مدرب غير معروف',
    'students' => 'الطلاب',
];

