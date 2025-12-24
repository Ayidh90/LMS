<template>
    <AppLayout>
        <!-- Hero Section with Enhanced Professional Styling -->
        <section class="relative bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 text-white py-32 md:py-40 lg:py-48 overflow-hidden">
            <!-- Enhanced Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-pattern"></div>
                <!-- Animated Gradient Orbs -->
                <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-0 left-1/2 w-[500px] h-[500px] bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
                <!-- Decorative Grid Pattern -->
                <div class="absolute inset-0 opacity-5" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            
            <!-- Floating Decorative Elements -->
            <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-float"></div>
            <div class="absolute top-40 right-20 w-32 h-32 bg-purple-300/20 rounded-full blur-2xl animate-float animation-delay-3000"></div>
            <div class="absolute bottom-20 left-1/4 w-24 h-24 bg-blue-300/20 rounded-full blur-xl animate-float animation-delay-1500"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="animate-fade-in-up">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 bg-white/10 backdrop-blur-md rounded-full border border-white/20 animate-fade-in">
                        <i class="pi pi-star-fill text-yellow-300"></i>
                        <span class="text-sm font-medium">{{ t('welcome.featured_courses') }}</span>
                    </div>
                    
                    <h1 class="text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-extrabold mb-6 leading-tight tracking-tight">
                        <span class="block bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent drop-shadow-lg">
                            {{ t('welcome.title') }}
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl lg:text-3xl mb-12 text-blue-50 max-w-3xl mx-auto leading-relaxed font-light drop-shadow-md">
                        {{ t('welcome.subtitle') }}
                    </p>
                <div class="flex justify-center gap-4 flex-wrap">
                    <Link
                        v-if="!auth"
                        :href="route('register')"
                    >
                            <Button 
                                :label="t('welcome.get_started')" 
                                icon="pi pi-user-plus" 
                                size="large" 
                                class="p-button-lg hero-cta-primary shadow-2xl hover:shadow-blue-500/50 transition-all duration-300 transform hover:scale-105" 
                            />
                    </Link>
                    <Link :href="route('welcome')">
                            <Button 
                                :label="t('welcome.browse_courses')" 
                                icon="pi pi-book" 
                                size="large" 
                                outlined 
                                class="p-button-lg hero-cta-secondary border-2 hover:bg-white/10 transition-all duration-300 transform hover:scale-105" 
                            />
                    </Link>
                </div>
                    
                    <!-- Stats Section -->
                    <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold mb-2">{{ courses?.length || 0 }}+</div>
                            <div class="text-sm md:text-base text-blue-100">{{ t('courses.title') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold mb-2">{{ categories?.length || 0 }}+</div>
                            <div class="text-sm md:text-base text-blue-100">{{ t('welcome.browse_by_category') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold mb-2">24/7</div>
                            <div class="text-sm md:text-base text-blue-100">Access</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold mb-2">100%</div>
                            <div class="text-sm md:text-base text-blue-100">{{ t('courses.free') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <div class="flex flex-col items-center gap-2">
                    <span class="text-xs text-blue-200 font-medium">Scroll</span>
                    <i class="pi pi-chevron-down text-2xl opacity-70"></i>
                </div>
            </div>
            
            <!-- Wave Divider -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg class="w-full h-20 fill-white" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M0,0 C150,100 350,0 500,50 C650,100 850,0 1000,50 C1100,75 1150,50 1200,50 L1200,120 L0,120 Z"></path>
                </svg>
            </div>
        </section>

        <!-- Categories Section with Enhanced Professional Styling -->
        <section class="py-20 md:py-28 bg-gradient-to-b from-white via-gray-50 to-white" v-if="categories && categories.length > 0" :dir="direction">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-6 bg-blue-50 rounded-full">
                        <i class="pi pi-th-large text-blue-600"></i>
                        <span class="text-sm font-semibold text-blue-600 uppercase tracking-wide">{{ t('welcome.browse_by_category') }}</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-6">
                        <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            {{ t('welcome.browse_by_category') }}
                        </span>
                    </h2>
                    <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">{{ t('welcome.browse_by_category_desc') }}</p>
                </div>
                
                <!-- Categories Slider -->
                <div class="relative">
                    <!-- Navigation Buttons -->
                    <Button
                        v-if="canScrollLeft"
                        @click="scrollCategories('left')"
                        icon="pi pi-chevron-left"
                        rounded
                        text
                        severity="secondary"
                        class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-14 h-14 shadow-xl bg-white hover:bg-gray-50 transition-all duration-300 border border-gray-200"
                    />
                    <Button
                        v-if="canScrollRight"
                        @click="scrollCategories('right')"
                        icon="pi pi-chevron-right"
                        rounded
                        text
                        severity="secondary"
                        class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-14 h-14 shadow-xl bg-white hover:bg-gray-50 transition-all duration-300 border border-gray-200"
                    />

                    <!-- Slider Container -->
                    <div
                        ref="categoriesSlider"
                        class="flex gap-6 overflow-x-auto scrollbar-hide scroll-smooth pb-6 px-2"
                        style="scrollbar-width: none; -ms-overflow-style: none;"
                        @scroll="updateScrollButtons"
                    >
                        <Card
                            v-for="(category, index) in categories"
                            :key="category.id"
                            class="flex-shrink-0 w-56 sm:w-64 cursor-pointer category-card group relative overflow-hidden"
                            @click="filterByCategory(category.id)"
                            :style="{ animationDelay: `${index * 100}ms` }"
                        >
                            <!-- Hover Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-purple-500/0 group-hover:from-blue-500/10 group-hover:to-purple-500/10 transition-all duration-500 z-0"></div>
                            
                            <template #content>
                                <div class="text-center py-8 relative z-10">
                                    <div class="inline-flex items-center justify-center w-20 h-20 mb-6 bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl group-hover:from-blue-100 group-hover:to-purple-100 transition-all duration-300">
                                        <div class="text-5xl transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                            {{ category.icon || '📚' }}
                                        </div>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-3 text-lg group-hover:text-blue-600 transition-colors duration-300">
                                        {{ category.translated_name }}
                                    </h3>
                                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-gray-100 rounded-full group-hover:bg-blue-50 transition-colors duration-300">
                                        <i class="pi pi-book text-xs text-gray-600 group-hover:text-blue-600"></i>
                                        <p class="text-sm text-gray-700 group-hover:text-blue-700 font-semibold">
                                            {{ category.courses_count || 0 }} {{ t('courses.title') }}
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </section>

        <!-- Courses Section with Enhanced Professional Styling -->
        <section class="py-20 md:py-28 bg-gradient-to-b from-white via-gray-50 to-white relative overflow-hidden" :dir="direction">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100/30 rounded-full blur-3xl -z-0"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-100/30 rounded-full blur-3xl -z-0"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-12 gap-6" :dir="direction">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-2 px-4 py-2 mb-4 bg-purple-50 rounded-full">
                            <i class="pi pi-star-fill text-purple-600"></i>
                            <span class="text-sm font-semibold text-purple-600 uppercase tracking-wide">{{ t('welcome.featured_courses') }}</span>
                        </div>
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-4">
                            <span class="bg-gradient-to-r from-gray-900 via-blue-600 to-purple-600 bg-clip-text text-transparent">
                                {{ t('welcome.featured_courses') }}
                            </span>
                        </h2>
                        <p class="text-lg md:text-xl text-gray-600 max-w-2xl leading-relaxed">{{ t('welcome.featured_courses_desc') }}</p>
                    </div>
                    <div class="flex-shrink-0">
                    <Link :href="route('welcome')">
                            <Button 
                                :label="t('welcome.view_all')" 
                                :icon="direction === 'rtl' ? 'pi pi-arrow-left' : 'pi pi-arrow-right'" 
                                :iconPos="direction === 'rtl' ? 'left' : 'right'" 
                                outlined
                                severity="secondary"
                                class="view-all-button font-semibold"
                            />
                    </Link>
                    </div>
                </div>
                <div v-if="courses && courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <Card
                        v-for="(course, index) in courses"
                        :key="course.id"
                        class="overflow-hidden course-card group"
                        :style="{ animationDelay: `${index * 100}ms` }"
                    >
                        <template #header>
                            <div class="h-52 bg-gradient-to-br from-gray-200 via-gray-300 to-gray-400 relative overflow-hidden group-hover:shadow-2xl transition-shadow duration-500">
                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/5 to-transparent z-10 group-hover:from-black/40 transition-all duration-500"></div>
                                <!-- Shine Effect -->
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 z-10"></div>
                                
                                <img 
                                    :src="getCourseImage(course)" 
                                    :alt="course.translated_title" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
                                    @error="handleImageError($event)" 
                                />
                                
                                
                                <!-- Level Badge (Top Right - Oval/Gray) -->
                                <div class="absolute top-3 right-3 z-20">
                                    <div class="bg-gray-700/80 backdrop-blur-sm text-white px-4 py-1.5 rounded-full text-xs font-semibold capitalize shadow-lg">
                                        {{ course.level }}
                                    </div>
                                </div>
                                
                                <!-- Bookmark/Favorite Icon -->
                                <div 
                                    v-if="auth"
                                    class="absolute top-3 right-3 z-20"
                                    @click.stop="toggleFavorite(course.slug || course.id)"
                                >
                                    <div class="bg-white/90 backdrop-blur-sm rounded-full p-2 shadow-lg cursor-pointer hover:bg-white transition-colors opacity-80 hover:opacity-100">
                                        <i 
                                            :class="isFavorited(course.id) ? 'pi pi-bookmark-fill text-blue-600' : 'pi pi-bookmark text-gray-700'" 
                                            class="text-lg transition-colors duration-300"
                                        ></i>
                                    </div>
                                </div>
                                
                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-blue-600/0 group-hover:bg-blue-600/10 transition-all duration-500 z-10"></div>
                            </div>
                        </template>
                        <template #content>
                            <div class="p-5">
                                <!-- Category Tag (Below Image) -->
                                <div class="mb-3">
                                    <div v-if="course.category" class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 rounded-md">
                                        <span class="text-xs text-blue-700 font-semibold">{{ course.category.translated_name }}</span>
                            </div>
                                </div>
                                
                                <!-- Title -->
                                <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 min-h-[3.5rem] group-hover:text-blue-600 transition-colors duration-300 leading-tight">
                                    {{ course.translated_title }}
                                </h3>
                                
                                <!-- Description -->
                                <p class="text-gray-600 text-sm mb-5 line-clamp-2 min-h-[2.5rem] leading-relaxed">
                                    {{ course.translated_description }}
                                </p>
                                
                                <!-- Instructor Section -->
                                <div class="flex items-center gap-3 mb-5 pt-4 border-t border-gray-100">
                                    <Avatar 
                                        :label="getInitials(course.instructor?.name)" 
                                        shape="circle" 
                                        size="normal" 
                                        class="bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 text-white font-bold shadow-md ring-2 ring-blue-100 flex-shrink-0" 
                                    />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate mb-0.5">
                                            {{ course.instructor?.name || t('courses.unknown_instructor') }}
                                        </p>
                                        <p class="text-xs text-gray-500 font-medium">{{ t('courses.fields.instructor') }}</p>
                            </div>
                                </div>
                                
                                <!-- Action Buttons (RTL-aware) -->
                                <div class="flex gap-2" :class="direction === 'rtl' ? 'flex-row-reverse' : ''">
                                    <!-- View Button -->
                                    <Link :href="route('courses.show', course.slug || course.id)" class="flex-1">
                                        <Button 
                                            :label="t('courses.actions.view')" 
                                            icon="pi pi-eye"
                                            class="w-full view-button" 
                                            size="small" 
                                            outlined
                                            severity="secondary"
                                        />
                                    </Link>
                                    
                                    <!-- Enroll Button (if not enrolled) -->
                                    <Button
                                        v-if="!isEnrolled(course.id)"
                                        @click="enroll(course.slug || course.id)"
                                        :label="t('courses.actions.enroll')"
                                        icon="pi pi-check-circle"
                                        severity="primary"
                                        size="small"
                                        class="flex-1 enroll-button font-semibold"
                                    />
                                    
                                    <!-- Enrolled Button (if already enrolled) -->
                                    <Button
                                        v-else
                                        :label="t('courses.actions.enrolled')"
                                        icon="pi pi-check-circle"
                                        severity="secondary"
                                        size="small"
                                        class="flex-1 enrolled-button font-semibold"
                                        disabled
                                    />
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
                <div v-else class="text-center py-20">
                    <Card class="max-w-md mx-auto">
                        <template #content>
                            <div class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-100 mb-6">
                                    <i class="pi pi-book text-5xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-600 text-xl mb-6 font-medium">{{ t('welcome.no_courses') }}</p>
                                <Link :href="route('welcome')">
                                    <Button 
                                        :label="t('welcome.browse_courses')" 
                                        icon="pi pi-arrow-right"
                                        iconPos="right"
                                        class="shadow-lg"
                                    />
                                </Link>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </section>

        <!-- FAQs Section with Enhanced Professional Styling -->
        <section class="py-20 md:py-28 bg-gradient-to-b from-gray-50 via-white to-gray-50 relative overflow-hidden" v-if="faqs && faqs.length > 0" :dir="direction">
            <!-- Background Decoration -->
            <div class="absolute top-1/2 left-0 w-64 h-64 bg-indigo-100/40 rounded-full blur-3xl -z-0"></div>
            <div class="absolute bottom-0 right-0 w-64 h-64 bg-blue-100/40 rounded-full blur-3xl -z-0"></div>
            
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-6 bg-indigo-50 rounded-full">
                        <i class="pi pi-question-circle text-indigo-600"></i>
                        <span class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">{{ t('welcome.faqs') }}</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-4">
                        <span class="bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600 bg-clip-text text-transparent">
                            {{ t('welcome.faqs') }}
                        </span>
                    </h2>
                    <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">{{ t('welcome.faqs_desc') }}</p>
                </div>
                <div class="space-y-4">
                    <Panel
                        v-for="(faq, index) in faqs"
                        :key="faq.id"
                        :header="faq.translated_question"
                        :toggleable="true"
                        class="faq-panel mb-4 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-200"
                        :style="{ animationDelay: `${index * 100}ms` }"
                    >
                        <template #header>
                            <div class="flex items-center gap-4 w-full">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-md">
                                    <i class="pi pi-question-circle text-white text-lg"></i>
                                </div>
                                <span class="font-bold text-gray-900 text-lg leading-tight flex-1">{{ faq.translated_question }}</span>
                            </div>
                        </template>
                        <div class="pl-14 pr-4 py-2">
                            <p class="text-gray-700 leading-relaxed text-base">{{ faq.translated_answer }}</p>
                        </div>
                    </Panel>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import { useDirection } from '../composables/useDirection';
import { useTranslation } from '../composables/useTranslation';
import { useRoute } from '../composables/useRoute';
import { useAlert } from '../composables/useAlert';
import { usePage, Link, router } from '@inertiajs/vue3';

defineProps({
    categories: Array,
    courses: Array,
    faqs: Array,
    enrolledCourseIds: Array,
    favoriteCourseIds: Array,
});

const { direction } = useDirection();
const { t } = useTranslation();
const { route } = useRoute();
const { showSuccess, showError, showWarning } = useAlert();
const page = usePage();

const auth = computed(() => page.props.auth?.user);
const enrolledCourseIds = computed(() => page.props.enrolledCourseIds || []);
const favoriteCourseIds = computed(() => page.props.favoriteCourseIds || []);

const categoriesSlider = ref(null);
const canScrollLeft = ref(false);
const canScrollRight = ref(true);

// Default course image
const defaultCourseImage = '/images/default-course.avif';

const getCourseImage = (course) => {
    return course.thumbnail_url || course.thumbnail || defaultCourseImage;
};

const handleImageError = (event) => {
    if (event.target.src !== defaultCourseImage) {
        event.target.src = defaultCourseImage;
    }
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const isEnrolled = (courseId) => {
    return enrolledCourseIds.value.includes(courseId);
};

const isFavorited = (courseId) => {
    return favoriteCourseIds.value.includes(courseId);
};

const enroll = (courseId) => {
    if (!auth.value) {
        router.visit(route('login'));
        return;
    }

    router.post(route('courses.enroll', { course: courseId }), {}, {
        preserveScroll: true,
        onSuccess: () => {
            const message = t('courses.enrolled_successfully') || 'Successfully enrolled in course!';
            showSuccess(message, t('common.success') || 'Success');
            router.reload({ only: ['enrolledCourseIds'] });
        },
        onError: (errors) => {
            if (errors.redirect) {
                router.visit(errors.redirect);
            } else {
                const message = errors.message || t('courses.enroll_failed') || 'Failed to enroll in course';
                showError(message, t('common.error') || 'Error');
            }
        },
    });
};

const toggleFavorite = (courseId) => {
    if (!auth.value) {
        const message = t('common.login_required') || 'Please login to add favorites';
        showWarning(message, t('common.warning') || 'Warning');
        router.visit(route('login'));
        return;
    }

    router.post(route('courses.favorite.toggle', { course: courseId }), {}, {
        preserveScroll: true,
        onSuccess: (page) => {
            const isFavoritedNow = page.props.isFavorited || false;
            const favoriteKey = isFavoritedNow ? 'courses.favorite_added' : 'courses.favorite_removed';
            const message = t(favoriteKey) || (isFavoritedNow ? 'Course added to favorites!' : 'Course removed from favorites');
            
            showSuccess(message, t('common.success') || 'Success');
            router.reload({ only: ['favoriteCourseIds'] });
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

const filterByCategory = (categoryId) => {
    const baseUrl = route('welcome');
    const queryParams = categoryId ? { category: categoryId } : {};
    const queryString = new URLSearchParams(queryParams).toString();
    const url = queryString ? `${baseUrl}?${queryString}` : baseUrl;
    
    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
    });
};

const scrollCategories = (dir) => {
    if (!categoriesSlider.value) return;
    
    const scrollAmount = 300;
    const isRTL = direction.value === 'rtl';
    
    let scrollValue;
    if (isRTL) {
        scrollValue = dir === 'left' ? scrollAmount : -scrollAmount;
    } else {
        scrollValue = dir === 'right' ? scrollAmount : -scrollAmount;
    }
    
    categoriesSlider.value.scrollBy({
        left: scrollValue,
        behavior: 'smooth'
    });
};

const updateScrollButtons = () => {
    if (!categoriesSlider.value) return;
    
    const { scrollLeft, scrollWidth, clientWidth } = categoriesSlider.value;
    const isRTL = direction.value === 'rtl';
    const maxScroll = scrollWidth - clientWidth;
    const tolerance = 1;
    
    if (isRTL) {
        canScrollRight.value = scrollLeft > tolerance;
        canScrollLeft.value = scrollLeft < (maxScroll - tolerance);
    } else {
        canScrollLeft.value = scrollLeft > tolerance;
        canScrollRight.value = scrollLeft < (maxScroll - tolerance);
    }
};

onMounted(() => {
    if (categoriesSlider.value) {
        updateScrollButtons();
        categoriesSlider.value.addEventListener('scroll', updateScrollButtons);
        window.addEventListener('resize', updateScrollButtons);
    }
});

onUnmounted(() => {
    if (categoriesSlider.value) {
        categoriesSlider.value.removeEventListener('scroll', updateScrollButtons);
        window.removeEventListener('resize', updateScrollButtons);
    }
});
</script>

<style scoped>
/* Scrollbar Hide */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Hero Section Styles */
.hero-cta-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 700;
    padding: 1rem 2.5rem;
    letter-spacing: 0.5px;
    box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
}

.hero-cta-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 15px 35px -5px rgba(102, 126, 234, 0.5);
}

.hero-cta-secondary {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(12px);
    font-weight: 700;
    padding: 1rem 2.5rem;
    letter-spacing: 0.5px;
    box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.2);
}

.hero-cta-secondary:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.6);
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 12px 30px -5px rgba(0, 0, 0, 0.3);
}

/* Card Styles */
:deep(.p-card) {
    border-radius: 16px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(229, 231, 235, 0.8);
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08), 0 1px 4px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

:deep(.p-card:hover) {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12), 0 4px 8px rgba(0, 0, 0, 0.08);
    border-color: rgba(59, 130, 246, 0.15);
}

.category-card {
    animation: fade-in-up 0.6s ease-out both;
}

.category-card:hover {
    transform: translateY(-12px) scale(1.03);
    box-shadow: 0 30px 60px -12px rgba(59, 130, 246, 0.2);
}

.course-card {
    animation: fade-in-up 0.6s ease-out both;
}

.course-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12), 0 4px 8px rgba(0, 0, 0, 0.08);
}

/* Level Badge Styling */
:deep(.p-card .absolute.top-3.right-3) {
    background: rgba(55, 65, 81, 0.85) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

/* Category Tag Styling */
:deep(.p-card .bg-blue-50) {
    background: #eff6ff;
    border: none;
}

/* Course Card Content Padding */
:deep(.p-card .p-card-content) {
    padding: 0;
}

/* Button Improvements - Blue/Purple Theme */
.enroll-button {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    border: none;
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0.5rem 1rem;
}

.enroll-button:hover {
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    transform: translateY(-2px) scale(1.02);
}

.enroll-button:active {
    transform: translateY(0) scale(0.98);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.enroll-button :deep(.p-button-icon) {
    font-size: 0.875rem;
    margin-right: 0.375rem;
}

[dir="rtl"] .enroll-button :deep(.p-button-icon) {
    margin-right: 0;
    margin-left: 0.375rem;
}

.enrolled-button {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
    font-weight: 600;
    cursor: not-allowed;
    opacity: 0.8;
}

.enrolled-button :deep(.p-button-icon) {
    font-size: 0.875rem;
    margin-right: 0.375rem;
}

[dir="rtl"] .enrolled-button :deep(.p-button-icon) {
    margin-right: 0;
    margin-left: 0.375rem;
}

.view-button {
    color: #374151;
    border-color: #d1d5db;
    background: transparent;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0.5rem 1rem;
}

.view-button:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
}

.view-button:active {
    transform: translateY(0);
    box-shadow: 0 1px 4px rgba(59, 130, 246, 0.1);
}

.view-button :deep(.p-button-icon) {
    font-size: 0.875rem;
    margin-right: 0.375rem;
}

[dir="rtl"] .view-button :deep(.p-button-icon) {
    margin-right: 0;
    margin-left: 0.375rem;
}

.view-all-button {
    color: #3b82f6;
    border-color: #3b82f6;
    background: transparent;
    padding: 0.625rem 1.5rem;
    font-size: 0.9375rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-width: 2px;
}

.view-all-button:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    color: white;
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.view-all-button:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2);
}

