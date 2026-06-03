<?php

use App\Models\Task;
use App\Models\User;

it('lists all tasks', function () {
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertOk();
    expect($response->json())->toHaveCount(3);
});

it('creates a task', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/tasks', [
        'name' => 'Comprar leche',
        'user_id' => $user->id,
    ]);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'Comprar leche']);

    $this->assertDatabaseHas('tasks', [
        'name' => 'Comprar leche',
        'user_id' => $user->id,
    ]);
});

it('validates required fields when creating a task', function () {
    $response = $this->postJson('/api/tasks', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'user_id']);
});

it('shows a single task', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $task->id,
            'name' => $task->name,
        ]);
});

it('updates a task', function () {
    $task = Task::factory()->create();

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'name' => 'Nombre actualizado',
        'user_id' => $task->user_id,
    ]);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'Nombre actualizado']);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => 'Nombre actualizado',
    ]);
});

it('deletes a task', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

it('marks a task as completed', function () {
    $task = Task::factory()->create(['completed' => false]);

    $response = $this->patchJson("/api/tasks/{$task->id}/complete");

    $response->assertOk()
        ->assertJsonFragment(['completed' => true]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'completed' => true,
    ]);
});
