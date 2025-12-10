<template>
    <component :is="layout">
        <div class="min-h-screen bg-slate-100" :dir="direction">
            <!-- Top Bar -->
            <div class="bg-slate-800 text-white py-2 text-sm">
                <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            +1(323) 555-9876
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            info@lms.com
                        </span>
                    </div>
                </div>
            </div>

            <!-- Hero Section -->
            <div class="relative bg-gradient-to-r from-slate-700 to-slate-800 py-16 overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-pattern"></div>
                <div class="max-w-7xl mx-auto px-4 relative z-10">
                    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        </div>
                        <nav class="text-sm text-gray-500 mb-2">
                            <Link :href="route('welcome')" class="hover:text-blue-600">LMS</Link>
                            <span class="mx-2">›</span>
                            <span class="text-gray-900">{{ course.translated_title || course.title }}</span>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900">{{ course.translated_title || course.title }}</h1>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-8 px-4">
                <!-- Success Alert -->
                <div v-if="page.props.flash?.success" class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-blue-800 font-medium">{{ page.props.flash.success }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Course Image -->
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <img 
                                :src="getCourseImage()" 
                                :alt="course.translated_title || course.title"
                                class="w-full h-80 object-cover"
                                @error="handleImageError($event)"
                            />
                        </div>

                        <!-- Course Info -->
                        <div class="bg-white rounded-xl shadow-sm p-8">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex text-yellow-400">
                                    <svg v-for="i in 5" :key="i" class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-600">(4.8 rating)</span>
                            </div>

                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ course.translated_title || course.title }}</h1>
                            <p class="text-gray-600 mb-6 leading-relaxed">{{ course.translated_description || course.description }}</p>

                            <!-- Course Details Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-6 border-t border-gray-200">
                                <div class="text-center p-4 bg-slate-50 rounded-xl">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-1">{{ t('courses.fields.instructor') }}</p>
                                    <p class="font-semibold text-gray-900 text-sm">{{ course.instructor?.name || t('courses.unknown_instructor') }}</p>
                                </div>
                                <div class="text-center p-4 bg-slate-50 rounded-xl">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-1">{{ t('courses.fields.level') }}</p>
                                    <p class="font-semibold text-gray-900 text-sm capitalize">{{ t('courses.levels.' + course.level) }}</p>
                                </div>
                                <div class="text-center p-4 bg-slate-50 rounded-xl">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-1">{{ t('courses.fields.duration') }}</p>
                                    <p class="font-semibold text-gray-900 text-sm">{{ course.duration_hours || 0 }} {{ t('courses.hours') }}</p>
                                </div>
                                <div class="text-center p-4 bg-slate-50 rounded-xl">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-1">{{ t('courses.fields.students') }}</p>
                                    <p class="font-semibold text-gray-900 text-sm">{{ course.students_count || 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sections and Lessons List -->
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <button
                                @click="toggleCourseContent"
                                class="w-full px-8 py-6 hover:bg-gray-50 transition-colors flex items-center justify-between text-left"
                            >
                                <div class="flex items-center gap-4 flex-1">
                                    <h2 class="text-2xl font-bold text-gray-900">{{ t('sections.title') }}</h2>
                                    <span class="text-sm text-gray-500 bg-slate-100 px-3 py-1 rounded-full">
                                        <template v-if="course.sections && course.sections.length > 0">
                                            {{ course.sections.length }} {{ t('sections.total') }}, 
                                            {{ getTotalLessonsCount() }} {{ t('lessons.total') }}
                                        </template>
                                        <template v-else>
                                            {{ course.lessons?.length || 0 }} {{ t('lessons.total') }}
                                        </template>
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <Link
                                        v-if="showEditButtons"
                                        @click.stop
                                        :href="route('instructor.sections.index', course.slug || course.id)"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors flex items-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        {{ t('sections.actions.manage') }}
                                    </Link>
                                    <svg 
                                        class="w-6 h-6 text-gray-500 flex-shrink-0 transition-transform duration-200"
                                        :class="{ 'rotate-180': isCourseContentExpanded }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>
                            
                            <transition
                                enter-active-class="transition-all duration-300 ease-out"
                                enter-from-class="opacity-0 max-h-0"
                                enter-to-class="opacity-100 max-h-[5000px]"
                                leave-active-class="transition-all duration-300 ease-in"
                                leave-from-class="opacity-100 max-h-[5000px]"
                                leave-to-class="opacity-0 max-h-0"
                            >
                                <div v-show="isCourseContentExpanded" class="overflow-hidden px-8 pb-8">
                            
                            <!-- Sections with Lessons -->
                            <div v-if="course.sections && course.sections.length > 0" class="space-y-4">
                                <div
                                    v-for="section in course.sections"
                                    :key="section.id"
                                    class="border border-gray-200 rounded-xl overflow-hidden"
                                >
                                    <button
                                        @click="toggleSection(section.id)"
                                        class="w-full bg-gray-50 px-6 py-4 border-b border-gray-200 hover:bg-gray-100 transition-colors flex items-center justify-between text-left"
                                    >
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-1">
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ section.translated_title || section.title }}
                                                </h3>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                                    {{ section.lessons?.length || 0 }} {{ t('lessons.total') }}
                                                </span>
                                            </div>
                                            <p v-if="section.translated_description || section.description" class="text-sm text-gray-600">
                                                {{ section.translated_description || section.description }}
                                            </p>
                                        </div>
                                        <svg 
                                            class="w-5 h-5 text-gray-500 flex-shrink-0 ml-4 transition-transform duration-200"
                                            :class="{ 'rotate-180': expandedSections[section.id] }"
                                            fill="none" 
                                            stroke="currentColor" 
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <transition
                                        enter-active-class="transition-all duration-300 ease-out"
                                        enter-from-class="opacity-0 max-h-0"
                                        enter-to-class="opacity-100 max-h-[2000px]"
                                        leave-active-class="transition-all duration-300 ease-in"
                                        leave-from-class="opacity-100 max-h-[2000px]"
                                        leave-to-class="opacity-0 max-h-0"
                                    >
                                        <div v-show="expandedSections[section.id]" class="overflow-hidden">
                                            <div v-if="section.lessons && section.lessons.length > 0" class="p-6 space-y-3">
                                                <div
                                                    v-for="(lesson, index) in section.lessons"
                                                    :key="lesson.id"
                                                    class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-md transition-all group"
                                                >
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center flex-1">
                                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-sm mr-4 group-hover:bg-blue-700 transition-colors">
                                                                {{ index + 1 }}
                                                            </div>
                                                            <div class="flex-1">
                                                                <h4 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ lesson.translated_title || lesson.title }}</h4>
                                                                <div class="flex items-center gap-4 mt-1">
                                                                    <span class="text-xs text-gray-500 flex items-center">
                                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                        {{ lesson.duration_minutes }} {{ t('lessons.fields.duration_minutes') }}
                                                                    </span>
                                                                    <span v-if="lesson.type" class="text-xs text-gray-500 capitalize">
                                                                        {{ t('lessons.types.' + lesson.type) || lesson.type }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex-shrink-0 flex items-center gap-2">
                                                            <Link
                                                                v-if="showEditButtons"
                                                                :href="route('instructor.lessons.edit', [course.slug || course.id, lesson.id])"
                                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors"
                                                            >
                                                                {{ t('lessons.actions.edit') }}
                                                            </Link>
                                                            <Link
                                                                v-else
                                                                :href="route('lessons.show', [course.slug || course.id, lesson.id])"
                                                                class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg text-sm font-medium transition-colors flex items-center"
                                                            >
                                                                {{ t('lessons.actions.view') }}
                                                                <svg class="w-4 h-4 mr-1" :class="direction === 'rtl' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                                </svg>
                                                            </Link>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else class="p-6 text-center text-gray-500">
                                                {{ t('sections.no_lessons') }}
                                            </div>
                                        </div>
                                    </transition>
                                </div>
                            </div>
                            <!-- Fallback to lessons if no sections -->
                            <div v-else-if="course.lessons && course.lessons.length > 0" class="space-y-3">
                                <div
                                    v-for="(lesson, index) in course.lessons"
                                    :key="lesson.id"
                                    class="border border-gray-200 rounded-xl p-5 hover:border-blue-500 hover:shadow-md transition-all group"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-sm mr-4 group-hover:bg-blue-700 transition-colors">
                                                {{ index + 1 }}
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ lesson.translated_title || lesson.title }}</h3>
                                                <div class="flex items-center gap-4 mt-1">
                                                    <span class="text-xs text-gray-500 flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ lesson.duration_minutes }} {{ t('lessons.fields.duration_minutes') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 flex items-center gap-2">
                                            <Link
                                                v-if="showEditButtons"
                                                :href="route('lessons.edit', [course.slug || course.id, lesson.id])"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors"
                                            >
                                                {{ t('lessons.actions.edit') }}
                                            </Link>
                                            <Link
                                                v-else
                                                :href="route('lessons.show', [course.slug || course.id, lesson.id])"
                                                class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg text-sm font-medium transition-colors flex items-center"
                                            >
                                                {{ t('lessons.actions.view') }}
                                                <svg class="w-4 h-4 mr-1" :class="direction === 'rtl' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-gray-500 text-lg mb-4">{{ t('lessons.no_lessons') }}</p>
                                <Link
                                    v-if="showEditButtons"
                                    :href="route('lessons.create', course.slug || course.id)"
                                    class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                                >
                                    {{ t('lessons.actions.add_first') }}
                                </Link>
                            </div>
                                </div>
                            </transition>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                            <!-- Admin Edit Buttons -->
                            <div v-if="showEditButtons" class="space-y-3 mb-6">
                                <Link
                                    :href="route('courses.edit', { course: course.slug || course.id })"
                                    class="w-full block text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                                >
                                    {{ t('courses.actions.edit') }}
                                </Link>
                                <Link
                                    :href="route('lessons.create', course.slug || course.id)"
                                    class="w-full block text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                                >
                                    {{ t('lessons.actions.add_first') }}
                                </Link>
                            </div>
                            
                            <!-- Instructor: Show course content access -->
                            <div v-if="isInstructor && course.lessons?.length > 0" class="space-y-3">
                                <Link
                                    :href="route('courses.play', course.slug || course.id)"
                                    class="w-full block text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                                >
                                    {{ t('courses.actions.view_content') }}
                                </Link>
                            </div>
                            
                            <!-- Student: Show course content if enrolled -->
                            <div v-else-if="auth && isEnrolled && course.lessons?.length > 0" class="space-y-3">
                                <Link
                                    :href="route('courses.play', course.slug || course.id)"
                                    class="w-full block text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                                >
                                    {{ t('courses.actions.continue') }}
                                </Link>
                            </div>
                            
                            <!-- Guest: Show login to access -->
                            <div v-else-if="!auth">
                                <Link
                                    :href="route('login')"
                                    class="w-full block text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors"
                                >
                                    {{ t('common.login') }} {{ t('courses.actions.to_view') }}
                                </Link>
                            </div>

                            <!-- Batches Section -->
                            <div v-if="batches && batches.length > 0" class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="font-semibold text-gray-900 mb-4">{{ t('courses.batches') }}</h3>
                                <div class="space-y-3">
                                    <div
                                        v-for="batch in batches"
                                        :key="batch.id"
                                        class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-colors"
                                    >
                                        <h4 class="font-semibold text-gray-900 mb-1">{{ batch.name }}</h4>
                                        <p v-if="batch.description" class="text-sm text-gray-600 mb-2">{{ batch.description }}</p>
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span v-if="batch.instructor" class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ batch.instructor.name }}
                                            </span>
                                            <span v-if="batch.start_date" class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ formatDate(batch.start_date) }}
                                            </span>
                                        </div>
                                        <span v-if="isInstructor && batch.enrollments_count !== undefined" class="mt-2 inline-block text-xs text-gray-600">
                                            {{ batch.enrollments_count }} {{ t('courses.students') || 'students' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="font-semibold text-gray-900 mb-4">{{ t('courses.includes') }}</h3>
                                <ul class="space-y-3 text-sm text-gray-600">
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ course.duration_hours || 0 }} {{ t('courses.hours') }} {{ t('courses.on_demand_video') }}
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ course.lessons?.length || 0 }} {{ t('lessons.title') }}
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        {{ t('courses.full_lifetime_access') }}
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                        {{ t('courses.certificate') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                                    </div>
                                </div>

            <!-- Newsletter Section -->
            <div class="bg-slate-800 py-8 mt-12">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📬</span>
                            <div>
                                <h3 class="text-white font-semibold">{{ t('newsletter.title') }}</h3>
                                <p class="text-gray-400 text-sm">{{ t('newsletter.subtitle') }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2 w-full md:w-auto">
                            <div class="relative flex-1 md:w-80">
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <input type="email" :placeholder="t('newsletter.placeholder')" class="w-full pr-10 pl-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                {{ t('newsletter.join') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructor Dashboard Section -->
            <div v-if="isInstructor && instructorData" class="max-w-7xl mx-auto py-8 px-4">
                <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ t('courses.instructor_dashboard') }}
                        </h2>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-sm opacity-90 mb-1">{{ t('courses.total_students') }}</p>
                            <p class="text-3xl font-bold">{{ instructorData.statistics.total_students }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm opacity-90 mb-1">{{ t('courses.completed_students') }}</p>
                            <p class="text-3xl font-bold">{{ instructorData.statistics.completed_students }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <p class="text-sm opacity-90 mb-1">{{ t('courses.average_progress') }}</p>
                            <p class="text-3xl font-bold">{{ instructorData.statistics.average_progress }}%</p>
                        </div>

                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm opacity-90 mb-1">{{ t('courses.total_questions') }}</p>
                            <p class="text-3xl font-bold">{{ instructorData.statistics.total_questions }}</p>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            <button
                                @click="instructorTab = 'students'"
                                :class="instructorTab === 'students' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            >
                                {{ t('courses.students') }} ({{ instructorData.students.length }})
                            </button>
                            <button
                                @click="instructorTab = 'lessons'"
                                :class="instructorTab === 'lessons' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            >
                                {{ t('lessons.title') }} ({{ instructorData.lessons.length }})
                            </button>
                        </nav>
                    </div>

                    <!-- Students Tab -->
                    <div v-if="instructorTab === 'students'" class="space-y-4">
                        <div v-if="instructorData.students.length === 0" class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p>{{ t('courses.no_students') }}</p>
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="student in instructorData.students"
                                :key="student.id"
                                class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 flex-1">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                            {{ getInitials(student.name) }}
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ student.name }}</h3>
                                            <p class="text-sm text-gray-500">{{ student.email }}</p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ t('courses.enrolled_at') }}: {{ formatDate(student.enrolled_at) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="mb-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium"
                                                :class="{
                                                    'bg-blue-100 text-blue-700': student.status === 'completed',
                                                    'bg-blue-100 text-blue-700': student.status === 'enrolled',
                                                    'bg-red-100 text-red-700': student.status === 'dropped',
                                                }"
                                            >
                                                {{ t('courses.status.' + student.status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                                <div
                                                    class="bg-blue-600 h-2 rounded-full transition-all"
                                                    :style="{ width: student.progress + '%' }"
                                                ></div>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-700">{{ student.progress }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lessons Tab -->
                    <div v-if="instructorTab === 'lessons'" class="space-y-4">
                        <div v-if="instructorData.lessons.length === 0" class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p>{{ t('lessons.no_lessons') }}</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="lesson in instructorData.lessons"
                                :key="lesson.id"
                                class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow"
                            >
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                                {{ t('lessons.lesson') }} {{ lesson.order }}
                                            </span>
                                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium capitalize">
                                                {{ t('lessons.types.' + lesson.type) }}
                                            </span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ lesson.title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ lesson.duration_minutes }} {{ t('lessons.fields.duration_minutes') }} • 
                                            {{ lesson.questions_count }} {{ t('lessons.questions') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Questions -->
                                <div v-if="lesson.questions && lesson.questions.length > 0" class="mt-4 pt-4 border-t border-gray-200">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ t('lessons.questions') }}:</h4>
                                    <div class="space-y-3">
                                        <div
                                            v-for="(question, qIndex) in lesson.questions"
                                            :key="question.id"
                                            class="bg-gray-50 rounded-lg p-4"
                                        >
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                                            {{ t('lessons.question') }} {{ qIndex + 1 }}
                                                        </span>
                                                        <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs capitalize">
                                                            {{ question.type }}
                                                        </span>
                                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">
                                                            {{ question.points }} {{ t('lessons.points') }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-900">{{ question.question }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 space-y-2">
                                                <div
                                                    v-for="answer in question.answers"
                                                    :key="answer.id"
                                                    class="flex items-center gap-2 text-sm"
                                                    :class="answer.is_correct ? 'text-blue-700 font-medium' : 'text-gray-600'"
                                                >
                                                    <div class="w-4 h-4 border-2 rounded-full flex items-center justify-center"
                                                        :class="answer.is_correct ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                                        <svg v-if="answer.is_correct" class="w-2.5 h-2.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <span>{{ answer.answer }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="mt-4 pt-4 border-t border-gray-200 text-sm text-gray-500">
                                    {{ t('lessons.no_questions') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Question Modal -->
        <div v-if="showQuestionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="closeQuestionModal">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" :dir="direction">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900">{{ t('lessons.actions.add_question') || 'Add Question' }}</h3>
                        <button @click="closeQuestionModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form @submit.prevent="handleAddQuestion">
                        <!-- Lesson Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('lessons.title') }}
                            </label>
                            <select 
                                v-model="selectedLessonId" 
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">{{ t('common.select') || 'Select Lesson' }}</option>
                                <option 
                                    v-for="lesson in course.lessons" 
                                    :key="lesson.id" 
                                    :value="lesson.id"
                                >
                                    {{ lesson.translated_title || lesson.title }}
                                </option>
                            </select>
                        </div>

                        <!-- Question Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('lessons.fields.question_type') || 'Question Type' }}
                            </label>
                            <select 
                                v-model="selectedQuestionType" 
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">{{ t('common.select') || 'Select Type' }}</option>
                                <option value="multiple_choice">{{ t('lessons.types.multiple_choice') || 'Multiple Choice' }}</option>
                                <option value="true_false">{{ t('lessons.types.true_false') || 'True/False' }}</option>
                                <option value="short_answer">{{ t('lessons.types.short_answer') || 'Short Answer' }}</option>
                                <option value="essay">{{ t('lessons.types.essay') || 'Essay' }}</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <button 
                                type="button" 
                                @click="closeQuestionModal"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                                {{ t('common.cancel') }}
                            </button>
                            <button 
                                type="submit"
                                :disabled="!selectedLessonId || !selectedQuestionType"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                {{ t('common.continue') || 'Continue' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </component>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
    course: Object,
    canEdit: Boolean,
    isEnrolled: Boolean,
    isFavorited: Boolean,
    isInstructor: Boolean,
    instructorData: Object,
    batches: Array,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError, showWarning } = useAlert();
const page = usePage();

const auth = computed(() => page.props.auth?.user);
const layout = computed(() => auth.value ? AuthenticatedLayout : AppLayout);

// Check if user is admin - admins should not see edit buttons
const isAdmin = computed(() => {
    const user = auth.value;
    if (!user) return false;
    const isAdminFlag = user.is_admin === 1 || user.is_admin === true;
    const role = user.role;
    return (role === 'super_admin' || role === 'admin') && isAdminFlag;
});

// Only show edit buttons if canEdit is true AND user is NOT admin
const showEditButtons = computed(() => {
    return props.canEdit && !isAdmin.value;
});

const instructorTab = ref('students');

// Collapsible Course Content State
const isCourseContentExpanded = ref(true);

const toggleCourseContent = () => {
    isCourseContentExpanded.value = !isCourseContentExpanded.value;
};

// Collapsible Sections State
const expandedSections = ref({});

// Initialize all sections as expanded by default
onMounted(() => {
    if (props.course.sections && props.course.sections.length > 0) {
        props.course.sections.forEach(section => {
            expandedSections.value[section.id] = true;
        });
    }
});

const toggleSection = (sectionId) => {
    expandedSections.value[sectionId] = !expandedSections.value[sectionId];
};

// Question Modal State
const showQuestionModal = ref(false);
const selectedLessonId = ref('');
const selectedQuestionType = ref('');

const closeQuestionModal = () => {
    showQuestionModal.value = false;
    selectedLessonId.value = '';
    selectedQuestionType.value = '';
};

const handleAddQuestion = () => {
    if (!selectedLessonId.value || !selectedQuestionType.value) {
        return;
    }
    
    // Navigate to question create page with selected lesson and type
    router.visit(route('instructor.lessons.questions.create', {
        course: props.course.slug || props.course.id,
        lesson: selectedLessonId.value
    }), {
        data: {
            type: selectedQuestionType.value
        }
    });
};

const getInitials = (name) => {
    if (!name) return '?';
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
};

// Default course image
const defaultCourseImage = '/images/default-course.avif';

// Favorite state
const isFavorited = ref(props.isFavorited || false);

const getCourseImage = () => {
    return props.course.thumbnail_url || props.course.thumbnail || defaultCourseImage;
};

// Enrollment removed - admin assigns students to batches

const handleImageError = (event) => {
    if (event.target.src !== defaultCourseImage) {
        event.target.src = defaultCourseImage;
    }
};

const getTotalLessonsCount = () => {
    if (props.course.sections && props.course.sections.length > 0) {
        // Count lessons from all sections
        return props.course.sections.reduce((total, section) => {
            return total + (section.lessons?.length || 0);
        }, 0);
    }
    // Fallback to direct lessons count
    return props.course.lessons?.length || 0;
};

const toggleFavorite = () => {
    if (!auth.value) {
        showWarning(t('common.login_required') || 'Please login to add favorites', t('common.warning') || 'Warning');
        router.visit(route('login'));
        return;
    }

    router.post(route('courses.favorite.toggle', { course: props.course.slug || props.course.id }), {}, {
        preserveScroll: true,
        onSuccess: (page) => {
            const isFavoritedNow = page.props.isFavorited || false;
            isFavorited.value = isFavoritedNow;
            
            const favoriteKey = isFavoritedNow ? 'courses.favorite_added' : 'courses.favorite_removed';
            const message = t(favoriteKey) || (isFavoritedNow ? 'Course added to favorites!' : 'Course removed from favorites');
            showSuccess(message, t('common.success') || 'Success');
        },
        onError: (errors) => {
            if (errors.redirect) {
                router.visit(errors.redirect);
            } else {
                const message = errors.message || t('courses.favorite_failed') || 'Failed to update favorite';
                showError(message, t('common.error') || 'Error');
            }
        },
    });
};
</script>
