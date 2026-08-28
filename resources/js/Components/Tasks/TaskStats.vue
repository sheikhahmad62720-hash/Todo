<script setup lang="ts">
import type { TaskStats as TaskStatsData } from '@/types';

defineProps<{
    stats: TaskStatsData;
}>();

const items: { key: 'total' | 'active' | 'completed'; label: string; classes: string }[] = [
    { key: 'total', label: 'Total', classes: 'text-gray-900' },
    { key: 'active', label: 'Active', classes: 'text-indigo-600' },
    { key: 'completed', label: 'Completed', classes: 'text-emerald-600' },
];
</script>

<template>
    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div
            v-for="item in items"
            :key="item.key"
            class="rounded-lg border border-gray-200 bg-white px-4 py-3 text-center shadow-sm"
        >
            <p class="text-2xl font-bold tabular-nums" :class="item.classes">
                {{ stats[item.key] }}
            </p>
            <p class="mt-0.5 text-xs font-medium uppercase tracking-wide text-gray-500">
                {{ item.label }}
            </p>
        </div>
    </div>
</template>