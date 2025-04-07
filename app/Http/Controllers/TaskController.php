<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class TaskController extends Controller
{
    public function index()
    {
        try {
            return response()->json([
                'tasks' => Auth::user()->tasks()->get()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'status' => 'sometimes|in:pending,in-progress,completed'
            ]);

            $task = Auth::user()->tasks()->create($validated);

            return response()->json($task, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Task creation failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, Task $task)
    {
        try {
            $this->authorize('update', $task);

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:pending,in-progress,completed'
            ]);

            $task->update($validated);

            return response()->json($task);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'Unauthorized to update this task'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Task update failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Task $task)
    {
        try {
            $this->authorize('delete', $task);
            
            $task->delete();
            
            return response()->json(null, 204);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'Unauthorized to delete this task'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Task deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}