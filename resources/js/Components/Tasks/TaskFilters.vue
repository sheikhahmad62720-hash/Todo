<script setup lang="ts">
import type { TaskFilter } from '@/types';

defineProps<{
    activeFilter: TaskFilter;
}>();

const emit = defineEmits<{
    change: [filter: TaskFilter];
}>();

const filters: { value: TaskFilter; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'active', label: 'Active' },
    { value: 'completed', label: 'Completed' },
];
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <button
            v-for="filter in filters"
            :key="filter.value"
            type="button"
            class="rounded-full px-4 py-1.5 text-sm font-medium transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            :class="
                activeFilter === filter.value
                    ? 'bg-indigo-600 text-white shadow-sm'
                    : 'bg-white text-gray-600 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 hover:text-gray-900'
            "
            @click="emit('change', filter.value)"
        >
            {{ filter.label }}
        </button>
    </div>
</template>