.view-all-button :deep(.p-button-icon) {
    font-size: 0.875rem;
}

.view-all-button :deep(.p-button-label) {
    font-weight: 600;
}

:deep(.p-button.p-button-primary) {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    border: none;
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

:deep(.p-button.p-button-primary:hover) {
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    transform: translateY(-2px);
}

:deep(.p-button.p-button-outlined.p-button-secondary) {
    color: #374151;
    border-color: #d1d5db;
    background: transparent;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

:deep(.p-button.p-button-outlined.p-button-secondary:hover) {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
}

/* Icon sizing for all buttons */
:deep(.p-button .p-button-icon) {
    font-size: 0.875rem;
}

:deep(.p-button.p-button-sm .p-button-icon) {
    font-size: 0.75rem;
}

/* Button label spacing */
:deep(.p-button-icon-left .p-button-label) {
    margin-left: 0.5rem;
}

:deep(.p-button-icon-right .p-button-label) {
    margin-right: 0.5rem;
}

[dir="rtl"] :deep(.p-button-icon-left .p-button-label) {
    margin-left: 0;
    margin-right: 0.5rem;
}

[dir="rtl"] :deep(.p-button-icon-right .p-button-label) {
    margin-right: 0;
    margin-left: 0.5rem;
}

/* Panel/FAQ Styles */
.faq-panel {
    animation: fade-in-up 0.6s ease-out both;
    border-radius: 16px;
    overflow: hidden;
    background: white;
    border: 1px solid rgba(229, 231, 235, 0.8);
}

:deep(.p-panel-header) {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px 16px 0 0;
    padding: 1.5rem 2rem;
    border-bottom: 2px solid #e5e7eb;
    transition: all 0.3s ease;
    position: relative;
}

:deep(.p-panel-header:hover) {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom-color: #cbd5e1;
}

:deep(.p-panel-content) {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
    border-radius: 0 0 16px 16px;
}

:deep(.p-panel-toggleable-header) {
    cursor: pointer;
    position: relative;
}

:deep(.p-panel-header[aria-expanded="true"]) {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-bottom-color: #93c5fd;
}

/* FAQ Toggle Icon Styling - Blue Gradient Circle */
:deep(.p-panel-toggleable-header .p-panel-header-icon) {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    margin-left: auto;
    flex-shrink: 0;
}

[dir="rtl"] :deep(.p-panel-toggleable-header .p-panel-header-icon) {
    margin-left: 0;
    margin-right: auto;
}

:deep(.p-panel-toggleable-header .p-panel-header-icon:hover) {
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

:deep(.p-panel-toggleable-header .p-panel-header-icon:active) {
    transform: scale(0.95);
}

:deep(.p-panel-header[aria-expanded="true"] .p-panel-header-icon) {
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    transform: rotate(45deg);
}

/* FAQ Panel Header Content Layout */
:deep(.p-panel-toggleable-header) {
    display: flex;
    align-items: center;
    gap: 1rem;
    width: 100%;
}

:deep(.p-panel-toggleable-header > div) {
    flex: 1;
    min-width: 0;
}

/* RTL Support for FAQ Panels */
[dir="rtl"] :deep(.p-panel-header) {
    padding: 1.5rem 2rem;
}

[dir="rtl"] :deep(.faq-panel .pl-14) {
    padding-left: 1rem;
    padding-right: 3.5rem;
}

/* Animations */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(5deg);
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out;
}

.animate-blob {
    animation: blob 7s infinite ease-in-out;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animation-delay-3000 {
    animation-delay: 3s;
}

.animation-delay-1500 {
    animation-delay: 1.5s;
}

/* Button Enhancements */
:deep(.p-button) {
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.025em;
}

:deep(.p-button:not(:disabled):hover) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

:deep(.p-button:not(:disabled):active) {
    transform: translateY(0) scale(0.98);
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.15);
}

:deep(.p-button.p-button-success) {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    border: none;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
    color: white;
}

:deep(.p-button.p-button-success:hover) {
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    transform: translateY(-2px);
}

/* Badge and Tag Enhancements */
:deep(.p-badge) {
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    font-size: 0.7rem;
    padding: 0.375rem 0.75rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

:deep(.p-badge.p-badge-info) {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    border: none;
    color: white;
}

:deep(.p-badge.p-badge-success) {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    border: none;
    color: white;
}

:deep(.p-tag) {
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
}

/* Avatar Enhancements */
:deep(.p-avatar) {
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

:deep(.p-avatar:hover) {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .category-card,
    .course-card,
    .faq-panel {
        animation-delay: 0ms !important;
    }
    
    :deep(.p-card:hover) {
        transform: translateY(-6px) scale(1.01);
    }
    
    .hero-cta-primary,
    .hero-cta-secondary {
        padding: 0.875rem 2rem;
        font-size: 0.875rem;
    }
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Focus States for Accessibility */
:deep(.p-button:focus-visible),
:deep(.p-panel-toggleable-header:focus-visible),
:deep(.p-card:focus-visible) {
    outline: 3px solid #3b82f6;
    outline-offset: 3px;
    border-radius: 0.5rem;
}

/* Gradient Text Support */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}

/* Enhanced Shadows */
.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Glass Morphism Effect */
.backdrop-blur-md {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* Smooth Transitions */
* {
    transition-property: transform, box-shadow, border-color, background-color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* SweetAlert2 Custom Styling - Small Blue Alerts */
.swal2-popup.sweet-alert-popup {
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15), 0 3px 8px rgba(0, 0, 0, 0.1);
    padding: 0.75rem 1rem;
    font-family: inherit;
    font-size: 0.8125rem;
    max-width: 16rem;
}

.swal2-title.sweet-alert-title {
    font-size: 0.8125rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.swal2-html-container.sweet-alert-content {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.95);
    line-height: 1.4;
    margin: 0;
}

/* Success Toast - Only Icon is Blue (No Green) */
.swal2-popup.swal2-toast.swal2-icon-success.sweet-alert-success,
.swal2-popup.swal2-toast.swal2-icon-success {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%) !important;
    color: white !important;
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3), 0 4px 12px rgba(59, 130, 246, 0.2) !important;
    border: none !important;
}

/* Only the success icon/checkmark is blue - everything else stays default */
.swal2-popup.swal2-toast.swal2-icon-success .swal2-icon.swal2-success {
    border-color: #3b82f6 !important;
    color: #3b82f6 !important;
}

.swal2-popup.swal2-toast.swal2-icon-success .swal2-icon.swal2-success [class^=swal2-success-line] {
    background-color: #3b82f6 !important;
}

.swal2-popup.swal2-toast.swal2-icon-success .swal2-icon.swal2-success .swal2-success-ring {
    border-color: rgba(59, 130, 246, 0.3) !important;
}

/* Override any default green success colors - Only icon is blue */
.swal2-icon.swal2-success {
    border-color: #3b82f6 !important;
    color: #3b82f6 !important;
}

.swal2-icon.swal2-success [class^=swal2-success-line] {
    background-color: #3b82f6 !important;
}

.swal2-icon.swal2-success .swal2-success-ring {
    border-color: rgba(59, 130, 246, 0.3) !important;
}

.swal2-popup.swal2-toast.swal2-icon-success .swal2-icon {
    width: 1.5rem;
    height: 1.5rem;
    margin: 0 0.5rem 0 0;
}

[dir="rtl"] .swal2-popup.swal2-toast.swal2-icon-success .swal2-icon {
    margin: 0 0 0 0.75rem;
}

/* Error Toast - Red */
.swal2-popup.swal2-toast.swal2-icon-error.sweet-alert-error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3), 0 4px 12px rgba(239, 68, 68, 0.2);
    border: none;
}

.swal2-popup.swal2-toast.swal2-icon-error .swal2-x-mark {
    color: white;
}

.swal2-popup.swal2-toast.swal2-icon-error .swal2-icon {
    width: 1.5rem;
    height: 1.5rem;
    margin: 0 0.5rem 0 0;
}

[dir="rtl"] .swal2-popup.swal2-toast.swal2-icon-error .swal2-icon {
    margin: 0 0 0 0.75rem;
}

/* Warning Toast - Orange */
.swal2-popup.swal2-toast.swal2-icon-warning.sweet-alert-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 8px 24px rgba(245, 158, 11, 0.3), 0 4px 12px rgba(245, 158, 11, 0.2);
    border: none;
}

