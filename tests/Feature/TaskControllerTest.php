<?php

use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('TaskController', function () {
    describe('index', function () {
        it('returns all tasks', function () {
            Task::factory()->count(3)->create(['user_id' => $this->user->id]);

            $response = $this->getJson('/api/tasks');

            $response->assertStatus(200)
                ->assertJsonCount(3);
        });

        it('returns empty list when no tasks exist', function () {
            $response = $this->getJson('/api/tasks');

            $response->assertStatus(200)
                ->assertJsonCount(0);
        });
    });

    describe('store', function () {
        it('creates a new task', function () {
            $data = [
                'name' => 'Test Task',
                'user_id' => $this->user->id,
            ];

            $response = $this->postJson('/api/tasks', $data);

            $response->assertStatus(201)
                ->assertJsonPath('name', 'Test Task')
                ->assertJsonPath('user_id', $this->user->id)
                ->assertJsonPath('completed', false);

            $this->assertDatabaseHas('tasks', $data);
        });

        it('validates required fields', function () {
            $response = $this->postJson('/api/tasks', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'user_id']);
        });

        it('validates name is string and max 255 characters', function () {
            $response = $this->postJson('/api/tasks', [
                'name' => str_repeat('a', 256),
                'user_id' => $this->user->id,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('name');
        });

        it('validates user_id exists', function () {
            $response = $this->postJson('/api/tasks', [
                'name' => 'Test Task',
                'user_id' => 9999,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('user_id');
        });
    });

    describe('show', function () {
        it('returns a single task', function () {
            $task = Task::factory()->create(['user_id' => $this->user->id]);

            $response = $this->getJson("/api/tasks/{$task->id}");

            $response->assertStatus(200)
                ->assertJsonPath('id', $task->id)
                ->assertJsonPath('name', $task->name);
        });

        it('returns 404 when task does not exist', function () {
            $response = $this->getJson('/api/tasks/9999');

            $response->assertStatus(404);
        });
    });

    describe('update', function () {
        it('updates a task', function () {
            $task = Task::factory()->create(['user_id' => $this->user->id]);
            $newUser = User::factory()->create();

            $data = [
                'name' => 'Updated Task',
                'user_id' => $newUser->id,
            ];

            $response = $this->putJson("/api/tasks/{$task->id}", $data);

            $response->assertStatus(200)
                ->assertJsonPath('name', 'Updated Task')
                ->assertJsonPath('user_id', $newUser->id);

            $this->assertDatabaseHas('tasks', $data);
        });

        it('validates required fields on update', function () {
            $task = Task::factory()->create(['user_id' => $this->user->id]);

            $response = $this->putJson("/api/tasks/{$task->id}", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'user_id']);
        });

        it('returns 404 when updating non-existent task', function () {
            $response = $this->putJson('/api/tasks/9999', [
                'name' => 'Updated Task',
                'user_id' => $this->user->id,
            ]);

            $response->assertStatus(404);
        });
    });

    describe('destroy', function () {
        it('deletes a task', function () {
            $task = Task::factory()->create(['user_id' => $this->user->id]);

            $response = $this->deleteJson("/api/tasks/{$task->id}");

            $response->assertStatus(204);
            $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        });

        it('returns 404 when deleting non-existent task', function () {
            $response = $this->deleteJson('/api/tasks/9999');

            $response->assertStatus(404);
        });
    });

    describe('markAsCompleted', function () {
        it('marks a task as completed', function () {
            $task = Task::factory()->create([
                'user_id' => $this->user->id,
                'completed' => false,
            ]);

            $response = $this->putJson("/api/tasks/{$task->id}/mark-as-completed");

            $response->assertStatus(200)
                ->assertJsonPath('completed', true);

            $this->assertDatabaseHas('tasks', [
                'id' => $task->id,
                'completed' => true,
            ]);
        });

        it('marks an already completed task as completed again', function () {
            $task = Task::factory()->create([
                'user_id' => $this->user->id,
                'completed' => true,
            ]);

            $response = $this->putJson("/api/tasks/{$task->id}/mark-as-completed");

            $response->assertStatus(200)
                ->assertJsonPath('completed', true);
        });

        it('returns 404 when marking non-existent task as completed', function () {
            $response = $this->putJson('/api/tasks/9999/mark-as-completed');

            $response->assertStatus(404);
        });

        it('preserves other task properties when marking as completed', function () {
            $task = Task::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Original Task Name',
                'completed' => false,
            ]);

            $this->putJson("/api/tasks/{$task->id}/mark-as-completed");

            $this->assertDatabaseHas('tasks', [
                'id' => $task->id,
                'name' => 'Original Task Name',
                'user_id' => $this->user->id,
                'completed' => true,
            ]);
        });
    });
});
