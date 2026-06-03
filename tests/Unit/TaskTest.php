<?php

namespace Tests\Unit;

use App\Http\Controllers\TaskController;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_tasks()
    {
        $user = User::factory()->create();

        Task::factory()->count(2)->create([
            'user_id' => $user->id
        ]);

        $controller = new TaskController();

        $result = $controller->index();

        $this->assertCount(2, $result);
    }

    public function test_show_returns_task()
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);

        $controller = new TaskController();

        $result = $controller->show($task);

        $this->assertEquals($task->id, $result->id);
    }

    public function test_destroy_deletes_task()
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);

        $controller = new TaskController();

        $response = $controller->destroy($task);

        $this->assertEquals(204, $response->getStatusCode());

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_complete_marks_task_as_completed()
{
    $user = User::factory()->create();

    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    $controller = new TaskController();

    $result = $controller->complete($task);

    $this->assertTrue($result->completed);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'completed' => true,
    ]);
}
}