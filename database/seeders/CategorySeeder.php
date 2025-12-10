<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Development', 'name_ar' => 'تطوير الويب', 'icon' => '💻', 'order' => 1],
            ['name' => 'Mobile Development', 'name_ar' => 'تطوير الموبايل', 'icon' => '📱', 'order' => 2],
            ['name' => 'Data Science', 'name_ar' => 'علوم البيانات', 'icon' => '📊', 'order' => 3],
            ['name' => 'Design', 'name_ar' => 'التصميم', 'icon' => '🎨', 'order' => 4],
            ['name' => 'Marketing', 'name_ar' => 'التسويق', 'icon' => '📈', 'order' => 5],
            ['name' => 'Business', 'name_ar' => 'الأعمال', 'icon' => '💼', 'order' => 6],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create([
                ...$category,
                'slug' => \Illuminate\Support\Str::slug($category['name']),
                'description' => "Learn {$category['name']} skills",
                'description_ar' => "تعلم مهارات {$category['name_ar']}",
                'is_active' => true,
            ]);
        }
    }
}
