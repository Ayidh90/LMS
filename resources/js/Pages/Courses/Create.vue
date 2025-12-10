<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50" :dir="direction">
            <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ t('courses.create') }}</h1>

                    <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6" enctype="multipart/form-data">
                        <div class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">{{ t('courses.fields.title') }} *</label>
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
                                <label for="description" class="block text-sm font-medium text-gray-700">{{ t('courses.fields.description') }}</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                            </div>

                            <div>
                                <label for="title_ar" class="block text-sm font-medium text-gray-700">{{ t('courses.fields.title_ar') }}</label>
                                <input
                                    id="title_ar"
                                    v-model="form.title_ar"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label for="description_ar" class="block text-sm font-medium text-gray-700">{{ t('courses.fields.description_ar') }}</label>
                                <textarea
                                    id="description_ar"
                                    v-model="form.description_ar"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="level" class="block text-sm font-medium text-gray-700">{{ t('courses.fields.level') }} *</label>
                                    <select
                                        id="level"
                                        v-model="form.level"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="beginner">{{ t('courses.levels.beginner') }}</option>
                                        <option value="intermediate">{{ t('courses.levels.intermediate') }}</option>
                                        <option value="advanced">{{ t('courses.levels.advanced') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700">{{ t('courses.fields.price') }} ($) *</label>
                                    <input
                                        id="price"
                                        v-model="form.price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>

                            <div>
                                <label for="duration_hours" class="block text-sm font-medium text-gray-700">{{ t('courses.fields.duration_hours') }} *</label>
                                <input
                                    id="duration_hours"
                                    v-model="form.duration_hours"
                                    type="number"
                                    min="0"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label for="thumbnail" class="block text-sm font-medium text-gray-700">{{ t('courses.fields.thumbnail') }}</label>
                                
                                <!-- Thumbnail preview -->
                                <div v-if="thumbnailPreview" class="mb-4">
                                    <img :src="thumbnailPreview" alt="Thumbnail preview" class="w-48 h-32 object-cover rounded-lg mb-2" />
                                    <button
                                        type="button"
                                        @click="removeThumbnail"
                                        class="text-sm text-red-600 hover:text-red-800"
                                    >
                                        {{ t('common.delete') }}
                                    </button>
                                </div>

                                <!-- File upload -->
                                <input
                                    id="thumbnail"
                                    type="file"
                                    accept="image/*"
                                    @change="handleFileChange"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                
                                <!-- URL fallback -->
                                <div class="mt-2">
                                    <label for="thumbnail_url" class="block text-xs text-gray-500 mb-1">{{ t('courses.fields.thumbnail') }} (URL)</label>
                                    <input
                                        id="thumbnail_url"
                                        v-model="form.thumbnail_url"
                                        type="url"
                                        placeholder="Or enter image URL"
                                        @input="thumbnailFile = null"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    />
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Link
                                    :href="route('profile.show')"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    {{ t('common.cancel') }}
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {{ form.processing ? t('common.loading') : t('courses.actions.create') }}
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
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useDirection } from '@/composables/useDirection';
import { useTranslation } from '@/composables/useTranslation';
import { useRoute } from '@/composables/useRoute';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

defineProps({
    errors: Object,
});

const { direction } = useDirection();
const { t } = useTranslation();
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

const thumbnailFile = ref(null);
const thumbnailPreview = ref(null);

const form = useForm({
    title: '',
    title_ar: '',
    description: '',
    description_ar: '',
    level: 'beginner',
    price: 0,
    duration_hours: 0,
    thumbnail: '',
    thumbnail_url: '',
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        thumbnailFile.value = file;
        form.thumbnail = '';
        form.thumbnail_url = '';
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            thumbnailPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeThumbnail = () => {
    thumbnailFile.value = null;
    thumbnailPreview.value = null;
    form.thumbnail = '';
    form.thumbnail_url = '';
};

const submit = () => {
    form.transform((data) => {
        const formData = { ...data };
        if (thumbnailFile.value) {
            formData.thumbnail = thumbnailFile.value;
        }
        return formData;
    }).post(route('courses.store'), {
        forceFormData: true,
    });
};
</script>

