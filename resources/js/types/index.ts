export type TaskPriority = 'low' | 'medium' | 'high';

export type TaskFilter = 'all' | 'active' | 'completed';

export interface Task {
    id: number;
    title: string;
    description: string | null;
    priority: TaskPriority;
    due_date: string | null;
    completed_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface TaskStats {
    total: number;
    active: number;
    completed: number;
}

export interface TaskFormData {
    title: string;
    description: string | null;
    priority: TaskPriority;
    due_date: string | null;
}

export interface PageFlash {
    success?: string;
    error?: string;
}