<template>
    <AdminLayout :page-title="course?.translated_title || course?.title || t('admin.course_details') || 'Course Details'">
        <Head :title="course?.translated_title || course?.title || t('admin.course_details') || 'Course Details'" />
        
        <div class="admin-course-show-page container-fluid px-3 px-md-4 px-lg-5 py-4" :dir="direction">
            <!-- Page Header - Enhanced -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-8 shadow-2xl relative overflow-hidden mb-6">
                <div class="absolute inset-0 bg-black/5"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                <div class="relative z-10 order-2 sm:order-1">
                                <Link 
                        v-if="course && (course.slug || course.id)"
                        :href="route('admin.courses.edit', course.slug || course.id)" 
                        class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-blue-600 font-semibold px-6 py-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                                >
                        <i class="bi bi-pencil-fill"></i>
                        <span>{{ t('admin.edit_course') || t('common.edit') || 'Edit Course' }}</span>
                                </Link>
                            </div>
                <div class="relative z-10 text-white flex-1 text-right order-1 sm:order-2" :class="direction === 'rtl' ? 'text-left' : 'text-right'">
                    <div class="flex items-center gap-2 text-sm text-blue-100 mb-3 justify-end" :class="direction === 'rtl' ? 'flex-row-reverse justify-start' : 'justify-end'">
                        <Link :href="route('admin.courses.index')" class="hover:text-white transition-colors font-medium">{{ t('admin.courses_management') || 'Courses Management' }}</Link>
                        <span>{{ direction === 'rtl' ? '‹' : '›' }}</span>
                        <span class="text-white font-semibold">{{ course?.translated_title || course?.title }}</span>
                        </div>
                    <h1 class="text-4xl font-bold mb-3 flex items-center gap-3 justify-end" :class="direction === 'rtl' ? 'flex-row-reverse justify-start' : 'justify-end'">
                        <i class="bi bi-book text-3xl"></i>
                        <span>{{ course?.translated_title || course?.title }}</span>
                                </h1>
                    <p class="text-blue-100 text-base flex items-center gap-2" :class="direction === 'rtl' ? 'justify-start' : 'justify-end'">
                        <i class="bi bi-info-circle"></i>
                                    {{ t('admin.course_full_details') || 'View complete course information and statistics' }}
                                </p>
                </div>
            </div>

            <!-- Success Message - Bootstrap Style -->
            <div v-if="page.props.flash?.success" class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ page.props.flash.success }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <!-- Statistics Cards - Bootstrap Style -->
            <div class="row g-3 g-md-4 mb-4">
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-gradient rounded p-3">
                                    <i class="bi bi-people fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-primary mb-1">{{ statistics?.total_students || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_students') || 'Total Students' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-success bg-gradient rounded p-3">
                                    <i class="bi bi-check-circle fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-success mb-1">{{ statistics?.total_enrollments || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_enrollments') || 'Total Enrollments' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-info bg-gradient rounded p-3">
                                    <i class="bi bi-book fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-info mb-1">{{ statistics?.total_lessons || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_lessons') || 'Total Lessons' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-warning bg-gradient rounded p-3">
                                    <i class="bi bi-collection fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-warning mb-1">{{ statistics?.total_batches || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_batches') || 'Total Batches' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card border-0 shadow-sm h-100 stat-card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-danger bg-gradient rounded p-3">
                                    <i class="bi bi-question-circle fs-4 text-white"></i>
                                </div>
                            </div>
                            <h3 class="h2 fw-bold text-danger mb-1">{{ statistics?.total_questions || 0 }}</h3>
                            <small class="text-muted fw-semibold d-block">{{ t('admin.total_questions') || 'Total Questions' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-12 col-lg-8">
                    <!-- Course Image -->
                    <div class="card shadow-sm mb-4 course-image-card">
                        <div class="card-body p-0">
                            <img 
                                :src="course?.thumbnail_url || course?.thumbnail || '/images/default-course.avif'" 
                                :alt="course?.translated_title || course?.title"
                                class="img-fluid w-100 course-thumbnail-image"
                                @error="handleImageError($event)"
                            />
                        </div>
                    </div>

                    <!-- Course Details - Bootstrap Style -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h3 class="h5 fw-bold mb-0">{{ t('admin.course_details') || 'Course Details' }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.title') || 'Title' }} ({{ t('common.language_english') || 'English' }})</label>
                                    <p class="mb-0 fw-semibold">{{ course?.title || '-' }}</p>
                                </div>

                                <div v-if="course?.title_ar" class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.title') || 'Title' }} ({{ t('common.language_arabic') || 'Arabic' }})</label>
                                    <p class="mb-0 fw-semibold" dir="rtl">{{ course?.title_ar }}</p>
                                </div>
                                
                                <div v-if="course?.translated_title && course?.translated_title !== course?.title" class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.title') || 'Title' }} ({{ t('common.current_language') || 'Current Language' }})</label>
                                    <p class="mb-0 fw-semibold" :dir="direction">{{ course?.translated_title }}</p>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.description') || 'Description' }} ({{ t('common.language_english') || 'English' }})</label>
                                    <p class="mb-0 text-muted">{{ course?.description || '-' }}</p>
                                </div>

                                <div v-if="course?.description_ar" class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.description') || 'Description' }} ({{ t('common.language_arabic') || 'Arabic' }})</label>
                                    <p class="mb-0 text-muted" dir="rtl">{{ course?.description_ar }}</p>
                                </div>
                                
                                <div v-if="course?.translated_description && course?.translated_description !== course?.description" class="col-12">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.description') || 'Description' }} ({{ t('common.current_language') || 'Current Language' }})</label>
                                    <p class="mb-0 text-muted" :dir="direction">{{ course?.translated_description }}</p>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.level') || 'Level' }}</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info">{{ t(`courses.levels.${course?.level}`) || course?.level }}</span>
                                    </p>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.fields.duration_hours') || 'Duration' }}</label>
                                    <p class="mb-0 fw-semibold">{{ course?.duration_hours || 0 }} {{ t('common.hours') || 'hours' }}</p>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-muted small fw-semibold">{{ t('courses.status.title') || 'Status' }}</label>
                                    <p class="mb-0">
                                        <span :class="course?.is_published ? 'badge bg-success' : 'badge bg-warning'">
                                            {{ course?.is_published ? t('courses.status.published') : t('courses.status.draft') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sections & Lessons - Bootstrap Style -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h3 class="h5 fw-bold mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-book text-primary"></i>
                                    {{ t('admin.course_content') || 'Course Content' }}
                                </h3>
                                <p class="text-muted small mb-0">{{ t('admin.course_content_description') || 'Manage sections, lessons, and questions' }}</p>
                            </div>
                            <Link 
                                v-if="course && (course.slug || course.id)" 
                                :href="route('admin.courses.sections.index', course.slug || course.id)" 
                                class="btn btn-sm manage-sections-btn"
                                @click.stop
                            >
                                <i class="bi bi-gear me-1"></i>
                                {{ t('admin.manage_sections') || 'Manage Chapters' }}
                            </Link>
                        </div>
                        <div class="card-body">
                            <div v-if="course?.sections && course.sections.length > 0">
                                <div class="accordion" id="sectionsAccordion">
                                    <div v-for="section in course.sections" :key="section.id" class="accordion-item mb-3">
                                        <h2 class="accordion-header" :id="`heading-${section.id}`">
                                            <div class="d-flex align-items-center w-100 pe-3">
                                                <button 
                                                    class="accordion-button flex-grow-1" 
                                                    :class="{ collapsed: !isSectionExpanded(section.id) }"
                                                    type="button" 
                                                    @click="toggleSection(section.id)"
                                                    :aria-expanded="isSectionExpanded(section.id) ? 'true' : 'false'"
                                                    :aria-controls="`section-${section.id}`"
                                                >
                                                    <span class="badge bg-info me-2">{{ section.lessons?.length || 0 }}</span>
                                                    {{ section.translated_title || section.title }}
                                                </button>
                                                <button
                                                    @click.stop="openSectionModal(section)"
                                                    class="btn btn-sm btn-outline-warning ms-2"
                                                    :title="t('common.edit') || 'Edit Section'"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </h2>
                                        <div 
                                            :id="`section-${section.id}`" 
                                            class="accordion-collapse collapse" 
                                            :class="{ show: isSectionExpanded(section.id) }" 
                                            :aria-labelledby="`heading-${section.id}`"
                                        >
                                            <div class="accordion-body">
                                                <p v-if="section.translated_description || section.description" class="text-muted mb-3">{{ section.translated_description || section.description }}</p>
                                                <div v-if="section.lessons && section.lessons.length > 0" class="list-group list-group-flush">
                                                    <div v-for="lesson in section.lessons" :key="lesson.id" class="list-group-item border rounded mb-2" @click.stop>
                                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                                            <div class="bg-primary bg-gradient rounded p-2 flex-shrink-0">
                                                                <i class="bi bi-play-circle text-white"></i>
                                                            </div>
                                                            <div class="flex-grow-1 min-w-0">
                                                                <strong class="d-block">{{ lesson.translated_title || lesson.title }}</strong>
                                                                <small v-if="lesson.translated_description || lesson.description" class="text-muted d-block mt-1">
                                                                    {{ (lesson.translated_description || lesson.description).substring(0, 100) }}{{ (lesson.translated_description || lesson.description).length > 100 ? '...' : '' }}
                                                                </small>
                                                            </div>
                                                            <div class="d-flex gap-2 align-items-center flex-wrap" @click.stop>
                                                                <button 
                                                                    @click.stop="openLessonModal({ ...lesson, section_id: lesson.section_id || section.id })"
                                                                    class="btn btn-sm btn-outline-warning"
                                                                    :title="t('common.edit') || 'Edit Lesson'"
                                                                >
                                                                    <i class="bi bi-pencil me-1"></i>
                                                                    <span class="d-none d-sm-inline">{{ t('common.edit') || 'Edit' }}</span>
                                                                </button>
                                                                <button 
                                                                    @click.stop="openQuestionModal(lesson.id)"
                                                                    class="btn btn-sm lesson-action-btn-add"
                                                                    :title="t('admin.add_question') || 'Add Question'"
                                                                >
                                                                    <i class="bi bi-plus-circle me-1"></i>
                                                                    <span class="d-none d-sm-inline">{{ t('admin.add_question') || 'Add Question' }}</span>
                                                                </button>
                                                                <Link 
                                                                    v-if="lesson.id && course && (course.slug || course.id)" 
                                                                    :href="route('admin.courses.lessons.questions.index', [course.slug || course.id, lesson.id])" 
                                                                    class="btn btn-sm lesson-action-btn-view"
                                                                    :title="t('admin.view_questions') || 'View Questions'"
                                                                    @click.stop
                                                                >
                                                                    <i class="bi bi-eye me-1"></i>
                                                                    <span class="d-none d-sm-inline">{{ t('admin.view_questions') || 'View Questions' }}</span>
                                                                    <span v-if="lesson.questions && lesson.questions.length > 0" class="badge bg-white text-primary ms-1">
                                                                        {{ lesson.questions.length }}
                                                                    </span>
                                                                </Link>
                                                                <span v-if="lesson.questions && lesson.questions.length > 0" class="badge bg-info align-self-center">
                                                                    <i class="bi bi-question-circle me-1"></i>
                                                                    {{ lesson.questions.length }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div v-if="lesson.questions && lesson.questions.length > 0" class="mt-3 pt-3 border-top">
                                                            <small class="text-muted fw-semibold d-block mb-2">
                                                                <i class="bi bi-list-ul me-1"></i>
                                                                {{ t('lessons.questions') || 'Questions' }}:
                                                            </small>
                                                            <div v-for="question in lesson.questions" :key="question.id" class="d-flex align-items-start gap-2 mb-2">
                                                                <i class="bi bi-circle-fill text-primary flex-shrink-0 mt-1" style="font-size: 0.5rem;"></i>
                                                                <small class="text-muted flex-grow-1">{{ question.translated_question || question.question }}</small>
                                                                <button
                                                                    @click.stop="openQuestionModal(lesson.id, question)"
                                                                    class="btn btn-sm btn-link text-primary p-0 ms-auto"
                                                                    :title="t('admin.edit_question') || 'Edit Question'"
                                                                >
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-else class="text-center py-3 text-muted">
                                                    <i class="bi bi-book me-2"></i>
                                                    {{ t('lessons.no_lessons') || 'No lessons in this section' }}
                                                    <div class="mt-3">
                                                        <button 
                                                            @click.stop="openLessonModal(section.id)"
                                                            class="btn btn-sm btn-primary"
                                                        >
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            {{ t('admin.add_lesson') || 'Add Lesson' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <p class="text-muted mb-3">{{ t('admin.no_sections') || 'No sections added yet' }}</p>
                                <button 
                                    @click="openSectionModal()"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('admin.add_section') || 'Add Section' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-12 col-lg-4">
                    <!-- Quick Actions - Bootstrap Style -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h3 class="h6 fw-bold mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-lightning-charge text-primary"></i>
                                {{ t('admin.quick_actions') || 'Quick Actions' }}
                            </h3>
                            <p class="text-muted small mb-0">{{ t('admin.manage_course_content_description') || 'Manage course content and structure' }}</p>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <Link v-if="course && (course.slug || course.id)" :href="route('admin.courses.batches.index', course.slug || course.id)" class="btn btn-outline-primary">
                                    <i class="bi bi-people me-2"></i>
                                    {{ t('admin.manage_batches') || 'Manage Batches' }}
                                </Link>
                                <button @click="openBatchModal()" class="btn btn-outline-success">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('admin.create_batch') || 'Create Batch' }}
                                </button>
                                <button @click="openSectionModal()" class="btn btn-outline-info">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('admin.add_section') || 'Add Section' }}
                                </button>
                                <Link v-if="course && (course.slug || course.id)" :href="route('admin.courses.sections.index', course.slug || course.id)" class="btn btn-outline-warning">
                                    <i class="bi bi-list me-2"></i>
                                    {{ t('admin.manage_sections') || 'Manage Chapters' }}
                                </Link>
                                <button @click="openLessonModal()" class="btn btn-outline-secondary">
                                    <i class="bi bi-book me-2"></i>
                                    {{ t('admin.add_lesson') || 'Add Lesson' }}
                                </button>
                                <Link v-if="course && (course.slug || course.id) && course?.sections && course.sections.length > 0 && course.sections.some(s => s.lessons && s.lessons.length > 0)" :href="route('admin.courses.lessons.index', course.slug || course.id)" class="btn btn-outline-warning">
                                    <i class="bi bi-list me-2"></i>
                                    {{ t('admin.manage_lessons') || 'Manage Lessons' }}
                                </Link>
                           
                            </div>
                        </div>
                    </div>

                    <!-- Batches - Bootstrap Style -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h3 class="h6 fw-bold mb-1 d-flex align-items-center gap-2">
                                    <i class="bi bi-people text-success"></i>
                                    {{ t('admin.batches') || 'Batches' }}
                                </h3>
                                <p class="text-muted small mb-0">{{ t('admin.batches_description') || 'Manage course batches and student enrollments' }}</p>
                            </div>
                            <div class="d-flex gap-2">
                                <Link v-if="course && (course.slug || course.id)" :href="route('admin.courses.batches.index', course.slug || course.id)" class="btn btn-sm btn-outline-primary" :title="t('admin.manage_batches') || 'Manage Batches'">
                                    <i class="bi bi-list me-1"></i>
                                    <span class="d-none d-sm-inline">{{ t('admin.manage_batches') || 'Manage' }}</span>
                                </Link>
                                <button @click="openBatchModal()" class="btn btn-sm btn-primary" :title="t('admin.create_batch') || 'Create Batch'">
                                    <i class="bi bi-plus me-1"></i>
                                    <span class="d-none d-sm-inline">{{ t('admin.create_batch') || 'Create' }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="course && (course.slug || course.id) && course?.batches && course.batches.length > 0" class="list-group list-group-flush">
                                <Link 
                                    v-for="batch in course.batches" 
                                    :key="batch.id" 
                                    :href="route('admin.courses.batches.show', [course.slug || course.id, batch.id])"
                                    class="list-group-item list-group-item-action border rounded mb-2"
                                >
                                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                        <div class="flex-grow-1">
                                            <strong class="d-block mb-1">{{ batch.name }}</strong>
                                            <div class="d-flex align-items-center justify-content-between gap-2 text-muted small">
                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="bi bi-person"></i>
                                                    <span>{{ t('admin.instructor') || 'Instructor' }}: <strong>{{ batch.instructor?.name || '-' }}</strong></span>
                                                </div>
                                                <div v-if="batch.enrollments_count !== undefined" class="d-flex align-items-center gap-1 flex-shrink-0">
                                                    <i class="bi bi-people text-info"></i>
                                                    <span class="badge bg-info text-white fw-bold">{{ batch.enrollments_count || 0 }}</span>
                                                </div>
                                    </div>
                                        </div>
                                        <span :class="batch.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                                            <i :class="batch.is_active ? 'bi bi-check-circle' : 'bi bi-x-circle'" class="me-1"></i>
                                            {{ batch.is_active ? (t('admin.active') || 'Active') : (t('admin.inactive') || 'Inactive') }}
                                        </span>
                                    </div>
                                    <div v-if="batch.start_date" class="text-muted small mt-2 d-flex align-items-center gap-2">
                                        <span>
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ new Date(batch.start_date).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </Link>
                            </div>
                            <div v-else class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                                <p class="text-muted mb-4">{{ t('admin.no_batches') || 'No batches yet' }}</p>
                                <button @click="openBatchModal()" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    {{ t('admin.create_batch') || 'Create Batch' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Form Modal -->
        <SectionForm
            :show="showSectionModal"
            :section="editingSection"
            :form-data="sectionForm"
            :errors="sectionForm.errors"
            :processing="sectionForm.processing"
            @close="closeSectionModal"
            @submit="submitSection"
        />

        <!-- Lesson Form Modal -->
        <LessonForm
            :show="showLessonModal"
            :lesson="editingLesson"
            :form-data="lessonForm"
            :errors="lessonForm.errors"
            :processing="lessonForm.processing"
            :sections="sections"
            :lesson-types="lessonTypes"
            @close="closeLessonModal"
            @submit="submitLesson"
            @type-change="handleLessonTypeChange"
        />

        <!-- Question Form Modal -->
        <QuestionForm
            :show="showQuestionModal"
            :question="editingQuestion"
            :form-data="questionForm"
            :errors="questionForm.errors"
            :processing="questionForm.processing"
            :question-types="questionTypes"
            @close="closeQuestionModal"
            @submit="submitQuestion"
            @type-change="handleQuestionTypeChange"
        />

        <!-- Batch Form Modal -->
        <BatchForm
            :show="showBatchModal"
            :batch="editingBatch"
            :form-data="batchForm"
            :errors="batchForm.errors"
            :processing="batchForm.processing"
            :instructors="instructors"
            @close="closeBatchModal"
            @submit="submitBatch"
        />
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useAlert } from '@/composables/useAlert';
import { computed, onMounted, nextTick, ref, watch } from 'vue';
import SectionForm from '@/Pages/Admin/Sections/Form.vue';
import LessonForm from '@/Pages/Admin/Lessons/Form.vue';
import QuestionForm from '@/Pages/Admin/Questions/Form.vue';
import BatchForm from '@/Pages/Admin/Batches/Form.vue';

const props = defineProps({
    course: Object,
    statistics: Object,
    sections: Array,
    instructors: Array,
    lessonTypes: Array,
    questionTypes: Array,
    existingBatch: Object,
});

const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError } = useAlert();
const page = usePage();

const direction = computed(() => page.props.direction || 'ltr');

// Get course identifier - prioritize slug, fallback to id
// Direct access to ensure reactivity
const courseIdentifier = computed(() => {
    const course = props.course;
    if (!course) return null;
    
    // Try slug first (preferred for SEO-friendly URLs)
    if (course.slug) {
        return course.slug;
    }
    
    // Fallback to id
    if (course.id) {
        return course.id;
    }
    
    return null;
});


// Track which sections are expanded
const expandedSections = ref(new Set());

// Initialize with first section expanded
if (props.course?.sections && props.course.sections.length > 0) {
    expandedSections.value.add(props.course.sections[0].id);
}

const toggleSection = (sectionId) => {
    if (expandedSections.value.has(sectionId)) {
        expandedSections.value.delete(sectionId);
    } else {
        expandedSections.value.add(sectionId);
    }
};

const isSectionExpanded = (sectionId) => {
    return expandedSections.value.has(sectionId);
};

const handleImageError = (event) => {
    event.target.src = '/images/default-course.avif';
};

// Modal states
const showSectionModal = ref(false);
const showLessonModal = ref(false);
const showQuestionModal = ref(false);
const showBatchModal = ref(false);
const editingSection = ref(null);
const editingLesson = ref(null);
const editingQuestion = ref(null);
const editingBatch = ref(null);
const selectedSectionIdForLesson = ref(null);
const selectedLessonIdForQuestion = ref(null);

// Forms
const sectionForm = useForm({
    title: '',
    title_ar: '',
    description: '',
    description_ar: '',
    order: null,
});

const lessonForm = useForm({
    title: '',
    title_ar: '',
    type: '',
    section_id: null,
    order: 1,
    duration_minutes: 0,
    description: '',
    description_ar: '',
    content: '',
    content_ar: '',
    video_url: '',
    live_meeting_date: '',
    live_meeting_link: '',
});

const questionForm = useForm({
    type: '',
    question: '',
    question_ar: '',
    explanation: '',
    explanation_ar: '',
    points: 1,
    order: 0,
    answers: [
        { answer: '', answer_ar: '', is_correct: false, order: 0 },
        { answer: '', answer_ar: '', is_correct: false, order: 1 },
    ],
});

const batchForm = useForm({
    name: '',
    name_ar: '',
    description: '',
    description_ar: '',
    instructor_id: null,
    start_date: null,
    end_date: null,
    max_students: null,
    is_active: true,
});

// Section Modal Functions
const openSectionModal = (section = null) => {
    editingSection.value = section;
    if (section) {
        sectionForm.title = section.title || '';
        sectionForm.title_ar = section.title_ar || '';
        sectionForm.description = section.description || '';
        sectionForm.description_ar = section.description_ar || '';
        sectionForm.order = section.order;
    } else {
        sectionForm.reset();
    }
    showSectionModal.value = true;
};

const closeSectionModal = () => {
    showSectionModal.value = false;
    editingSection.value = null;
    sectionForm.reset();
    sectionForm.clearErrors();
};

const submitSection = (formData) => {
    // Prepare form data
    const data = { ...formData };
    if (data.order !== null && data.order !== '') {
        data.order = parseInt(data.order);
    } else {
        data.order = null;
    }
    
    if (editingSection.value) {
        sectionForm.put(route('admin.courses.sections.update', [props.course.slug || props.course.id, editingSection.value.id]), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('sections.updated_successfully') || 'Section updated successfully!', t('common.success') || 'Success');
                closeSectionModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    } else {
        sectionForm.post(route('admin.courses.sections.store', props.course.slug || props.course.id), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('sections.created_successfully') || 'Section created successfully!', t('common.success') || 'Success');
                closeSectionModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    }
};

// Lesson Modal Functions
const openLessonModal = (lessonOrSectionId = null) => {
    // Clear any existing errors first
    lessonForm.clearErrors();
    
    // Check if it's a lesson object or section ID
    if (lessonOrSectionId && typeof lessonOrSectionId === 'object' && lessonOrSectionId.id) {
        // It's a lesson object - edit mode
        editingLesson.value = lessonOrSectionId;
        // Get section_id from lesson - it might be in section.id or section_id property
        const sectionId = lessonOrSectionId.section?.id || lessonOrSectionId.section_id || null;
        selectedSectionIdForLesson.value = sectionId;
        lessonForm.title = lessonOrSectionId.title || '';
        lessonForm.title_ar = lessonOrSectionId.title_ar || '';
        lessonForm.type = lessonOrSectionId.type || '';
        lessonForm.section_id = sectionId;
        lessonForm.order = lessonOrSectionId.order || 1;
        lessonForm.duration_minutes = lessonOrSectionId.duration_minutes || 0;
        lessonForm.description = lessonOrSectionId.description || '';
        lessonForm.description_ar = lessonOrSectionId.description_ar || '';
        lessonForm.content = lessonOrSectionId.content || '';
        lessonForm.content_ar = lessonOrSectionId.content_ar || '';
        lessonForm.video_url = lessonOrSectionId.video_url || '';
        // Format live_meeting_date for datetime-local input (YYYY-MM-DDTHH:mm)
        // Parse the date string directly without timezone conversion to preserve the stored time
        if (lessonOrSectionId.live_meeting_date) {
            try {
                const dateString = String(lessonOrSectionId.live_meeting_date);
                let year, month, day, hours, minutes;
                
                // Check if date string has timezone info
                const hasTimezone = dateString.includes('Z') || 
                                   dateString.match(/[+-]\d{2}:\d{2}$/) ||
                                   (dateString.includes('T') && dateString.length > 16);
                
                if (hasTimezone) {
                    // Has timezone - extract UTC components to get the stored time
                    const utcDate = new Date(dateString);
                    year = utcDate.getUTCFullYear();
                    month = String(utcDate.getUTCMonth() + 1).padStart(2, '0');
                    day = String(utcDate.getUTCDate()).padStart(2, '0');
                    hours = String(utcDate.getUTCHours()).padStart(2, '0');
                    minutes = String(utcDate.getUTCMinutes()).padStart(2, '0');
                } else {
                    // No timezone - parse directly from string (format: YYYY-MM-DD HH:mm:ss or YYYY-MM-DDTHH:mm:ss)
                    const match = dateString.match(/^(\d{4})-(\d{2})-(\d{2})(?:[\sT](\d{2}):(\d{2})(?::(\d{2}))?)?/);
                    if (match) {
                        [, year, month, day, hours = '00', minutes = '00'] = match;
                        year = parseInt(year);
                        month = String(parseInt(month)).padStart(2, '0');
                        day = String(parseInt(day)).padStart(2, '0');
                        hours = String(parseInt(hours)).padStart(2, '0');
                        minutes = String(parseInt(minutes)).padStart(2, '0');
                    } else {
                        // Fallback to Date parsing
                        const date = new Date(dateString);
                        if (!isNaN(date.getTime())) {
                            year = date.getFullYear();
                            month = String(date.getMonth() + 1).padStart(2, '0');
                            day = String(date.getDate()).padStart(2, '0');
                            hours = String(date.getHours()).padStart(2, '0');
                            minutes = String(date.getMinutes()).padStart(2, '0');
                        } else {
                            lessonForm.live_meeting_date = '';
                            return;
                        }
                    }
                }
                
            lessonForm.live_meeting_date = `${year}-${month}-${day}T${hours}:${minutes}`;
            } catch (e) {
                console.error('Error parsing date:', e);
                lessonForm.live_meeting_date = '';
            }
        } else {
            lessonForm.live_meeting_date = '';
        }
        lessonForm.live_meeting_link = lessonOrSectionId.live_meeting_link || '';
    } else {
        // It's a section ID or null - create mode
        editingLesson.value = null;
        selectedSectionIdForLesson.value = lessonOrSectionId;
        lessonForm.reset();
        lessonForm.clearErrors();
        lessonForm.section_id = lessonOrSectionId || null;
        lessonForm.order = 1;
        lessonForm.duration_minutes = 0;
        lessonForm.live_meeting_date = '';
        lessonForm.live_meeting_link = '';
        lessonForm.type = ''; // Reset type for new lessons
    }
    showLessonModal.value = true;
};

const closeLessonModal = () => {
    showLessonModal.value = false;
    editingLesson.value = null;
    selectedSectionIdForLesson.value = null;
    lessonForm.reset();
    lessonForm.clearErrors();
    // Reset form defaults
    lessonForm.order = 1;
    lessonForm.duration_minutes = 0;
    lessonForm.live_meeting_date = '';
    lessonForm.live_meeting_link = '';
};

// Handle lesson type changes
const handleLessonTypeChange = (newType) => {
    // This is called when the type changes in the form
    // The watcher below will handle the actual field clearing/initialization
};

// Watch for live_meeting_date changes to clear errors when date is filled
watch(() => lessonForm.live_meeting_date, (newDate, oldDate) => {
    // Clear error if date is now filled and valid
    if (newDate && newDate.trim() !== '' && newDate !== oldDate) {
        // Check if the date is in valid format
        const isValidFormat = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(newDate.trim());
        if (isValidFormat && lessonForm.errors.live_meeting_date) {
            lessonForm.clearErrors('live_meeting_date');
        }
    }
});

// Watch for lesson type changes to handle live lesson fields
watch(() => lessonForm.type, (newType, oldType) => {
    // When switching away from 'live', clear live-specific fields
    if (oldType === 'live' && newType !== 'live') {
        lessonForm.live_meeting_date = '';
        lessonForm.live_meeting_link = '';
        // Clear any errors related to live fields
        if (lessonForm.errors.live_meeting_date) {
            lessonForm.clearErrors('live_meeting_date');
        }
        if (lessonForm.errors.live_meeting_link) {
            lessonForm.clearErrors('live_meeting_link');
        }
    }
    // When switching to 'live', ensure fields are initialized
    if (newType === 'live' && oldType !== 'live') {
        // If live_meeting_date is not set, keep it empty (user needs to fill it)
        // But ensure it's a string, not null
        if (lessonForm.live_meeting_date === null) {
            lessonForm.live_meeting_date = '';
        }
        if (lessonForm.live_meeting_link === null) {
            lessonForm.live_meeting_link = '';
        }
        // Clear any existing errors when switching to live type
        if (lessonForm.errors.live_meeting_date) {
            lessonForm.clearErrors('live_meeting_date');
        }
    }
});

const submitLesson = (formData) => {
    // Convert empty strings to null for nullable fields
    if (lessonForm.section_id === '' || lessonForm.section_id === null) {
        lessonForm.section_id = null;
    } else if (lessonForm.section_id) {
        lessonForm.section_id = parseInt(lessonForm.section_id);
    }
    
    // Convert empty strings to null for other nullable fields
    // But keep live_meeting_date if lesson type is 'live' and it has a value
    ['description', 'description_ar', 'content', 'content_ar', 'video_url', 'title_ar', 'live_meeting_link'].forEach(field => {
        if (lessonForm[field] === '') {
            lessonForm[field] = null;
        }
    });
    
    // Handle live_meeting_date separately - format for live lessons
    if (lessonForm.type === 'live') {
        // For live lessons, send the date as-is without timezone conversion
        // This ensures the database stores exactly what the user selected
        if (lessonForm.live_meeting_date && lessonForm.live_meeting_date.trim() !== '') {
            const dateValue = lessonForm.live_meeting_date.trim();
            
            // Validate format matches YYYY-MM-DDTHH:mm
            if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(dateValue)) {
                // Convert from datetime-local format (YYYY-MM-DDTHH:mm) to MySQL format (YYYY-MM-DD HH:mm:ss)
                // This preserves the exact time the user selected without timezone conversion
                const [datePart, timePart] = dateValue.split('T');
                lessonForm.live_meeting_date = `${datePart} ${timePart}:00`;
            } else {
                // Invalid format, try to parse and format
                try {
                    const date = new Date(dateValue);
                    if (!isNaN(date.getTime())) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        const hours = String(date.getHours()).padStart(2, '0');
                        const minutes = String(date.getMinutes()).padStart(2, '0');
                        lessonForm.live_meeting_date = `${year}-${month}-${day} ${hours}:${minutes}:00`;
                    } else {
                        lessonForm.live_meeting_date = null;
                    }
                } catch (e) {
                    lessonForm.live_meeting_date = null;
                }
            }
        } else {
            // Empty date for live lesson - will be validated by backend
            lessonForm.live_meeting_date = null;
        }
    } else {
        // For non-live lessons, convert empty to null
        if (lessonForm.live_meeting_date === '' || !lessonForm.live_meeting_date) {
            lessonForm.live_meeting_date = null;
        }
    }
    
    // Ensure order is integer or null
    if (lessonForm.order === '' || lessonForm.order === null) {
        lessonForm.order = null;
    } else {
        lessonForm.order = parseInt(lessonForm.order) || null;
    }
    
    // Ensure duration_minutes is integer (default to 0, not null, as DB doesn't allow null)
    if (lessonForm.duration_minutes === '' || lessonForm.duration_minutes === null || lessonForm.duration_minutes === undefined) {
        lessonForm.duration_minutes = 0;
    } else {
        const parsed = parseInt(lessonForm.duration_minutes);
        lessonForm.duration_minutes = isNaN(parsed) ? 0 : parsed;
    }
    
    if (editingLesson.value) {
        // Update existing lesson
        lessonForm.put(route('admin.courses.lessons.update', [props.course.slug || props.course.id, editingLesson.value.id]), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                showSuccess(t('lessons.updated_successfully') || 'Lesson updated successfully!', t('common.success') || 'Success');
                closeLessonModal();
                // Refresh the page data to show updated lesson
                router.reload({ only: ['course'] });
            },
            onError: (errors) => {
                // Errors are automatically handled by Inertia form
                // The form will display validation errors
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    } else {
        // Create new lesson
        lessonForm.post(route('admin.courses.lessons.store', props.course.slug || props.course.id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                showSuccess(t('lessons.created_successfully') || 'Lesson created successfully!', t('common.success') || 'Success');
                closeLessonModal();
                // Refresh the page data to show new lesson
                router.reload({ only: ['course'] });
            },
            onError: (errors) => {
                // Errors are automatically handled by Inertia form
                // The form will display validation errors
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    }
};

// Question Modal Functions
const openQuestionModal = (lessonId, question = null) => {
    editingQuestion.value = question;
    selectedLessonIdForQuestion.value = lessonId;
    questionForm.reset();
    
    if (question) {
        // Editing existing question
        questionForm.type = question.type || '';
        questionForm.question = question.translated_question || question.question || '';
        questionForm.question_ar = question.question_ar || '';
        questionForm.explanation = question.translated_explanation || question.explanation || '';
        questionForm.explanation_ar = question.explanation_ar || '';
        questionForm.points = question.points || 1;
        questionForm.order = question.order || 0;
        
        // Load answers if they exist
        if (question.answers && question.answers.length > 0) {
            questionForm.answers = question.answers.map((answer, index) => ({
                id: answer.id || null,
                answer: answer.translated_answer || answer.answer || '',
                answer_ar: answer.answer_ar || '',
                is_correct: answer.is_correct || false,
                order: answer.order !== undefined ? answer.order : index,
            }));
        } else {
            // Default answers based on type
            if (question.type === 'true_false') {
                questionForm.answers = [
                    { answer: 'True', answer_ar: 'صحيح', is_correct: false, order: 0 },
                    { answer: 'False', answer_ar: 'خطأ', is_correct: false, order: 1 },
                ];
            } else {
                questionForm.answers = [
                    { answer: '', answer_ar: '', is_correct: false, order: 0 },
                    { answer: '', answer_ar: '', is_correct: false, order: 1 },
                ];
            }
        }
    } else {
        // Creating new question
        questionForm.answers = [
            { answer: '', answer_ar: '', is_correct: false, order: 0 },
            { answer: '', answer_ar: '', is_correct: false, order: 1 },
        ];
    }
    
    showQuestionModal.value = true;
};

const closeQuestionModal = () => {
    showQuestionModal.value = false;
    editingQuestion.value = null;
    selectedLessonIdForQuestion.value = null;
    questionForm.reset();
    questionForm.clearErrors();
};

const handleQuestionTypeChange = (type) => {
    if (type === 'true_false') {
        questionForm.answers = [
            { answer: 'True', answer_ar: 'صحيح', is_correct: false, order: 0 },
            { answer: 'False', answer_ar: 'خطأ', is_correct: false, order: 1 },
        ];
    } else if (type === 'multiple_choice' && questionForm.answers.length < 2) {
        questionForm.answers = [
            { answer: '', answer_ar: '', is_correct: false, order: 0 },
            { answer: '', answer_ar: '', is_correct: false, order: 1 },
        ];
    }
};

const addAnswer = () => {
    questionForm.answers.push({
        answer: '',
        answer_ar: '',
        is_correct: false,
        order: questionForm.answers.length,
    });
};

const removeAnswer = (index) => {
    if (questionForm.answers.length > 2) {
        questionForm.answers.splice(index, 1);
        questionForm.answers.forEach((answer, idx) => {
            answer.order = idx;
        });
    }
};

const submitQuestion = (formData) => {
    if (!selectedLessonIdForQuestion.value) {
        showError('Please select a lesson', t('common.error') || 'Error');
        return;
    }
    
    // Prepare form data - convert empty strings to null for nullable fields
    ['question_ar', 'explanation', 'explanation_ar'].forEach(field => {
        if (questionForm[field] === '') {
            questionForm[field] = null;
        }
    });
    
    // Ensure points and order are integers
    if (questionForm.points === '' || questionForm.points === null) {
        questionForm.points = 1;
    } else {
        questionForm.points = parseInt(questionForm.points) || 1;
    }
    
    if (questionForm.order === '' || questionForm.order === null) {
        questionForm.order = 0;
    } else {
        questionForm.order = parseInt(questionForm.order) || 0;
    }
    
    // Clean up answers - remove empty answers and ensure order is set
    if (questionForm.answers && Array.isArray(questionForm.answers)) {
        questionForm.answers = questionForm.answers
            .filter(answer => answer.answer && answer.answer.trim() !== '')
            .map((answer, index) => ({
                ...answer,
                order: index,
            }));
    }
    
    if (editingQuestion.value) {
        // Update existing question
        questionForm.put(route('admin.courses.lessons.questions.update', [
            props.course.slug || props.course.id,
            selectedLessonIdForQuestion.value,
            editingQuestion.value.id
        ]), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('questions.updated_successfully') || 'Question updated successfully!', t('common.success') || 'Success');
                closeQuestionModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    } else {
        // Create new question
        questionForm.post(route('admin.courses.lessons.questions.store', [
            props.course.slug || props.course.id,
            selectedLessonIdForQuestion.value
        ]), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('questions.created_successfully') || 'Question created successfully!', t('common.success') || 'Success');
                closeQuestionModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    }
};

// Batch Modal Functions
const openBatchModal = (batch = null) => {
    editingBatch.value = batch;
    if (batch) {
        batchForm.name = batch.name || '';
        batchForm.name_ar = batch.name_ar || '';
        batchForm.description = batch.description || '';
        batchForm.description_ar = batch.description_ar || '';
        batchForm.instructor_id = batch.instructor?.id || null;
        batchForm.start_date = batch.start_date;
        batchForm.end_date = batch.end_date;
        batchForm.max_students = batch.max_students;
        batchForm.is_active = batch.is_active !== undefined ? batch.is_active : true;
    } else {
        batchForm.reset();
        // Show warning if there's an existing batch that hasn't ended
        if (props.existingBatch) {
            const locale = page.props.locale || 'en';
            const endDate = props.existingBatch.end_date 
                ? new Date(props.existingBatch.end_date).toLocaleDateString(locale === 'ar' ? 'ar-SA' : 'en-US', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                })
                : t('admin.batch_has_no_end_date') || 'no end date';
            const errorMessage = (t('admin.cannot_create_batch_existing_not_ended') || 'Cannot create a new batch. The previous batch (:batch_name) has not ended yet. Please wait until :end_date before creating a new one.')
                .replace(':batch_name', props.existingBatch.name || '')
                .replace(':end_date', endDate);
            showError(
                errorMessage,
                t('common.warning') || 'Warning'
            );
        }
    }
    showBatchModal.value = true;
};

const closeBatchModal = () => {
    showBatchModal.value = false;
    editingBatch.value = null;
    batchForm.reset();
    batchForm.clearErrors();
};

const getInstructorName = (instructorId) => {
    const instructor = props.instructors?.find(i => i.id === instructorId);
    return instructor ? instructor.name : '';
};

const submitBatch = (formData) => {
    // Sync formData values into batchForm to ensure Arabic fields are included
    if (formData) {
        batchForm.name = formData.name || '';
        batchForm.name_ar = formData.name_ar || '';
        batchForm.description = formData.description || '';
        batchForm.description_ar = formData.description_ar || '';
        batchForm.instructor_id = formData.instructor_id || null;
        batchForm.start_date = formData.start_date || null;
        batchForm.end_date = formData.end_date || null;
        batchForm.max_students = formData.max_students || null;
        batchForm.is_active = formData.is_active !== undefined ? formData.is_active : true;
    }
    
    // Ensure instructor_id is properly set (required field)
    if (!batchForm.instructor_id || batchForm.instructor_id === '') {
        batchForm.instructor_id = null;
    } else {
        batchForm.instructor_id = parseInt(batchForm.instructor_id);
    }
    
    // Convert empty strings to null for nullable fields (including Arabic fields)
    ['name_ar', 'description', 'description_ar'].forEach(field => {
        if (batchForm[field] === '' || batchForm[field] === null || batchForm[field] === undefined) {
            batchForm[field] = null;
        }
    });
    
    // Handle date fields
    if (batchForm.start_date === '') {
        batchForm.start_date = null;
    }
    if (batchForm.end_date === '') {
        batchForm.end_date = null;
    }
    
    // Handle max_students
    if (batchForm.max_students === '' || batchForm.max_students === null) {
        batchForm.max_students = null;
    } else {
        batchForm.max_students = parseInt(batchForm.max_students) || null;
    }
    
    // Ensure is_active is boolean
    batchForm.is_active = Boolean(batchForm.is_active);
    
    if (editingBatch.value) {
        batchForm.put(route('admin.courses.batches.update', [props.course.slug || props.course.id, editingBatch.value.id]), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('admin.batch_updated_successfully') || 'Batch updated successfully!', t('common.success') || 'Success');
                closeBatchModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    } else {
        batchForm.post(route('admin.courses.batches.store', props.course.slug || props.course.id), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(t('admin.batch_created_successfully') || 'Batch created successfully!', t('common.success') || 'Success');
                closeBatchModal();
            },
            onError: (errors) => {
                if (errors.message) {
                    showError(errors.message, t('common.error') || 'Error');
                }
            },
        });
    }
};

// Watch for flash messages
watch(() => page.props.flash?.success, (success) => {
    if (success) {
        showSuccess(success, t('common.success') || 'Success');
    }
});
                        
// Initialize Bootstrap accordion and other components
onMounted(() => {
    nextTick(() => {
        // Initialize Bootstrap alerts dismiss functionality
        if (window.bootstrap && window.bootstrap.Alert) {
            const alertElements = document.querySelectorAll('.alert.alert-dismissible');
            alertElements.forEach((alert) => {
                const closeButtons = alert.querySelectorAll('.btn-close[data-bs-dismiss="alert"]');
                closeButtons.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const bsAlert = new window.bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                });
            });
        }
    });
});
</script>

<style scoped>
/* Color Variables */
:root {
    --primary-color: #0d6efd;
    --primary-dark: #0b5ed7;
    --primary-darker: #0a58ca;
    --primary-light: #3d8bfd;
    --success-color: #198754;
    --success-light: #20c997;
    --info-color: #0dcaf0;
    --info-dark: #0aa2c0;
    --warning-color: #ffc107;
    --warning-dark: #ffb300;
    --danger-color: #dc3545;
    --danger-light: #e4606d;
    --white: #ffffff;
    --gray-50: #f8f9fa;
    --gray-100: #f1f3f5;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #adb5bd;
    --gray-600: #6c757d;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
    --text-primary: #212529;
    --text-secondary: #6c757d;
    --text-muted: #6c757d;
    --border-color: #e9ecef;
    --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --shadow-md: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --shadow-xl: 0 1rem 2rem rgba(0, 0, 0, 0.2);
}

/* Page Container */
.admin-course-show-page {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    padding: 0;
    background: transparent;
    min-height: calc(100vh - 80px);
    position: relative;
    border-radius: 0;
    overflow-x: hidden;
}

.admin-course-show-page .container-fluid {
    max-width: 100%;
    padding-left: 1rem;
    padding-right: 1rem;
}

@media (min-width: 576px) {
    .admin-course-show-page .container-fluid {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
}

@media (min-width: 768px) {
    .admin-course-show-page .container-fluid {
        padding-left: 2rem;
        padding-right: 2rem;
    }
}

@media (min-width: 992px) {
    .admin-course-show-page .container-fluid {
        padding-left: 3rem;
        padding-right: 3rem;
    }
}

.admin-course-show-page::before {
    display: none;
}

.admin-course-show-page > * {
    position: relative;
    z-index: 1;
}

/* Header styles are now handled by Tailwind classes */

/* Alert Styling */
.alert {
    border-radius: 0.5rem;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 1.5rem;
}

/* Statistics Cards */
.stat-card-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    overflow: hidden;
    background: var(--white);
    position: relative;
    cursor: default;
}

.stat-card-hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, rgba(13, 110, 253, 0.3), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card-hover:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg) !important;
    border-color: var(--primary-color);
}

.stat-card-hover:hover::before {
    opacity: 1;
}

.stat-card-hover .card-body {
    padding: 1.25rem;
}

.stat-card-hover .bg-gradient {
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.stat-card-hover:hover .bg-gradient {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.stat-card-hover h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    line-height: 1.2;
    transition: all 0.3s ease;
}

.stat-card-hover:hover h3 {
    transform: scale(1.05);
}

.stat-card-hover small {
    font-size: 0.8125rem;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    line-height: 1.4;
}

/* Card Styling */
.card {
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    overflow: hidden;
    background: var(--white);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--gray-300);
}

.card-header {
    background: linear-gradient(to bottom, var(--white) 0%, var(--gray-50) 100%);
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem 1.75rem;
    font-weight: 600;
    position: relative;
}

.card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card:hover .card-header::after {
    opacity: 0.4;
}

.card-header.bg-light {
    background: linear-gradient(to bottom, var(--gray-50) 0%, var(--white) 100%) !important;
}

.card-header h3,
.card-header h5,
.card-header h6 {
    margin: 0;
    font-weight: 600;
    color: var(--text-primary);
}

.card-header p {
    margin: 0.25rem 0 0 0;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.card-body {
    padding: 1.75rem;
    background: var(--white);
}

/* Course Image */
.course-image-card {
    overflow: hidden;
    border-radius: 0.75rem;
    position: relative;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.course-image-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.course-thumbnail-image {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

.course-image-card:hover .course-thumbnail-image {
    transform: scale(1.02);
}

@media (max-width: 768px) {
    .course-thumbnail-image {
        max-height: 300px;
    }
}

@media (max-width: 576px) {
    .course-thumbnail-image {
        max-height: 250px;
    }
}

/* Form Labels */
.form-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.card-body p {
    font-size: 0.9375rem;
    line-height: 1.6;
    color: var(--gray-700);
}

.card-body p.fw-semibold {
    font-weight: 600;
    color: var(--text-primary);
}

/* Badge Styling */
.badge {
    padding: 0.5rem 0.75rem;
    font-size: 0.8125rem;
    font-weight: 500;
    border-radius: 0.375rem;
    box-shadow: var(--shadow-sm);
    transition: all 0.2s ease;
}

.badge:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-md);
}

.badge.bg-info {
    background-color: var(--info-color) !important;
    color: var(--white);
}

.badge.bg-success {
    background-color: var(--success-color) !important;
    color: var(--white);
}

.badge.bg-warning {
    background-color: var(--warning-color) !important;
    color: var(--gray-900);
}

.badge.bg-danger {
    background-color: var(--danger-color) !important;
    color: var(--white);
}

.badge.bg-primary {
    background-color: var(--primary-color) !important;
    color: var(--white);
}

.badge.bg-secondary {
    background-color: var(--gray-600) !important;
    color: var(--white);
}

/* Accordion Enhancements */
.accordion {
    --bs-accordion-border-radius: 0.5rem;
    --bs-accordion-border-color: #e9ecef;
}

.accordion-item {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    overflow: visible;
    background: #ffffff;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    position: relative;
}
.accordion-collapse,
.accordion-body {
    overflow: visible !important;
}

.accordion-collapse {
    overflow: visible !important;
    transition: height 0.35s ease, opacity 0.35s ease !important;
}

.accordion-collapse:not(.show) {
    display: none;
}

.accordion-collapse.show {
    display: block;
}

.accordion-body {
    overflow: visible !important;
    visibility: visible !important;
}


.accordion-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-color: #0d6efd;
}

.accordion-item:last-child {
    margin-bottom: 0;
}

.accordion-button {
    font-weight: 600;
    font-size: 0.9375rem;
    padding: 1rem 1.25rem;
    background: #ffffff;
    color: #212529;
    border: none;
    transition: all 0.2s ease;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
    color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.1);
    font-weight: 700;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

.accordion-button .badge {
    margin-right: 0.75rem;
    font-size: 0.75rem;
}

[dir="rtl"] .accordion-button .badge {
    margin-right: 0;
    margin-left: 0.75rem;
}

.accordion-body {
    padding: 1.25rem;
    background: var(--white);
}

/* List Group Enhancements */
.list-group {
    border-radius: 0.5rem;
    overflow: visible !important;
}

.list-group-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--border-color);
    padding: 1.25rem;
    background: var(--white);
    position: relative;
    overflow: visible !important;
    visibility: visible !important;
    display: block !important;
    pointer-events: auto !important;
}

.list-group-item > * {
    pointer-events: auto !important;
}

.list-group-item .btn,
.list-group-item a.btn,
.list-group-item .lesson-action-btn-add,
.list-group-item .lesson-action-btn-view {
    pointer-events: auto !important;
    z-index: 50 !important;
    position: relative;
    cursor: pointer !important;
    display: inline-flex !important;
}

.list-group-item .d-flex.gap-2 {
    pointer-events: auto !important;
    z-index: 50 !important;
    position: relative;
}

/* Ensure list-group-item doesn't interfere with button clicks */
.list-group-item:not(.list-group-item-action) {
    cursor: default;
}

.list-group-item:not(.list-group-item-action) .btn {
    pointer-events: auto !important;
    z-index: 100 !important;
}

.list-group-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, var(--primary-color), var(--primary-dark));
    transform: scaleY(0);
    transform-origin: bottom;
    transition: transform 0.3s ease;
}

.list-group-item:hover {
    transform: none;
    border-color: var(--primary-color);
    background: linear-gradient(to right, var(--gray-50) 0%, var(--white) 100%);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.18);
    padding-left: 1.5rem;
}

[dir="rtl"] .list-group-item:hover {
    padding-left: 1.25rem;
    padding-right: 1.5rem;
}

.list-group-item:hover::before {
    transform: scaleY(1);
}

[dir="rtl"] .list-group-item::before {
    left: auto;
    right: 0;
}

.list-group-item-action {
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.list-group-item-action:hover {
    color: inherit;
}

.list-group-item strong {
    font-size: 0.9375rem;
    color: var(--text-primary);
    display: block;
    margin-bottom: 0.5rem;
}

.list-group-item .text-muted {
    font-size: 0.8125rem;
    color: var(--text-muted);
}

.list-group-item .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.625rem;
}

/* Lesson Items */
.list-group-item .bg-primary {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.375rem;
}

.list-group-item .flex-grow-1 strong {
    font-size: 0.9375rem;
    color: var(--text-primary);
    margin: 0;
}

.list-group-item .d-flex.gap-2 {
    gap: 0.5rem;
}

.list-group-item .btn-sm {
    padding: 0.375rem 0.625rem;
    font-size: 0.8125rem;
    border-radius: 0.375rem;
    border: 1px solid;
    transition: all 0.2s ease;
}

.list-group-item .btn-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* Question Items */
.list-group-item .border-top {
    border-top: 1px solid var(--border-color) !important;
    padding-top: 0.75rem;
    margin-top: 0.75rem;
}

.list-group-item .bi-circle-fill {
    flex-shrink: 0;
    margin-top: 0.375rem;
}

.list-group-item small {
    font-size: 0.8125rem;
    line-height: 1.5;
}

/* Quick Actions */
.d-grid.gap-2 {
    gap: 0.75rem;
}

.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    padding: 0.625rem 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-width: 1px;
    font-size: 0.875rem;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn-outline-info,
.btn-outline-warning,
.btn-outline-secondary,
.btn-outline-primary,
.btn-outline-success {
    transition: all 0.2s ease;
}

.btn-outline-info:hover,
.btn-outline-warning:hover,
.btn-outline-secondary:hover,
.btn-outline-primary:hover,
.btn-outline-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    border-width: 2px;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    border-radius: 0.375rem;
}

/* Empty States */
.text-center.py-5 {
    padding: 3rem 2rem;
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    border-radius: 0.75rem;
    border: 2px dashed var(--gray-300);
    margin: 1rem 0;
    transition: all 0.3s ease;
}

.text-center.py-5:hover {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, var(--white) 0%, var(--gray-50) 100%);
}

