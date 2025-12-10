<template>
    <AdminLayout :page-title="course?.translated_title || course?.title || t('admin.course_details')">
        <Head :title="course?.translated_title || course?.title || t('admin.course_details')" />
        
        <div class="space-y-6 min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 pb-8" :dir="direction">
            <!-- Page Header - Enhanced -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-black/5"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                <div class="relative z-10 text-white flex-1">
                    <div class="flex items-center gap-2 text-sm text-blue-100 mb-3">
                        <Link :href="route('admin.courses.index')" class="hover:text-white transition-colors font-medium">{{ t('admin.courses_management') || 'Courses Management' }}</Link>
                        <span>›</span>
                        <span class="text-white font-semibold">{{ course?.translated_title || course?.title }}</span>
                    </div>
                    <h1 class="text-4xl font-bold mb-3">{{ course?.translated_title || course?.title }}</h1>
                    <p class="text-blue-100 text-base">{{ t('admin.course_full_details') || 'View complete course information and statistics' }}</p>
                </div>
                <div class="relative z-10 flex items-center gap-3">
                    <Link :href="route('admin.courses.edit', course?.slug || course?.id)">
                        <Button 
                            :label="t('common.edit') || 'Edit'" 
                            icon="pi pi-pencil"
                            severity="secondary"
                            outlined
                            class="bg-white hover:bg-gray-50 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                        />
                    </Link>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ page.props.flash.success }}</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                <Card class="stat-card-hover">
                    <template #content>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="pi pi-users text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-1">{{ statistics?.total_students || 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">{{ t('admin.total_students') || 'Total Students' }}</div>
                    </template>
                </Card>

                <Card class="stat-card-hover">
                    <template #content>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="pi pi-check-circle text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-1">{{ statistics?.total_enrollments || 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">{{ t('admin.total_enrollments') || 'Total Enrollments' }}</div>
                    </template>
                </Card>

                <Card class="stat-card-hover">
                    <template #content>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="pi pi-book text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent mb-1">{{ statistics?.total_lessons || 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">{{ t('admin.total_lessons') || 'Total Lessons' }}</div>
                    </template>
                </Card>

                <Card class="stat-card-hover">
                    <template #content>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="pi pi-copy text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent mb-1">{{ statistics?.total_batches || 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">{{ t('admin.total_batches') || 'Total Batches' }}</div>
                    </template>
                </Card>

                <Card class="stat-card-hover">
                    <template #content>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="pi pi-question-circle text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent mb-1">{{ statistics?.total_questions || 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">{{ t('lessons.total_questions') || 'Total Questions' }}</div>
                    </template>
                </Card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Course Image -->
                    <Card>
                        <template #content>
                            <img 
                                :src="course?.thumbnail_url || course?.thumbnail || '/images/default-course.avif'" 
                                :alt="course?.translated_title || course?.title"
                                class="w-full h-80 object-cover rounded-lg"
                                @error="handleImageError($event)"
                            />
                        </template>
                    </Card>

                    <!-- Course Details -->
                    <Card>
                        <template #content>
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ t('admin.course_details') || 'Course Details' }}</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">{{ t('courses.fields.title') || 'Title' }}</label>
                                <p class="mt-1 text-gray-900">{{ course?.translated_title || course?.title }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-500">{{ t('courses.fields.description') || 'Description' }}</label>
                                <p class="mt-1 text-gray-600 leading-relaxed">{{ course?.translated_description || course?.description || '-' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('courses.fields.level') || 'Level' }}</label>
                                    <p class="mt-1">
                                        <Tag :value="t(`courses.levels.${course?.level}`) || course?.level" severity="info" class="capitalize" />
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('courses.fields.duration_hours') || 'Duration' }}</label>
                                    <p class="mt-1 text-gray-900 font-semibold">{{ course?.duration_hours || 0 }} {{ t('common.hours') || 'hours' }}</p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ t('courses.status.title') || 'Status' }}</label>
                                    <p class="mt-1">
                                        <Tag 
                                            :value="course?.is_published ? t('courses.status.published') : t('courses.status.draft')" 
                                            :severity="course?.is_published ? 'success' : 'warning'" 
                                        />
                                    </p>
                                </div>
                            </div>
                        </div>
                        </template>
                    </Card>

                    <!-- Sections & Lessons - Enhanced -->
                    <Card class="course-content-card">
                        <template #header>
                            <div class="flex items-center justify-between p-6 pb-0">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2">
                                        <i class="pi pi-book text-blue-600"></i>
                                        {{ t('admin.course_content') || 'Course Content' }}
                                    </h2>
                                    <p class="text-sm text-gray-500">{{ t('admin.course_content_description') || 'Manage sections, lessons, and questions' }}</p>
                                </div>
                                <Link
                                    :href="route('admin.courses.sections.index', course?.slug || course?.id)"
                                >
                                    <Button 
                                        :label="t('admin.manage_sections') || 'Manage Sections'" 
                                        icon="pi pi-cog"
                                        text
                                        size="small"
                                        class="hover:bg-blue-50"
                                    />
                                </Link>
                            </div>
                        </template>
                        <template #content>

                        <div v-if="course?.sections && course.sections.length > 0" class="space-y-4">
                            <Panel 
                                v-for="section in course.sections" 
                                :key="section.id"
                                :header="section.translated_title || section.title"
                                :toggleable="true"
                                class="section-panel"
                            >
                                <template #icons>
                                    <Badge :value="section.lessons?.length || 0" severity="info" class="mr-2" />
                                </template>
                                <p v-if="section.translated_description || section.description" class="text-sm text-gray-600 mb-4 leading-relaxed">
                                    {{ section.translated_description || section.description }}
                                </p>
                                <div v-if="section.lessons && section.lessons.length > 0" class="space-y-3">
                                    <div v-for="lesson in section.lessons" :key="lesson.id" class="border border-gray-200 rounded-xl p-4 hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-indigo-50/30 hover:border-blue-300 transition-all duration-300 group">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-md group-hover:shadow-lg group-hover:scale-110 transition-all duration-300">
                                                <i class="pi pi-play-circle text-white text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ lesson.translated_title || lesson.title }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <Link 
                                                    v-if="lesson.id"
                                                    :href="route('admin.courses.lessons.questions.create', [course?.slug || course?.id, lesson.id])"
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                                    :title="t('admin.add_question') || 'Add Question'"
                                                >
                                                    <Button 
                                                        icon="pi pi-plus-circle"
                                                        severity="success"
                                                        text
                                                        rounded
                                                        size="small"
                                                    />
                                                </Link>
                                                <Link 
                                                    v-if="lesson.id && lesson.questions && lesson.questions.length > 0"
                                                    :href="route('admin.courses.lessons.questions.index', [course?.slug || course?.id, lesson.id])"
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                                    :title="t('admin.view_questions') || 'View Questions'"
                                                >
                                                    <Button 
                                                        icon="pi pi-eye"
                                                        severity="info"
                                                        text
                                                        rounded
                                                        size="small"
                                                    />
                                                </Link>
                                                <Badge v-if="lesson.questions && lesson.questions.length > 0" :value="lesson.questions.length" severity="info" class="font-semibold" />
                                            </div>
                                        </div>
                                        <div v-if="lesson.questions && lesson.questions.length > 0" class="pl-13 mt-3 space-y-2 border-t border-gray-100 pt-3">
                                            <div v-for="question in lesson.questions" :key="question.id" class="text-sm text-gray-600 flex items-center gap-2 hover:text-gray-900 transition-colors">
                                                <i class="pi pi-circle-fill text-xs text-blue-400"></i>
                                                <span class="flex-1">{{ question.translated_question || question.question }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Panel>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <i class="pi pi-inbox text-4xl text-gray-300 mb-4"></i>
                            <p>{{ t('admin.no_sections') || 'No sections added yet' }}</p>
                        </div>
                        </template>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions - Enhanced -->
                    <Card class="quick-actions-card">
                        <template #header>
                            <div class="p-6 pb-0">
                                <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                                    <i class="pi pi-bolt text-blue-600"></i>
                                    {{ t('admin.quick_actions') || 'Quick Actions' }}
                                </h3>
                                <p class="text-sm text-gray-500">{{ t('admin.manage_course_content') || 'Manage course content and structure' }}</p>
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-3 p-2">
                                <Link
                                    :href="route('admin.courses.sections.create', course?.slug || course?.id)"
                                    class="block w-full group"
                                >
                                    <Button 
                                        :label="t('admin.add_section') || 'Add Section'" 
                                        icon="pi pi-plus"
                                        class="w-full justify-start hover:scale-105 transition-transform duration-200"
                                        outlined
                                        severity="info"
                                    />
                                </Link>
                                <Link
                                    :href="route('admin.courses.lessons.create', course?.slug || course?.id)"
                                    class="block w-full group"
                                >
                                    <Button 
                                        :label="t('admin.add_lesson') || 'Add Lesson'" 
                                        icon="pi pi-book"
                                        class="w-full justify-start hover:scale-105 transition-transform duration-200"
                                        outlined
                                        severity="secondary"
                                    />
                                </Link>
                                <Link
                                    v-if="course?.sections && course.sections.length > 0 && course.sections.some(s => s.lessons && s.lessons.length > 0)"
                                    :href="route('admin.courses.lessons.index', course?.slug || course?.id)"
                                    class="block w-full group"
                                >
                                    <Button 
                                        :label="t('admin.manage_lessons') || 'Manage Lessons'" 
                                        icon="pi pi-list"
                                        class="w-full justify-start hover:scale-105 transition-transform duration-200"
                                        outlined
                                        severity="warning"
                                    />
                                </Link>
                                <Link
                                    :href="route('admin.courses.sections.index', course?.slug || course?.id)"
                                    class="block w-full group"
                                >
                                    <Button 
                                        :label="t('admin.manage_sections') || 'Manage Sections'" 
                                        icon="pi pi-cog"
                                        class="w-full justify-start hover:scale-105 transition-transform duration-200"
                                        outlined
                                        severity="success"
                                    />
                                </Link>
                                <Link
                                    :href="route('admin.courses.batches.index', course?.slug || course?.id)"
                                    class="block w-full group"
                                >
                                    <Button 
                                        :label="t('admin.manage_batches') || 'Manage Batches'" 
                                        icon="pi pi-users"
                                        class="w-full justify-start hover:scale-105 transition-transform duration-200"
                                        outlined
                                        severity="help"
                                    />
                                </Link>
                            </div>
                        </template>
                    </Card>

                    <!-- Batches -->
                    <Card v-if="course?.batches && course.batches.length > 0">
                        <template #header>
                            <h3 class="text-lg font-bold text-gray-900 p-6 pb-0">{{ t('admin.batches') || 'Batches' }}</h3>
                        </template>
                        <template #content>
                            <div class="space-y-3">
                                <div v-for="batch in course.batches" :key="batch.id" class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:bg-blue-50/30 transition-all duration-300">
                                    <div class="font-bold text-gray-900 mb-1">{{ batch.name }}</div>
                                    <div class="text-sm text-gray-600">
                                        {{ t('admin.instructor') || 'Instructor' }}: <Tag :value="batch.instructor?.name || '-'" severity="info" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { computed } from 'vue';

const props = defineProps({
    course: Object,
    statistics: Object,
});

const { t } = useTranslation();
const { route } = useRoute();
const page = usePage();

const direction = computed(() => page.props.direction || 'ltr');

const handleImageError = (event) => {
    event.target.src = '/images/default-course.avif';
};
</script>

<style scoped>
:deep(.p-card) {
    border-radius: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.stat-card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15), 0 4px 8px rgba(0, 0, 0, 0.1);
}

.quick-actions-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

.quick-actions-card:hover {
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12), 0 4px 8px rgba(0, 0, 0, 0.08);
}

.course-content-card {
    background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

:deep(.p-panel) {
    border-radius: 0.75rem;
    border: 1px solid rgba(229, 231, 235, 0.8);
    margin-bottom: 1rem;
}

:deep(.p-panel-header) {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
    border-bottom: 1px solid rgba(229, 231, 235, 0.8);
    font-weight: 600;
}

:deep(.p-panel-content) {
    padding: 1.25rem;
}

:deep(.p-button) {
    font-weight: 500;
    transition: all 0.3s ease;
}

:deep(.p-button:hover) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

:deep(.p-tag) {
    font-weight: 600;
    padding: 0.375rem 0.75rem;
}
</style>

