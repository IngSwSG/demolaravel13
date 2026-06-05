<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_tasks(): void
    {
        Task::factory(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_store_creates_a_task(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'name' => 'Nueva tarea',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Nueva tarea']);
        $this->assertDatabaseHas('tasks', ['name' => 'Nueva tarea']);
    }

    public function test_store_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(422);
    }

    public function test_show_returns_a_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $task->name]);
    }

    public function test_update_modifies_a_task(): void
    {
        $task = Task::factory()->create();
        $user = User::factory()->create();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'name' => 'Tarea actualizada',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Tarea actualizada']);
        $this->assertDatabaseHas('tasks', ['name' => 'Tarea actualizada']);
    }

    public function test_destroy_deletes_a_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}