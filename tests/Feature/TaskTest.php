<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_page_renders_tasks_and_stats(): void
    {
        Task::create(['title' => 'Active task', 'priority' => 'medium']);
        Task::create(['title' => 'Completed task', 'priority' => 'high', 'completed_at' => now()]);

        $this->get('/tasks')
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tasks/Index')
                ->has('tasks', 2)
                ->where('stats.total', 2)
                ->where('stats.active', 1)
                ->where('stats.completed', 1));
    }

    public function test_user_can_create_a_task(): void
    {
        $response = $this->post('/tasks', [
            'title' => 'Learn Laravel',
            'description' => 'Understand controllers and models',
            'priority' => 'high',
            'due_date' => '2026-08-30',
        ]);

        $response->assertRedirect(route('tasks.index', absolute: false))
            ->assertSessionHas('success', 'Task created successfully');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Learn Laravel',
            'description' => 'Understand controllers and models',
            'priority' => 'high',
            'due_date' => '2026-08-30 00:00:00',
            'completed_at' => null,
        ]);
    }

    public function test_task_title_is_required(): void
    {
        $this->post('/tasks', [
            'title' => '',
            'priority' => 'medium',
        ])->assertSessionHasErrors('title');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_task_title_must_not_exceed_maximum_length(): void
    {
        $this->post('/tasks', [
            'title' => str_repeat('a', 256),
            'priority' => 'medium',
        ])->assertSessionHasErrors('title');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_task_priority_must_be_valid(): void
    {
        $this->post('/tasks', [
            'title' => 'Invalid priority task',
            'priority' => 'urgent',
        ])->assertSessionHasErrors('priority');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_task_due_date_must_be_a_valid_date(): void
    {
        $this->post('/tasks', [
            'title' => 'Invalid date task',
            'priority' => 'low',
            'due_date' => 'not-a-date',
        ])->assertSessionHasErrors('due_date');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_task_description_and_due_date_are_optional(): void
    {
        $this->post('/tasks', [
            'title' => 'Minimal task',
            'priority' => 'low',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Minimal task',
            'priority' => 'low',
            'description' => null,
            'due_date' => null,
        ]);
    }

    public function test_user_can_edit_a_task(): void
    {
        $task = Task::create([
            'title' => 'Original title',
            'description' => 'Original description',
            'priority' => 'low',
            'due_date' => '2026-08-30',
        ]);

        $response = $this->put("/tasks/{$task->id}", [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'priority' => 'high',
            'due_date' => '2026-09-15',
        ]);

        $response->assertRedirect(route('tasks.index', absolute: false))
            ->assertSessionHas('success', 'Task updated successfully');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated title',
            'description' => 'Updated description',
            'priority' => 'high',
            'due_date' => '2026-09-15 00:00:00',
        ]);
    }

    public function test_user_can_mark_an_active_task_as_completed(): void
    {
        $task = Task::create(['title' => 'Active task', 'priority' => 'medium']);

        $this->patch("/tasks/{$task->id}/toggle")
            ->assertRedirect(route('tasks.index', absolute: false))
            ->assertSessionHas('success', 'Task marked as completed');

        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_user_can_mark_a_completed_task_as_active(): void
    {
        $task = Task::create([
            'title' => 'Completed task',
            'priority' => 'medium',
            'completed_at' => now(),
        ]);

        $this->patch("/tasks/{$task->id}/toggle")
            ->assertRedirect(route('tasks.index', absolute: false))
            ->assertSessionHas('success', 'Task marked as active');

        $this->assertNull($task->fresh()->completed_at);
    }

    public function test_user_can_delete_a_task(): void
    {
        $task = Task::create(['title' => 'Task to delete', 'priority' => 'medium']);

        $response = $this->delete("/tasks/{$task->id}");

        $response->assertRedirect(route('tasks.index', absolute: false))
            ->assertSessionHas('success', 'Task deleted successfully');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_task_model_helpers(): void
    {
        $active = Task::create(['title' => 'Active', 'priority' => 'low']);
        $completed = Task::create(['title' => 'Done', 'priority' => 'low', 'completed_at' => now()]);
        $overdue = Task::create(['title' => 'Late', 'priority' => 'high', 'due_date' => now()->subDay()]);

        $this->assertFalse($active->isCompleted());
        $this->assertTrue($completed->isCompleted());
        $this->assertFalse($completed->isOverdue());
        $this->assertTrue($overdue->isOverdue());
        $this->assertFalse($active->isOverdue());
    }
}