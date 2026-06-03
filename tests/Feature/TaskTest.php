<?php

use App\Models\Task;
use App\Models\User;

it('lista las tareas', function () {
    $user = User::factory()->create();

    $tasks = Task::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    $response = $this->getJson('/api/tasks');

    $response->assertOk()
        ->assertJsonCount(3)
        ->assertJsonFragment([
            'id' => $tasks->first()->id,
            'name' => $tasks->first()->name,
            'user_id' => $user->id,
        ]);
});

it('crea una tarea', function () {
    $user = User::factory()->create();

    $data = [
        'name' => 'Estudiar pruebas en Laravel',
        'user_id' => $user->id,
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertCreated()
        ->assertJsonFragment($data);

    $this->assertDatabaseHas('tasks', $data);
});

it('muestra una tarea', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $task->id,
            'name' => $task->name,
            'user_id' => $task->user_id,
        ]);
});

it('actualiza una tarea', function () {
    $task = Task::factory()->create();
    $newUser = User::factory()->create();

    $data = [
        'name' => 'Tarea actualizada',
        'user_id' => $newUser->id,
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $data);

    $response->assertOk()
        ->assertJsonFragment($data);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => 'Tarea actualizada',
        'user_id' => $newUser->id,
    ]);
});

it('elimina una tarea', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

it('marca una tarea como completada', function () {
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
