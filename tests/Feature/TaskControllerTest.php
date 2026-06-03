<?php

use App\Models\Task;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('un usuario autenticado puede crear una tarea correctamente', function () {
    $user = User::factory()->create();
    
    $taskData = [
        'name' => 'Aprender Git Workflow en la UCR',
        'user_id' => $user->id
    ];

    $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', [
        'name' => 'Aprender Git Workflow en la UCR',
        'user_id' => $user->id
    ]);
});

test('un usuario puede marcar su propia tarea como completada', function () {
    $user = User::factory()->create();
    $task = Task::create([
        'name' => 'Laboratorio de Ingeniería de Software',
        'user_id' => $user->id,
        'is_completed' => false
    ]);

    $response = $this->actingAs($user)->patch(route('tasks.complete', $task));

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'is_completed' => true
    ]);
});

test('un usuario no puede marcar como completada la tarea de otra persona', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $task = Task::create([
        'name' => 'Examen de Redes de Juan',
        'user_id' => $user1->id,
        'is_completed' => false
    ]);

    $response = $this->actingAs($user2)->patch(route('tasks.complete', $task));

    $response->assertStatus(403);
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'is_completed' => false
    ]);
});