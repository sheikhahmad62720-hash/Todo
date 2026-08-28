<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = [
            [
                'title' => 'Learn Laravel',
                'description' => 'Understand controllers, models, and routing',
                'priority' => 'high',
                'due_date' => now()->addDays(3),
                'completed_at' => null,
            ],
            [
                'title' => 'Build Vue Project',
                'description' => 'Create TaskFlow application with Vue 3 and Inertia',
                'priority' => 'medium',
                'due_date' => now()->addDays(7),
                'completed_at' => null,
            ],
            [
                'title' => 'Practice TypeScript',
                'description' => 'Work with interfaces and types',
                'priority' => 'low',
                'due_date' => now()->addDays(14),
                'completed_at' => null,
            ],
            [
                'title' => 'Review Git Commands',
                'description' => 'Master branching, merging, and rebasing',
                'priority' => 'medium',
                'due_date' => now()->subDays(2),
                'completed_at' => null,
            ],
            [
                'title' => 'Deploy Project',
                'description' => 'Push to production server',
                'priority' => 'high',
                'due_date' => now()->addDays(21),
                'completed_at' => now()->subDay(),
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
