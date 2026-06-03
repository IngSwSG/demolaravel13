<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

   
    private function createUser(): User
    {
        return User::factory()->create();
    }

    private function createTask(array $overrides = []): Task
    {
        $user = $this->createUser();

        return Task::factory()->create(array_merge([
            'user_id' => $user->id,
        ], $overrides));
    }

    public function test_index_returns_all_tasks(): void
    {
        Task::factory(3)->create(['user_id' => $this->createUser()->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
                 ->assertJsonCount(3);
    }


    public function test_store_creates_a_task(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/api/tasks', [
            'name'    => 'Nueva tarea',
            'user_id' => $user->id,
        ]);

        $response->assertCreated()
                 ->assertJsonFragment(['name' => 'Nueva tarea']);

        $this->assertDatabaseHas('tasks', ['name' => 'Nueva tarea']);
    }

    public function test_store_requires_name_and_user_id(): void
    {
        $response = $this->postJson('/api/tasks', []);

        $response->assertUnprocessable()
                 ->assertJsonValidationErrors(['name', 'user_id']);
    }

    public function test_show_returns_a_task(): void
    {
        $task = $this->createTask();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertOk()
                 ->assertJsonFragment(['id' => $task->id]);
    }


    public function test_update_modifies_a_task(): void
    {
        $task = $this->createTask();
        $user = $this->createUser();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'name'    => 'Tarea actualizada',
            'user_id' => $user->id,
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['name' => 'Tarea actualizada']);

        $this->assertDatabaseHas('tasks', ['name' => 'Tarea actualizada']);
    }

    public function test_destroy_deletes_a_task(): void
    {
        $task = $this->createTask();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_complete_marks_task_as_completed(): void
    {
        $task = $this->createTask(['completed_at' => null]);

        $response = $this->patchJson("/api/tasks/{$task->id}/complete");

        $response->assertOk()
                 ->assertJsonFragment(['message' => 'Task marked as completed.']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);

        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_complete_unmarks_an_already_completed_task(): void
    {
        $task = $this->createTask(['completed_at' => now()]);

        $response = $this->patchJson("/api/tasks/{$task->id}/complete");

        $response->assertOk()
                 ->assertJsonFragment(['message' => 'Task marked as incomplete.']);

        $this->assertNull($task->fresh()->completed_at);
    }

    public function test_complete_returns_404_for_nonexistent_task(): void
    {
        $response = $this->patchJson('/api/tasks/999/complete');

        $response->assertNotFound();
    }
}