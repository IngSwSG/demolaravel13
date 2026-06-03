<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_tasks()
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_task()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'name' => 'Nueva tarea',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Nueva tarea',
            'user_id' => $user->id,
        ]);
    }

    public function test_can_show_task()
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $task->id,
                 ]);
    }

    public function test_can_update_task()
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'name' => 'Tarea actualizada',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Tarea actualizada',
        ]);
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_can_complete_task()
{
    $user = User::factory()->create();

    $task = Task::factory()->create([
        'user_id' => $user->id,
        'completed' => false,
    ]);

    $response = $this->patchJson("/api/tasks/{$task->id}/complete");

    $response->assertStatus(200);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'completed' => true,
    ]);
}
}