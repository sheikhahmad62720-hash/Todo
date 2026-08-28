<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteTaskModal from '@/Components/Tasks/DeleteTaskModal.vue';
import TaskCard from '@/Components/Tasks/TaskCard.vue';
import TaskFilters from '@/Components/Tasks/TaskFilters.vue';
import TaskForm from '@/Components/Tasks/TaskForm.vue';
import TaskStats from '@/Components/Tasks/TaskStats.vue';
import type { PageFlash, Task, TaskFilter, TaskFormData, TaskStats as TaskStatsData } from '@/types';

const props = defineProps<{
    tasks: Task[];
    stats: TaskStatsData;
}>();

const page = usePage();
const flash = computed<PageFlash | undefined>(
    () => (page.props as unknown as { flash?: PageFlash }).flash,
);

const activeFilter = ref<TaskFilter>('all');
const searchQuery = ref('');
const showToast = ref(false);
let toastTimer: ReturnType<typeof setTimeout> | null = null;

const quickAddForm = useForm<TaskFormData>({
    title: '',
    description: null,
    priority: 'medium',
    due_date: null,
});

const formOpen = ref(false);
const editingTask = ref<Task | null>(null);
const taskToDelete = ref<Task | null>(null);

watch(
    () => flash.value?.success,
    (message) => {
        if (message) {
            showToast.value = true;

            if (toastTimer) {
                clearTimeout(toastTimer);
            }

            toastTimer = setTimeout(() => {
                showToast.value = false;
            }, 3500);
        }
    },
);

const filteredTasks = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();

    return props.tasks.filter((task) => {
        const matchesFilter =
            activeFilter.value === 'all'
                ? true
                : activeFilter.value === 'completed'
                  ? task.completed_at !== null
                  : task.completed_at === null;

        if (!matchesFilter) {
            return false;
        }

        if (!query) {
            return true;
        }

        return (
            task.title.toLowerCase().includes(query) ||
            (task.description ?? '').toLowerCase().includes(query)
        );
    });
});

const submitQuickAdd = () => {
    quickAddForm.post(route('tasks.store'), {
        onSuccess: () => quickAddForm.reset('title'),
    });
};

const openCreate = () => {
    editingTask.value = null;
    formOpen.value = true;
};

const openEdit = (task: Task) => {
    editingTask.value = task;
    formOpen.value = true;
};

const closeForm = () => {
    formOpen.value = false;
};

const openDelete = (task: Task) => {
    taskToDelete.value = task;
};

const closeDelete = () => {
    taskToDelete.value = null;
};

const changeFilter = (filter: TaskFilter) => {
    activeFilter.value = filter;
};
</script>

<template>
    <Head title="TaskFlow" />

    <AuthenticatedLayout>
        <template #header>
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-900">
                        TaskFlow
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage your tasks easily
                    </p>
                </div>

                <div class="mt-3 sm:mt-0">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition duration-150 ease-in-out hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-700"
                        @click="openCreate"
                    >
                        <svg
                            class="size-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"
                            />
                        </svg>
                        Add Task
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div
                        v-if="showToast"
                        class="flex items-center justify-between gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3"
                    >
                        <p class="text-sm font-medium text-emerald-800">
                            {{ flash?.success }}
                        </p>
                        <button
                            type="button"
                            class="text-emerald-600 transition hover:text-emerald-800 focus:outline-none"
                            @click="showToast = false"
                        >
                            <svg
                                class="size-4"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                                />
                            </svg>
                        </button>
                    </div>
                </Transition>

                <form
                    class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white p-2 shadow-sm"
                    @submit.prevent="submitQuickAdd"
                >
                    <svg
                        class="ms-2 size-5 shrink-0 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            d="M10 2a6 6 0 0 0-6 6v3.586l-.707.707A1 1 0 0 0 3.586 14H16.414a1 1 0 0 0 .707-1.707L16.414 11.586V8a6 6 0 0 0-6-6ZM10 18a3 3 0 0 1-3-3h6a3 3 0 0 1-3 3Z"
                        />
                    </svg>
                    <input
                        v-model="quickAddForm.title"
                        type="text"
                        class="w-full border-0 bg-transparent py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-0 focus:outline-none"
                        placeholder="What needs to be done?"
                        autocomplete="off"
                    />
                    <button
                        type="submit"
                        :disabled="quickAddForm.processing || quickAddForm.title.trim() === ''"
                        class="shrink-0 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 active:bg-indigo-700"
                    >
                        {{ quickAddForm.processing ? 'Adding...' : 'Add' }}
                    </button>
                </form>

                <div class="space-y-4">
                    <TaskFilters
                        :active-filter="activeFilter"
                        @change="changeFilter"
                    />

                    <div class="relative">
                        <svg
                            class="pointer-events-none absolute start-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <input
                            v-model="searchQuery"
                            type="search"
                            class="w-full rounded-lg border-gray-300 py-2.5 ps-10 pe-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Search tasks..."
                        />
                    </div>

                    <TaskStats :stats="stats" />
                </div>

                <div class="space-y-3">
                    <div
                        class="flex items-center justify-between text-sm font-semibold text-gray-700"
                    >
                        <span>Tasks</span>
                        <span class="tabular-nums text-gray-500">
                            {{ filteredTasks.length }}
                            {{ filteredTasks.length === 1 ? 'task' : 'tasks' }}
                        </span>
                    </div>

                    <template v-if="filteredTasks.length > 0">
                        <TaskCard
                            v-for="task in filteredTasks"
                            :key="task.id"
                            :task="task"
                            @edit="openEdit(task)"
                            @delete="openDelete(task)"
                        />
                    </template>

                    <div
                        v-else
                        class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center"
                    >
                        <template v-if="tasks.length === 0">
                            <p class="text-sm font-semibold text-gray-900">
                                No tasks yet
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Create your first task and start getting things
                                done.
                            </p>
                            <button
                                type="button"
                                class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition duration-150 ease-in-out hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                @click="openCreate"
                            >
                                Add Task
                            </button>
                        </template>

                        <template v-else-if="activeFilter === 'completed'">
                            <p class="text-sm font-semibold text-gray-900">
                                No completed tasks yet.
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Completed tasks will show up here.
                            </p>
                        </template>

                        <template v-else-if="searchQuery.trim() !== ''">
                            <p class="text-sm font-semibold text-gray-900">
                                No tasks found
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Try a different search term.
                            </p>
                        </template>

                        <template v-else>
                            <p class="text-sm font-semibold text-gray-900">
                                No {{ activeFilter }} tasks
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Try switching filters.
                            </p>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <TaskForm
        :show="formOpen"
        :task="editingTask"
        @close="closeForm"
    />

    <DeleteTaskModal
        :show="taskToDelete !== null"
        :task="taskToDelete"
        @close="closeDelete"
    />
</template>