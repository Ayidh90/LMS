<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50" :dir="direction">
            <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Lesson</h1>
                    <p class="text-gray-600 mb-6">Course: {{ course.title }}</p>

                    <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <div v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                            </div>

                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                                <textarea
                                    id="content"
                                    v-model="form.content"
                                    rows="6"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL</label>
                                    <input
                                        id="video_url"
                                        v-model="form.video_url"
                                        type="url"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label for="order" class="block text-sm font-medium text-gray-700">Order *</label>
                                    <input
                                        id="order"
                                        v-model="form.order"
                                        type="number"
                                        min="0"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (Minutes) *</label>
                                    <input
                                        id="duration_minutes"
                                        v-model="form.duration_minutes"
                                        type="number"
                                        min="0"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div class="flex items-end">
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.is_free"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Free Lesson</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Link
                                    :href="route('lessons.show', [course.slug || course.id, lesson.id])"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Updating...' : 'Update Lesson' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useRoute } from '@/composables/useRoute';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lesson: Object,
    errors: Object,
});

const { direction } = useDirection();
const { route } = useRoute();
const page = usePage();

// Check if user is admin and redirect to dashboard
const auth = computed(() => page.props.auth?.user);
const isAdmin = computed(() => {
    const user = auth.value;
    if (!user) return false;
    const isAdminFlag = user.is_admin === 1 || user.is_admin === true;
    const role = user.role;
    return (role === 'super_admin' || role === 'admin') && isAdminFlag;
});

onMounted(() => {
    if (isAdmin.value) {
        router.visit(route('admin.dashboard'));
    }
});

const form = useForm({
    title: props.lesson.title,
    description: props.lesson.description || '',
    content: props.lesson.content || '',
    video_url: props.lesson.video_url || '',
    order: props.lesson.order,
    duration_minutes: props.lesson.duration_minutes,
    is_free: props.lesson.is_free,
});

const submit = () => {
    form.put(route('lessons.update', [props.course.slug || props.course.id, props.lesson.id]));
};
</script>

