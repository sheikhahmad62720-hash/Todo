<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        $tasks = Task::query()
            ->orderByRaw("CASE priority WHEN 'high' THEN 0 WHEN 'medium' THEN 1 ELSE 2 END")
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'priority' => $task->priority,
                'due_date' => $task->due_date?->toDateString(),
                'completed_at' => $task->completed_at?->toISOString(),
                'created_at' => $task->created_at->toISOString(),
                'updated_at' => $task->updated_at->toISOString(),
            ]);

        $total = Task::count();
        $active = Task::whereNull('completed_at')->count();
        $completed = Task::whereNotNull('completed_at')->count();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'stats' => [
                'total' => $total,
                'active' => $active,
                'completed' => $completed,
            ],
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        Task::create($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully');
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully');
    }

    public function toggle(Task $task): RedirectResponse
    {
        $task->update([
            'completed_at' => $task->isCompleted() ? null : now(),
        ]);

        return redirect()->route('tasks.index')
            ->with('success', $task->isCompleted()
                ? 'Task marked as completed'
                : 'Task marked as active'
            );
    }
}
