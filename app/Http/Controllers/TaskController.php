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
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $task = Task::create($data);

        return $task;
    }

    public function show(Task $task)
    {
        return $task;
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
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
        if ($task->completed) {
            return response()->json([
                'message' => 'La tarea ya estaba marcada como completada.',
                'task'    => $task,
            ], 200);
        }
 
        $task->update([
            'completed'    => true,
            'completed_at' => now(),
        ]);
 
        return response()->json([
            'message' => 'Tarea marcada como completada.',
            'task'    => $task,
        ], 200);
    }
}
