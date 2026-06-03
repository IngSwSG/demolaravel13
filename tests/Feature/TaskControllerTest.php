<?php

use App\Models\Task;
use App\Models\User;

// index()
it('returns all tasks', function () {
    $user = User::factory()->create();
    Task::factory()->count(3)->create(['user_id' => $user->id]);

    $this->getJson('/api/tasks')
         ->assertStatus(200)
         ->assertJsonCount(3);
});

it('returns empty array when no tasks exist', function () {
    $this->getJson('/api/tasks')
         ->assertStatus(200)
         ->assertExactJson([]);
});

// store()
it('creates a task with valid data', function () {
    $user = User::factory()->create();
    $payload = ['name' => 'Nueva tarea', 'user_id' => $user->id];

    $this->postJson('/api/tasks', $payload)
         ->assertStatus(201)
         ->assertJsonFragment($payload);

    $this->assertDatabaseHas('tasks', $payload);
});

it('fails store when name is missing', function () {
    $user = User::factory()->create();

    $this->postJson('/api/tasks', ['user_id' => $user->id])
         ->assertStatus(422)
         ->assertJsonValidationErrors(['name']);
});

it('fails store when user_id does not exist', function () {
    $this->postJson('/api/tasks', ['name' => 'Tarea', 'user_id' => 9999])
         ->assertStatus(422)
         ->assertJsonValidationErrors(['user_id']);
});

it('fails store when name exceeds max length', function () {
    $user = User::factory()->create();

    $this->postJson('/api/tasks', ['name' => str_repeat('a', 256), 'user_id' => $user->id])
         ->assertStatus(422)
         ->assertJsonValidationErrors(['name']);
});

// show()
it('returns a single task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->getJson("/api/tasks/{$task->id}")
         ->assertStatus(200)
         ->assertJsonFragment(['id' => $task->id]);
});

it('returns 404 for nonexistent task on show', function () {
    $this->getJson('/api/tasks/9999')
         ->assertStatus(404);
});

// update()
it('updates a task with valid data', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $payload = ['name' => 'Nombre actualizado', 'user_id' => $user->id];

    $this->putJson("/api/tasks/{$task->id}", $payload)
         ->assertStatus(200)
         ->assertJsonFragment(['name' => 'Nombre actualizado']);

    $this->assertDatabaseHas('tasks', array_merge(['id' => $task->id], $payload));
});

it('fails update when name is missing', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->putJson("/api/tasks/{$task->id}", ['user_id' => $user->id])
         ->assertStatus(422)
         ->assertJsonValidationErrors(['name']);
});

it('fails update when user_id does not exist', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->putJson("/api/tasks/{$task->id}", ['name' => 'Tarea', 'user_id' => 9999])
         ->assertStatus(422)
         ->assertJsonValidationErrors(['user_id']);
});

it('returns 404 for nonexistent task on update', function () {
    $user = User::factory()->create();

    $this->putJson('/api/tasks/9999', ['name' => 'Tarea', 'user_id' => $user->id])
         ->assertStatus(404);
});

// destroy()
it('deletes a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->deleteJson("/api/tasks/{$task->id}")
         ->assertStatus(204);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

it('returns 404 for nonexistent task on destroy', function () {
    $this->deleteJson('/api/tasks/9999')
         ->assertStatus(404);
});