<?php

use App\Models\Task;
use App\Models\User;

test('it lists tasks', function () {
    $tasks = Task::factory()->count(2)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertOk()
        ->assertJsonCount(2)
        ->assertJsonFragment([
            'id' => $tasks->first()->id,
            'name' => $tasks->first()->name,
            'completed' => false,
        ]);
});

test('it stores a task', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'name' => 'Estudiar pruebas',
        'user_id' => $user->id,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'Estudiar pruebas',
            'user_id' => $user->id,
            'completed' => false,
        ]);

    $this->assertDatabaseHas('tasks', [
        'name' => 'Estudiar pruebas',
        'user_id' => $user->id,
        'completed' => false,
    ]);
});

test('it shows a task', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $task->id,
            'name' => $task->name,
            'completed' => false,
        ]);
});

test('it updates a task', function () {
    $task = Task::factory()->create();
    $user = User::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'name' => 'Tarea actualizada',
        'user_id' => $user->id,
    ]);

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $task->id,
            'name' => 'Tarea actualizada',
            'user_id' => $user->id,
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => 'Tarea actualizada',
        'user_id' => $user->id,
    ]);
});

test('it deletes a task', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('it marks a task as completed', function () {
    $task = Task::factory()->create([
        'completed' => false,
    ]);

    $response = $this->patchJson("/api/tasks/{$task->id}/complete");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $task->id,
            'completed' => true,
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'completed' => true,
    ]);
});