.swal2-popup.swal2-toast.swal2-icon-warning .swal2-icon.swal2-warning {
    border-color: white;
    color: white;
}

.swal2-popup.swal2-toast.swal2-icon-warning .swal2-icon {
    width: 1.5rem;
    height: 1.5rem;
    margin: 0 0.5rem 0 0;
}

[dir="rtl"] .swal2-popup.swal2-toast.swal2-icon-warning .swal2-icon {
    margin: 0 0 0 0.75rem;
}

/* Info Toast - Blue */
.swal2-popup.swal2-toast.swal2-icon-info.sweet-alert-info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3), 0 4px 12px rgba(59, 130, 246, 0.2);
    border: none;
}

.swal2-popup.swal2-toast.swal2-icon-info .swal2-icon.swal2-info {
    border-color: white;
    color: white;
}

.swal2-popup.swal2-toast.swal2-icon-info .swal2-icon {
    width: 1.5rem;
    height: 1.5rem;
    margin: 0 0.5rem 0 0;
}

[dir="rtl"] .swal2-popup.swal2-toast.swal2-icon-info .swal2-icon {
    margin: 0 0 0 0.75rem;
}

/* Toast Container Positioning - Small Alerts */
.swal2-container.swal2-top-end {
    top: 20px;
    right: 20px;
    left: auto;
}

