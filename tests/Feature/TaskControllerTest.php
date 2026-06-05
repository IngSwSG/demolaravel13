<?php

use App\Models\Task;
use App\Models\User;

test('puede listar las tareas', function () {
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

test('puede crear una tarea', function () {
    $user = User::factory()->create();

    $data = [
        'name' => 'Tarea de prueba',
        'user_id' => $user->id,
    ];

    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201)
        ->assertJsonFragment([
            'name' => 'Tarea de prueba',
            'user_id' => $user->id,
        ]);

    $this->assertDatabaseHas('tasks', [
        'name' => 'Tarea de prueba',
        'user_id' => $user->id,
    ]);
});

test('puede mostrar una tarea especifica', function () {
    $task = Task::factory()->create();

    $response = $this->getJson('/api/tasks/' . $task->id);

    $response->assertStatus(200)
        ->assertJsonFragment([
            'id' => $task->id,
            'name' => $task->name,
            'user_id' => $task->user_id,
        ]);
});

test('puede actualizar una tarea', function () {
    $task = Task::factory()->create();
    $user = User::factory()->create();

    $data = [
        'name' => 'Tarea actualizada',
        'user_id' => $user->id,
    ];

    $response = $this->putJson('/api/tasks/' . $task->id, $data);

    $response->assertStatus(200)
        ->assertJsonFragment([
            'name' => 'Tarea actualizada',
            'user_id' => $user->id,
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => 'Tarea actualizada',
        'user_id' => $user->id,
    ]);
});

test('puede eliminar una tarea', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson('/api/tasks/' . $task->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('puede marcar una tarea como completada', function () {
    $task = Task::factory()->create(['completed' => false]);

    $response = $this->patchJson('/api/tasks/' . $task->id . '/complete');
                                                                                                                                                                        
    $response->assertStatus(200)
        ->assertJsonFragment(['completed' => true]);

    $this->assertDatabaseHas('tasks', [
        'id'        => $task->id,
        'completed' => true,
    ]);
});