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
        'price' => 'السعر',
        'duration_hours' => 'المدة (ساعات)',
        'thumbnail' => 'رابط الصورة',
        'is_published' => 'منشور',
        'instructor' => 'المدرب',
    ],
    
    'levels' => [
        'beginner' => 'مبتدئ',
        'intermediate' => 'متوسط',
        'advanced' => 'متقدم',
    ],
    
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
        'published' => 'منشور',
        'draft' => 'مسودة',
    ],
    
    'instructor' => 'المدرب',
    'instructor_dashboard' => 'لوحة تحكم المدرب',
    'unknown_instructor' => 'مدرب غير معروف',
    'students' => 'الطلاب',
];

