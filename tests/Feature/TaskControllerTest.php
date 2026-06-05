<?php

use App\Models\Task;
use App\Models\User;

// ── INDEX ──────────────────────────────────────────────────────────────────

test('index returns all tasks', function () {
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertOk()
             ->assertJsonCount(3);
});

test('index returns empty array when no tasks exist', function () {
    $response = $this->getJson('/api/tasks');

    $response->assertOk()
             ->assertJsonCount(0);
});

// ── STORE ──────────────────────────────────────────────────────────────────

test('store creates a task with valid data', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'name'    => 'Nueva tarea',
        'user_id' => $user->id,
    ]);

    $response->assertCreated()
             ->assertJsonFragment(['name' => 'Nueva tarea', 'user_id' => $user->id]);

    $this->assertDatabaseHas('tasks', ['name' => 'Nueva tarea', 'user_id' => $user->id]);
});

test('store fails when name is missing', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', ['user_id' => $user->id]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
});

test('store fails when user_id does not exist', function () {
    $response = $this->postJson('/api/tasks', [
        'name'    => 'Tarea huérfana',
        'user_id' => 9999,
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['user_id']);
});

test('store fails when user_id is missing', function () {
    $response = $this->postJson('/api/tasks', ['name' => 'Sin usuario']);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['user_id']);
});

// ── SHOW ───────────────────────────────────────────────────────────────────

test('show returns the requested task', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
             ->assertJsonFragment(['id' => $task->id, 'name' => $task->name]);
});

test('show returns 404 for a non-existent task', function () {
    $response = $this->getJson('/api/tasks/9999');

    $response->assertNotFound();
});

// ── UPDATE ─────────────────────────────────────────────────────────────────

test('update modifies a task with valid data', function () {
    $task = Task::factory()->create();
    $user = User::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'name'    => 'Tarea actualizada',
        'user_id' => $user->id,
    ]);

    $response->assertOk()
             ->assertJsonFragment(['name' => 'Tarea actualizada']);

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'Tarea actualizada']);
});

test('update fails when name is missing', function () {
    $task = Task::factory()->create();
    $user = User::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", ['user_id' => $user->id]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['name']);
});

test('update fails when user_id does not exist', function () {
    $task = Task::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'name'    => 'Actualizada',
        'user_id' => 9999,
    ]);

    $response->assertUnprocessable()
             ->assertJsonValidationErrors(['user_id']);
});

test('update returns 404 for a non-existent task', function () {
    $user = User::factory()->create();

    $response = $this->putJson('/api/tasks/9999', [
        'name'    => 'Fantasma',
        'user_id' => $user->id,
    ]);

    $response->assertNotFound();
});

// ── DESTROY ────────────────────────────────────────────────────────────────

test('destroy deletes a task and returns 204', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('destroy returns 404 for a non-existent task', function () {
    $response = $this->deleteJson('/api/tasks/9999');

    $response->assertNotFound();
});
