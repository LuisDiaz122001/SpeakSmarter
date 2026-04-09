<script>
export default {
    name: "LessonsIndex",
}
</script>

<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Link, router } from '@inertiajs/vue3';

    defineProps({
        lessons: {
            type: Object,
            required: true
        },
    });

    const deleteLesson = (id) => {
        if (confirm('Are you sure you want to delete this lesson?')) {
            router.delete(route('lessons.destroy', id));
        }
    };

</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="font-semibold text-xl text-gray-800 leading-tight">Lessons</h1>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between" v-if="$page.props.user.permissions.some(p => p.trim() === 'create lessons')">
                        <Link :href="route('lessons.create')" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">CREATE LESSON</Link>
                        <!--{{ lessons.total }} {{ lessons.total === 1 ? 'Lesson' : 'Lessons' }} -->
                    </div>
                <div class="mt-4">
                    <ul role="list" class="divide-y divide-gray-100">
                        <li class="flex justify-between gap-x-6 py-5" v-for="lesson in lessons.data" :key="lesson.id">
                            <div class="flex min-w-0 gap-x-4">
                            <div class="min-w-0 flex-auto">
                                <p class="text-md font-semibold leading-6 text-gray-900">{{ lesson.name }}</p>
                            </div>
                            </div>
                            <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                <p class="flex items-center gap-2">
                                    <Link :href="route('lessons.edit', lesson.id)" v-if="$page.props.user.permissions.some(p => p.trim() === 'update lessons')" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium shadow-sm hover:bg-blue-700 hover:shadow-md transition duration-200">Edit</Link>
                                    <button type="button" @click="deleteLesson(lesson.id)" v-if="$page.props.user.permissions.some(p => p.trim() === 'delete lessons')" class="inline-flex items-center px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium shadow-sm hover:bg-red-700 hover:shadow-md transition duration-200">Delete</button>
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="flex justify-between mt-2">
                    <Link v-if="lessons.current_page > 1" :href="lessons.prev_page_url" class="px-4 py-2 rounded-md">PREVIOUS</Link>
                    <div v-else></div>
                    <Link v-if="lessons.current_page < lessons.last_page" :href="lessons.next_page_url" class="px-4 py-2 rounded-md">NEXT</Link>
                    <div v-else></div>
                </div>
            </div>
            </div>
        </div>
    </AppLayout>
</template>