<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();

        return $tasks;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        return $task;
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $task->update($data);

        return $task;
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }

    public function complete(Task $task)
    {
        if ($task->completed_at) {
            $task->update(['completed_at' => null]);

            return response()->json([
                'message' => 'Task marked as incomplete.',
                'task'    => $task,
            ]);
        }

        $task->update(['completed_at' => now()]);

        return response()->json([
            'message' => 'Task marked as completed.',
            'task'    => $task,
        ]);
    }
}