.swal2-container.swal2-top-start {
    top: 20px;
    left: 20px;
    right: auto;
}

[dir="rtl"] .swal2-container.swal2-top-end {
    right: auto;
    left: 20px;
}

[dir="rtl"] .swal2-container.swal2-top-start {
    left: auto;
    right: 20px;
}

@media (max-width: 960px) {
    .swal2-container.swal2-top-end,
    .swal2-container.swal2-top-start {
        right: 20px;
        left: 20px;
        width: calc(100% - 40px);
    }
    
    .swal2-popup.swal2-toast {
        width: 100% !important;
        max-width: 100% !important;
    }
}

/* Progress Bar */
.swal2-timer-progress-bar {
    background: rgba(255, 255, 255, 0.3);
    height: 2px;
    border-radius: 0 0 12px 12px;
}

/* Make alerts compact */
.swal2-popup.swal2-toast {
    min-height: auto;
    padding: 0.75rem 1rem !important;
    max-width: 16rem !important;
}

.swal2-popup.swal2-toast .swal2-header {
    margin-bottom: 0.5rem;
}

.swal2-popup.swal2-toast .swal2-title {
    margin: 0 0 0.25rem 0;
    padding: 0;
}

.swal2-popup.swal2-toast .swal2-html-container {
    margin: 0;
    padding: 0;
}
</style>
