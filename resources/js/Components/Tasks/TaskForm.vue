<script setup lang="ts">
import { nextTick, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import type { Task, TaskFormData, TaskPriority } from '@/types';

const props = defineProps<{
    show: boolean;
    task?: Task | null;
}>();

const emit = defineEmits<{
    close: [];
}>();

const titleInput = ref<InstanceType<typeof TextInput>>();

const priorityOptions: { value: TaskPriority; label: string }[] = [
    { value: 'low', label: 'Low' },
    { value: 'medium', label: 'Medium' },
    { value: 'high', label: 'High' },
];

const form = useForm<TaskFormData>({
    title: '',
    description: null,
    priority: 'medium',
    due_date: null,
});

watch(
    () => props.show,
    async (show) => {
        if (show) {
            form.clearErrors();
            form.reset();

            if (props.task) {
                Object.assign(form, {
                    title: props.task.title,
                    description: props.task.description,
                    priority: props.task.priority,
                    due_date: props.task.due_date,
                });
            }

            await nextTick();
            titleInput.value?.focus();
        }
    },
);

const submit = () => {
    if (props.task) {
        form.put(route('tasks.update', { task: props.task.id }), {
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('tasks.store'), {
            onSuccess: () => emit('close'),
        });
    }
};
</script>

<template>
    <Modal :show="show" :closeable="!form.processing" max-width="lg" @close="emit('close')">
        <form @submit.prevent="submit" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ task ? 'Edit Task' : 'Add New Task' }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ task ? 'Update the details of your task.' : 'Add a new task to your list.' }}
            </p>

            <div class="mt-6 space-y-6">
                <div>
                    <InputLabel for="title" value="Title" />

                    <TextInput
                        id="title"
                        ref="titleInput"
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="off"
                        placeholder="e.g. Learn Laravel"
                    />

                    <InputError class="mt-2" :message="form.errors.title" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" />

                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Optional details about this task"
                    />

                    <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <InputLabel for="priority" value="Priority" />

                        <select
                            id="priority"
                            v-model="form.priority"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option
                                v-for="option in priorityOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>

                        <InputError class="mt-2" :message="form.errors.priority" />
                    </div>

                    <div>
                        <InputLabel for="due_date" value="Due Date" />

                        <input
                            id="due_date"
                            v-model="form.due_date"
                            type="date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />

                        <InputError class="mt-2" :message="form.errors.due_date" />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <SecondaryButton type="button" :disabled="form.processing" @click="emit('close')">
                    Cancel
                </SecondaryButton>

                <PrimaryButton class="ms-3" :disabled="form.processing">
                    {{ task ? 'Save Changes' : 'Create Task' }}
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>