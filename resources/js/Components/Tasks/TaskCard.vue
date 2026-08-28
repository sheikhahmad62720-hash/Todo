<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { Task, TaskPriority } from '@/types';

const props = defineProps<{
    task: Task;
}>();

const emit = defineEmits<{
    edit: [];
    delete: [];
}>();

const toggleForm = useForm<Record<string, never>>({});

const isCompleted = computed(() => props.task.completed_at !== null);

const isOverdue = computed(() => {
    if (isCompleted.value || !props.task.due_date) {
        return false;
    }

    const today = new Date().toLocaleDateString('en-CA');

    return props.task.due_date < today;
});

const formattedDueDate = computed(() => {
    if (!props.task.due_date) {
        return null;
    }

    return new Date(`${props.task.due_date}T00:00:00`).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
});

const priorityStyles: Record<TaskPriority, { label: string; classes: string }> = {
    high: {
        label: 'HIGH',
        classes: 'bg-red-50 text-red-700 ring-red-600/20',
    },
    medium: {
        label: 'MEDIUM',
        classes: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    },
    low: {
        label: 'LOW',
        classes: 'bg-sky-50 text-sky-700 ring-sky-600/20',
    },
};

const toggleCompletion = () => {
    toggleForm.patch(route('tasks.toggle', { task: props.task.id }));
};
</script>

<template>
    <div
        class="flex items-start gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition duration-150 ease-in-out hover:border-gray-300 hover:shadow"
        :class="{ 'bg-gray-50': isCompleted }"
    >
        <button
            type="button"
            role="checkbox"
            :aria-checked="isCompleted"
            :disabled="toggleForm.processing"
            class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded border transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
            :class="
                isCompleted
                    ? 'border-indigo-600 bg-indigo-600 text-white hover:bg-indigo-500'
                    : 'border-gray-300 bg-white text-transparent hover:border-indigo-400'
            "
            @click="toggleCompletion"
        >
            <svg
                class="size-3.5"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                    clip-rule="evenodd"
                />
            </svg>
        </button>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <h3
                    class="truncate text-sm font-semibold"
                    :class="isCompleted ? 'text-gray-400 line-through' : 'text-gray-900'"
                >
                    {{ task.title }}
                </h3>

                <span
                    class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider ring-1 ring-inset"
                    :class="priorityStyles[task.priority].classes"
                >
                    {{ priorityStyles[task.priority].label }}
                </span>
            </div>

            <p
                v-if="task.description"
                class="mt-1 text-sm"
                :class="isCompleted ? 'text-gray-400' : 'text-gray-600'"
            >
                {{ task.description }}
            </p>

            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
                <span
                    v-if="formattedDueDate"
                    class="inline-flex items-center gap-1 font-medium"
                    :class="isOverdue ? 'text-red-600' : isCompleted ? 'text-gray-400' : 'text-gray-500'"
                >
                    <svg
                        class="size-3.5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Due: {{ formattedDueDate }}
                    <span
                        v-if="isOverdue"
                        class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-red-700"
                    >
                        Overdue
                    </span>
                </span>

                <span
                    v-if="isCompleted && task.completed_at"
                    class="font-medium text-gray-400"
                >
                    Completed {{ new Date(task.completed_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
                </span>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-1">
            <button
                type="button"
                class="rounded-md px-2.5 py-1.5 text-xs font-semibold text-gray-600 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                @click="emit('edit')"
            >
                Edit
            </button>

            <button
                type="button"
                class="rounded-md px-2.5 py-1.5 text-xs font-semibold text-red-600 transition duration-150 ease-in-out hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                @click="emit('delete')"
            >
                Delete
            </button>
        </div>
    </div>
</template>