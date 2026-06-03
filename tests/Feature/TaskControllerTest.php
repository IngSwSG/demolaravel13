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
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_store_creates_a_task(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'name'    => 'Tarea de prueba',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', ['name' => 'Tarea de prueba']);
    }

    public function test_show_returns_a_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $task->id]);
    }

    public function test_update_modifies_a_task(): void
    {
        $task    = Task::factory()->create();
        $newUser = User::factory()->create();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'name'    => 'Nombre actualizado',
            'user_id' => $newUser->id,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Nombre actualizado']);

        $this->assertDatabaseHas('tasks', ['name' => 'Nombre actualizado']);
    }

    public function test_destroy_deletes_a_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}