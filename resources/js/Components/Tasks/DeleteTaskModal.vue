<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import type { Task } from '@/types';

const props = defineProps<{
    show: boolean;
    task?: Task | null;
}>();

const emit = defineEmits<{
    close: [];
}>();

const deleteForm = useForm<Record<string, never>>({});

const deleteTask = () => {
    if (!props.task) {
        return;
    }

    deleteForm.delete(route('tasks.destroy', { task: props.task.id }), {
        onSuccess: () => emit('close'),
    });
};
</script>

<template>
    <Modal :show="show" :closeable="!deleteForm.processing" max-width="sm" @close="emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Delete Task
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Are you sure you want to delete this task?
            </p>

            <p class="mt-2 text-sm font-semibold text-gray-900">
                "{{ task?.title }}"
            </p>

            <div class="mt-6 flex items-center justify-end gap-3">
                <SecondaryButton type="button" :disabled="deleteForm.processing" @click="emit('close')">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ms-3"
                    :disabled="deleteForm.processing"
                    @click="deleteTask"
                >
                    {{ deleteForm.processing ? 'Deleting...' : 'Delete' }}
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>