<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        return Task::all(); 
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();
        
        $user->tasks()->create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Tarea creada con éxito.');
    }

    public function show(Task $task)
    {
        return $task;
    }

    public function update(Request $request, Task $task)
    {
        // Ya no pedimos el user_id manual
        $data = $request->validate([
            'name' => 'required|string|max:255',
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
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->update(['is_completed' => true]);

        return redirect()->back()->with('success', 'Tarea completada con éxito.');
    }
}