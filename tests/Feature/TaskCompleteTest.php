<?php

use App\Models\Task;
use App\Models\User;

it('marks a task as completed', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id'   => $user->id,
        'completed' => false,
    ]);

    $this->patchJson("/api/tasks/{$task->id}/complete")
         ->assertStatus(200)
         ->assertJsonFragment(['message' => 'Tarea marcada como completada.'])
         ->assertJsonPath('task.completed', true);

    $this->assertDatabaseHas('tasks', [
        'id'        => $task->id,
        'completed' => true,
    ]);

    $this->assertNotNull($task->fresh()->completed_at);
});

it('returns 200 when task is already completed', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id'      => $user->id,
        'completed'    => true,
        'completed_at' => now(),
    ]);

    $this->patchJson("/api/tasks/{$task->id}/complete")
         ->assertStatus(200)
         ->assertJsonFragment(['message' => 'La tarea ya estaba marcada como completada.']);
});

it('returns 404 for nonexistent task on complete', function () {
    $this->patchJson('/api/tasks/9999/complete')
         ->assertStatus(404);
});

it('records completed_at timestamp when task is completed', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id'      => $user->id,
        'completed'    => false,
        'completed_at' => null,
    ]);

    expect($task->completed_at)->toBeNull();

    $this->patchJson("/api/tasks/{$task->id}/complete");

    expect($task->fresh()->completed_at)->not->toBeNull();
});