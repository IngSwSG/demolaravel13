<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_can_be_marked_as_completed(): void
    {
        $task = Task::factory()->create(['completed' => false]);

        $response = $this->patchJson("/api/tasks/{$task->id}/complete");

        $response->assertStatus(200);
        $response->assertJsonFragment(['completed' => true]);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'completed' => true]);
    }
}