<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How do I enroll in a course?',
                'question_ar' => 'كيف أسجل في دورة؟',
                'answer' => 'Simply click on the "Enroll" button on any course page. No payment is required.',
                'answer_ar' => 'ما عليك سوى النقر على زر "التسجيل" في صفحة أي دورة. لا يلزم الدفع.',
                'order' => 1,
            ],
            [
                'question' => 'Are the courses free?',
                'question_ar' => 'هل الدورات مجانية؟',
                'answer' => 'Yes, all courses are currently free to enroll. You can start learning right away!',
                'answer_ar' => 'نعم، جميع الدورات مجانية حاليًا للتسجيل. يمكنك البدء في التعلم على الفور!',
                'order' => 2,
            ],
            [
                'question' => 'Can I access courses on mobile?',
                'question_ar' => 'هل يمكنني الوصول إلى الدورات على الهاتف المحمول؟',
                'answer' => 'Yes, our platform is fully responsive and works great on mobile devices.',
                'answer_ar' => 'نعم، منصتنا متجاوبة بالكامل وتعمل بشكل رائع على الأجهزة المحمولة.',
                'order' => 3,
            ],
            [
                'question' => 'How do I track my progress?',
                'question_ar' => 'كيف أتابع تقدمي؟',
                'answer' => 'Your progress is automatically tracked as you complete lessons. You can view it in your dashboard.',
                'answer_ar' => 'يتم تتبع تقدمك تلقائيًا عند إكمال الدروس. يمكنك عرضه في لوحة التحكم الخاصة بك.',
                'order' => 4,
            ],
        ];

        foreach ($faqs as $faq) {
            \App\Models\FAQ::create([
                ...$faq,
                'is_active' => true,
            ]);
        }
    }
}