.text-center.py-5 i {
    font-size: 3.5rem;
    color: var(--gray-500);
    display: block;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.text-center.py-5:hover i {
    color: var(--primary-color);
    transform: scale(1.05);
}

.text-center.py-5 p {
    color: var(--text-muted);
    margin-bottom: 1rem;
    font-size: 0.9375rem;
    font-weight: 500;
}

.text-center.py-3 {
    padding: 2rem 1rem;
    background: var(--gray-50);
    border-radius: 0.5rem;
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .stat-card-hover h3 {
        font-size: 1.5rem;
    }
}

@media (max-width: 992px) {
    .header-gradient-card h1 {
        font-size: 1.75rem;
    }
    
    .stat-card-hover .card-body {
        padding: 1rem;
    }
    
    .stat-card-hover h3 {
        font-size: 1.5rem;
    }
    
    .stat-card-hover .bg-gradient {
        width: 48px;
        height: 48px;
        padding: 0.75rem !important;
    }
    
    .stat-card-hover .fs-4 {
        font-size: 1rem !important;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .col-lg-4 {
        margin-top: 1rem;
    }
}

@media (max-width: 768px) {
    .header-gradient-card .card-body {
        padding: 1.5rem !important;
    }
    
    .header-gradient-card h1 {
        font-size: 1.5rem;
    }
    
    .header-gradient-card h1 .bi {
        font-size: 1.25rem;
    }
    
    .header-gradient-card p {
        font-size: 0.875rem;
    }
    
    .card-header {
        padding: 1rem 1.25rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .stat-card-hover h3 {
        font-size: 1.25rem;
    }
    
    .stat-card-hover small {
        font-size: 0.75rem;
    }
    
    .stat-card-hover .bg-gradient {
        width: 40px;
        height: 40px;
        padding: 0.625rem !important;
    }
    
    .accordion-button {
        padding: 0.875rem 1rem;
        font-size: 0.875rem;
    }
    
    .accordion-body {
        padding: 1rem;
    }
    
    .list-group-item {
        padding: 0.875rem;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
    
    .flex-wrap {
        flex-wrap: wrap !important;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    
    .header-gradient-card .card-body {
        padding: 1.25rem !important;
    }
    
    .header-gradient-card h1 {
        font-size: 1.25rem;
    }
    
    .header-gradient-card h1 .bi {
        font-size: 1rem;
        margin-right: 0.5rem !important;
    }
    
    [dir="rtl"] .header-gradient-card h1 .bi {
        margin-left: 0.5rem !important;
        margin-right: 0 !important;
    }
    
    .row.g-3,
    .row.g-4 {
        --bs-gutter-y: 0.75rem;
        --bs-gutter-x: 0.75rem;
    }
    
    .card-body {
        padding: 0.875rem;
    }
    
    .card-header {
        padding: 0.875rem 1rem;
    }
    
    .stat-card-hover .card-body {
        padding: 0.75rem;
    }
    
    .stat-card-hover h3 {
        font-size: 1rem;
    }
    
    .stat-card-hover small {
        font-size: 0.7rem;
    }
    
    .stat-card-hover .bg-gradient {
        width: 32px;
        height: 32px;
        padding: 0.5rem !important;
    }
    
    .stat-card-hover .bg-gradient i {
        font-size: 0.875rem !important;
    }
    
    .accordion-button {
        padding: 0.75rem 0.875rem;
        font-size: 0.8125rem;
    }
    
    .accordion-button .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .accordion-body {
        padding: 0.875rem;
    }
    
    .list-group-item {
        padding: 0.75rem;
    }
    
    .text-center.py-5 {
        padding: 2rem 1rem;
    }
    
    .text-center.py-5 i {
        font-size: 2.5rem;
    }
    
    .text-center.py-3 {
        padding: 1.5rem 0.75rem;
    }
    
    .gap-2 {
        gap: 0.375rem !important;
    }
    
    .gap-3 {
        gap: 0.75rem !important;
    }
    
    .btn {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    .btn i {
        font-size: 0.875rem;
    }
}

/* RTL Support */
/* RTL Support */
[dir="rtl"] .header-gradient-card .text-md-end {
    text-align: left !important;
}

[dir="rtl"] .me-1 {
    margin-right: 0 !important;
    margin-left: 0.25rem !important;
}

[dir="rtl"] .me-2 {
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

[dir="rtl"] .ms-1 {
    margin-left: 0 !important;
    margin-right: 0.25rem !important;
}

[dir="rtl"] .ms-2 {
    margin-left: 0 !important;
    margin-right: 0.5rem !important;
}

[dir="rtl"] .breadcrumb-item + .breadcrumb-item::before {
    content: "‹";
    padding: 0 0.5rem;
}

[dir="rtl"] .text-start {
    text-align: right !important;
}

[dir="rtl"] .text-end {
    text-align: left !important;
}

[dir="rtl"] .accordion-button::after {
    margin-left: 0;
    margin-right: auto;
}

/* Bootstrap Overrides for Better Integration */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    text-decoration: none;
}

.btn i {
    flex-shrink: 0;
}

.btn-sm {
    font-size: 0.875rem;
    line-height: 1.5;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

/* Accordion Improvements */
.accordion-button {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
}

.accordion-button .badge {
    flex-shrink: 0;
    font-size: 0.875rem;
    padding: 0.35rem 0.65rem;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(to bottom, rgba(13, 110, 253, 0.08) 0%, var(--white) 100%);
    color: var(--primary-color);
    font-weight: 600;
    box-shadow: inset 0 -1px 0 rgba(13, 110, 253, 0.15);
}

.accordion-item {
    border: 1px solid rgba(0, 0, 0, 0.125);
    margin-bottom: 0.5rem;
    border-radius: 0.375rem !important;
    overflow: visible !important;
}

.accordion-item:last-child {
    margin-bottom: 0;
}

.accordion-body {
    padding: 1.5rem;
}

/* Card Header Improvements */
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    background-color: #f8f9fa;
}

.card-header h3,
.card-header h5,
.card-header h6 {
    margin-bottom: 0;
}

/* Improved Responsive Utilities */
@media (max-width: 576px) {
    .d-none.d-sm-inline {
        display: none !important;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8125rem;
    }
    
    .accordion-button {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
}

/* Better List Group Item Styling */
.list-group-item .min-w-0 {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
}

.list-group-item {
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

/* Flex Utilities */
.flex-shrink-0 {
    flex-shrink: 0 !important;
}

.flex-grow-1 {
    flex-grow: 1 !important;
}

.gap-2 {
    gap: 0.5rem !important;
}

.gap-3 {
    gap: 1rem !important;
}

/* Better Button Alignment */
.d-grid .btn {
    justify-content: flex-start;
    padding-left: 1rem;
    padding-right: 1rem;
}

[dir="rtl"] .d-grid .btn {
    justify-content: flex-start;
}

/* Card Body Improvements */
.card-body {
    background: #ffffff;
}

.card {
    background: #ffffff;
}

/* Better Shadow for Cards */
.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

/* Improved Text Muted */
.text-muted {
    color: #6c757d !important;
}

/* Better Small Text */
small.text-muted {
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Improved Badge Styling */
.badge {
    font-weight: 500;
    letter-spacing: 0.025em;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
    color: #000;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-primary {
    background-color: #0d6efd !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* Breadcrumb Improvements */
.breadcrumb {
    margin-bottom: 0;
    background: transparent;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    list-style: none;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item a {
    text-decoration: none;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
    opacity: 0.9;
}

.breadcrumb-item.active {
    font-weight: 500;
}

/* Icon Improvements */
.bi {
    vertical-align: middle;
}

/* Better Hover Effects */
.card:hover {
    transition: all 0.3s ease;
}

.list-group-item-action {
    cursor: pointer;
    transition: all 0.2s ease;
}

.list-group-item-action:hover {
    background-color: var(--gray-50);
    transform: translateX(3px);
}

[dir="rtl"] .list-group-item-action:hover {
    transform: translateX(-3px);
}

/* Better Button Styling */
.btn-outline-info:hover {
    color: #000;
}

.btn-outline-warning:hover {
    color: #000;
}

/* Ensure all buttons are clickable and properly styled */
.btn,
.btn-sm,
.btn-outline-primary,
.btn-outline-info,
.btn-outline-warning,
.btn-outline-secondary,
.btn-outline-success,
.btn-success,
.btn-info,
.btn-primary {
    cursor: pointer !important;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    pointer-events: auto !important;
    position: relative;
    z-index: 10 !important;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    text-decoration: none !important;
}

/* Ensure buttons inside list-group-items are clickable */
.list-group-item .btn,
.list-group-item .btn-sm {
    z-index: 20 !important;
    pointer-events: auto !important;
    cursor: pointer !important;
    position: relative;
}

/* Ensure accordion body doesn't block buttons */
.accordion-body {
    position: relative;
    z-index: 1;
}

.accordion-body .btn {
    z-index: 20 !important;
}

.btn:disabled,
.btn.disabled {
    cursor: not-allowed;
    opacity: 0.65;
    pointer-events: none;
}

/* Ensure Link components styled as buttons work properly */
a.btn,
a.btn-sm {
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

a.btn:hover,
a.btn-sm:hover {
    text-decoration: none;
    transform: translateY(-1px);
}

a.btn:active,
a.btn-sm:active {
    transform: translateY(0);
}

/* Button hover effects */
.btn-outline-primary:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.btn-outline-info:hover {
    background-color: var(--info-color);
    border-color: var(--info-color);
    color: #000;
}

.btn-outline-warning:hover {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: #000;
}

.btn-outline-secondary:hover {
    background-color: var(--gray-600);
    border-color: var(--gray-600);
    color: white;
}

.btn-outline-success:hover {
    background-color: var(--success-color);
    border-color: var(--success-color);
    color: white;
}

.btn-success:hover {
    background-color: #157347;
    border-color: #146c43;
}

.btn-info:hover {
    background-color: var(--info-dark);
    border-color: var(--info-dark);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
}

/* Accordion button improvements */
.accordion-button {
    cursor: pointer;
    user-select: none;
}

.accordion-button:not(.collapsed)::after {
    transform: rotate(-180deg);
}

[dir="rtl"] .accordion-button:not(.collapsed)::after {
    transform: rotate(180deg);
}

/* Responsive Image */
.course-thumbnail-image {
    object-fit: cover;
    width: 100%;
    height: auto;
}

/* Improved Spacing */
.mb-1 {
    margin-bottom: 0.25rem !important;
}

.mb-2 {
    margin-bottom: 0.5rem !important;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}

/* Better Alignment */
.align-items-center {
    align-items: center !important;
}

.align-items-start {
    align-items: flex-start !important;
}

.justify-content-between {
    justify-content: space-between !important;
}

.justify-content-start {
    justify-content: flex-start !important;
}

/* Custom Button Styles - Matching Image Style */
.manage-sections-btn,
a.manage-sections-btn {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%) !important;
    border: none !important;
    color: white !important;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
    cursor: pointer !important;
    pointer-events: auto !important;
    z-index: 10 !important;
    position: relative;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
}

.manage-sections-btn:hover,
a.manage-sections-btn:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 50%, #3730a3 100%) !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    text-decoration: none !important;
}

.lesson-action-btn-add,
a.lesson-action-btn-add {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%) !important;
    border: none !important;
    color: white !important;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
    cursor: pointer !important;
    pointer-events: auto !important;
    z-index: 10 !important;
    position: relative;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
}

.lesson-action-btn-add:hover,
a.lesson-action-btn-add:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%) !important;
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
    text-decoration: none !important;
}

.lesson-action-btn-view,
a.lesson-action-btn-view {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%) !important;
    border: none !important;
    color: white !important;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(139, 92, 246, 0.3);
    cursor: pointer !important;
    pointer-events: auto !important;
    z-index: 10 !important;
    position: relative;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
}

.lesson-action-btn-view:hover,
a.lesson-action-btn-view:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #5b21b6 100%) !important;
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(139, 92, 246, 0.4);
    text-decoration: none !important;
}

.lesson-action-btn-view .badge {
    background-color: rgba(255, 255, 255, 0.25) !important;
    color: white !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    font-weight: 600;
    padding: 0.125rem 0.5rem;
}

/* Accordion button improvements */
.accordion-button {
    cursor: pointer !important;
    user-select: none;
    pointer-events: auto !important;
    z-index: 5 !important;
    position: relative;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25) !important;
    border-color: transparent;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(to bottom, rgba(99, 102, 241, 0.08) 0%, var(--white) 100%) !important;
    color: #6366f1 !important;
    font-weight: 600;
    box-shadow: inset 0 -1px 0 rgba(99, 102, 241, 0.15);
}
</style>

