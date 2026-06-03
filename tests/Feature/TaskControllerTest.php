<?php

use App\Models\Task;
use App\Models\User;

it('lista todas las tareas (index)', function () {
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertOk();
    expect($response->json())->toHaveCount(3);
});

it('crea una tarea (store)', function () {
    $user = User::factory()->create();

    $payload = [
        'name' => 'Tarea de prueba',
        'user_id' => $user->id,
    ];

    $response = $this->postJson('/api/tasks', $payload);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => 'Tarea de prueba',
            'user_id' => $user->id,
        ]);

    $this->assertDatabaseHas('tasks', $payload);
});

it('valida los campos requeridos al crear una tarea', function () {
    $response = $this->postJson('/api/tasks', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'user_id']);
});

it('muestra una tarea especifica (show)', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $task->id,
            'name' => $task->name,
        ]);
});

it('actualiza una tarea (update)', function () {
    $task = Task::factory()->create();

    $payload = [
        'name' => 'Nombre actualizado',
        'user_id' => $task->user_id,
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $payload);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'Nombre actualizado']);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => 'Nombre actualizado',
    ]);
});

it('elimina una tarea (destroy)', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

it('marca una tarea como completada (complete)', function () {
    $task = Task::factory()->create(['completed' => false]);

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
