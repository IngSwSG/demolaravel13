<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index returns all tasks', function () {
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

test('store creates a task', function () {
    $user = User::factory()->create();

    $payload = ['name' => 'Test Task', 'user_id' => $user->id];

    $response = $this->postJson('/api/tasks', $payload);

    $response->assertStatus(201)
             ->assertJsonFragment(['name' => 'Test Task', 'user_id' => $user->id]);

    $this->assertDatabaseHas('tasks', ['name' => 'Test Task', 'user_id' => $user->id]);
});

test('show returns a task', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
             ->assertJsonFragment(['id' => $task->id]);
});

test('update modifies a task', function () {
    $task = Task::factory()->create();
    $user = User::factory()->create();

    $payload = ['name' => 'Updated Name', 'user_id' => $user->id];

    $response = $this->putJson("/api/tasks/{$task->id}", $payload);

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'Updated Name', 'user_id' => $user->id]);

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'Updated Name', 'user_id' => $user->id]);
});

test('destroy deletes a task', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
