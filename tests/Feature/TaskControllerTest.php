<?php

use App\Models\Task;
use App\Models\User;

describe('TaskController@index', function () {
    it('returns all tasks', function () {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(3);
    });

    it('returns an empty list when there are no tasks', function () {
        $response = $this->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(0);
    });
});

describe('TaskController@store', function () {
    it('creates a task with valid data', function () {
        $user = User::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'name' => 'Write tests',
            'user_id' => $user->id,
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'Write tests',
                'user_id' => $user->id,
            ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Write tests',
            'user_id' => $user->id,
        ]);
    });

    it('rejects a task without a name', function () {
        $user = User::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'user_id' => $user->id,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->assertDatabaseCount('tasks', 0);
    });

    it('rejects a task whose user does not exist', function () {
        $response = $this->postJson('/api/tasks', [
            'name' => 'Orphan task',
            'user_id' => 999,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('user_id');

        $this->assertDatabaseCount('tasks', 0);
    });
});

describe('TaskController@show', function () {
    it('returns a single task', function () {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $task->id,
                'name' => $task->name,
            ]);
    });

    it('returns 404 for a task that does not exist', function () {
        $response = $this->getJson('/api/tasks/999');

        $response->assertNotFound();
    });
});

describe('TaskController@update', function () {
    it('updates a task with valid data', function () {
        $task = Task::factory()->create(['name' => 'Old name']);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'name' => 'New name',
            'user_id' => $task->user_id,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'New name']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'New name',
        ]);
    });

    it('rejects an update with invalid data', function () {
        $task = Task::factory()->create(['name' => 'Keep me']);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'name' => '',
            'user_id' => $task->user_id,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Keep me',
        ]);
    });
});

describe('TaskController@destroy', function () {
    it('deletes a task', function () {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    });
});

describe('TaskController@complete', function () {
    it('marks a task as completed', function () {
        $task = Task::factory()->create(['completed' => false]);

        $response = $this->postJson("/api/tasks/{$task->id}/complete");

        $response->assertOk()
            ->assertJsonFragment(['completed' => true]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => true,
        ]);
    });

    it('keeps a task completed when completed again', function () {
        $task = Task::factory()->create(['completed' => true]);

        $response = $this->postJson("/api/tasks/{$task->id}/complete");

        $response->assertOk()
            ->assertJsonFragment(['completed' => true]);
    });

    it('returns 404 when completing a task that does not exist', function () {
        $response = $this->postJson('/api/tasks/999/complete');

        $response->assertNotFound();
    });
});
