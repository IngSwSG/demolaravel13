<?php

use App\Models\Task;
use App\Models\User;

test('index returns all tasks', function () {
    Task::factory()->count(3)->create();

    $this->getJson('/api/tasks')
        ->assertStatus(200)
        ->assertJsonCount(3);
});

test('store creates a task', function () {
    $user = User::factory()->create();

    $this->postJson('/api/tasks', [
        'name' => 'Buy groceries',
        'user_id' => $user->id,
    ])
        ->assertStatus(201)
        ->assertJsonFragment(['name' => 'Buy groceries', 'completed' => false]);

    $this->assertDatabaseHas('tasks', ['name' => 'Buy groceries', 'user_id' => $user->id]);
});

test('store validates that name is required', function () {
    $user = User::factory()->create();

    $this->postJson('/api/tasks', ['user_id' => $user->id])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('store validates that user_id is required and exists', function () {
    $this->postJson('/api/tasks', ['name' => 'Task without user'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['user_id']);
});

test('show returns a single task', function () {
    $task = Task::factory()->create(['name' => 'Read a book']);

    $this->getJson("/api/tasks/{$task->id}")
        ->assertStatus(200)
        ->assertJsonFragment(['name' => 'Read a book']);
});

test('show returns 404 for non-existent task', function () {
    $this->getJson('/api/tasks/999')
        ->assertStatus(404);
});

test('update modifies an existing task', function () {
    $task = Task::factory()->create();
    $newUser = User::factory()->create();

    $this->putJson("/api/tasks/{$task->id}", [
        'name' => 'Updated task name',
        'user_id' => $newUser->id,
    ])
        ->assertStatus(200)
        ->assertJsonFragment(['name' => 'Updated task name']);

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'Updated task name']);
});

test('update validates that name is required', function () {
    $task = Task::factory()->create();
    $user = User::factory()->create();

    $this->putJson("/api/tasks/{$task->id}", ['user_id' => $user->id])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('update returns 404 for non-existent task', function () {
    $user = User::factory()->create();

    $this->putJson('/api/tasks/999', ['name' => 'Ghost task', 'user_id' => $user->id])
        ->assertStatus(404);
});

test('destroy deletes a task', function () {
    $task = Task::factory()->create();

    $this->deleteJson("/api/tasks/{$task->id}")
        ->assertStatus(204);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('destroy returns 404 for non-existent task', function () {
    $this->deleteJson('/api/tasks/999')
        ->assertStatus(404);
});

test('complete marks a task as completed', function () {
    $task = Task::factory()->create(['completed' => false]);

    $this->patchJson("/api/tasks/{$task->id}/complete")
        ->assertStatus(200)
        ->assertJsonFragment(['completed' => true]);

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'completed' => true]);
});

test('complete returns 404 for non-existent task', function () {
    $this->patchJson('/api/tasks/999/complete')
        ->assertStatus(404);
});
