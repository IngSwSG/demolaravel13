<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// INDEX
test('puede listar todas las tareas', function () {
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

// STORE
test('puede crear una tarea con datos válidos', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'name'    => 'Tarea de prueba',
        'user_id' => $user->id,
    ]);

    $response->assertStatus(201)
             ->assertJsonFragment([
                 'name'    => 'Tarea de prueba',
                 'user_id' => $user->id,
             ]);

    $this->assertDatabaseHas('tasks', ['name' => 'Tarea de prueba']);
});

test('no puede crear una tarea sin nombre', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'user_id' => $user->id,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
});

test('no puede crear una tarea con user_id inexistente', function () {
    $response = $this->postJson('/api/tasks', [
        'name'    => 'Tarea inválida',
        'user_id' => 9999,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['user_id']);
});

// SHOW
test('puede obtener una tarea por id', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => $task->name]);
});

test('retorna 404 al buscar una tarea inexistente', function () {
    $response = $this->getJson('/api/tasks/9999');

    $response->assertStatus(404);
});

// UPDATE
test('puede actualizar una tarea existente', function () {
    $task = Task::factory()->create();
    $user = User::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'name'    => 'Tarea actualizada',
        'user_id' => $user->id,
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'Tarea actualizada']);

    $this->assertDatabaseHas('tasks', ['name' => 'Tarea actualizada']);
});

test('no puede actualizar una tarea con datos inválidos', function () {
    $task = Task::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'name'    => '',
        'user_id' => $task->user_id,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
});

// DESTROY
test('puede eliminar una tarea existente', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('retorna 404 al eliminar una tarea inexistente', function () {
    $response = $this->deleteJson('/api/tasks/9999');

    $response->assertStatus(404);
});
