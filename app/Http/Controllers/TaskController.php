<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // POST /api/tasks
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'title'    => $request->input('title'),
            'due_date' => $request->input('due_date'),
            'priority' => $request->input('priority'),
            'status'   => 'pending',
        ]);

        return response()->json(['message' => 'Task created successfully.', 'data' => $task], 201);
    }

    // GET /api/tasks?status=
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');

        if ($status && !in_array($status, ['pending', 'in_progress', 'done'])) {
            return response()->json(['message' => 'Invalid status filter.'], 422);
        }

        $tasks = Task::filterByStatus($status)->sortByPriorityAndDueDate()->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found.', 'data' => []], 200);
        }

        return response()->json(['message' => 'Tasks retrieved successfully.', 'data' => $tasks], 200);
    }

    // PATCH /api/tasks/{id}/status
    public function updateStatus(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => "Task with ID {$id} not found."], 404);
        }

        $newStatus = $request->input('status');

        if ($task->status === $newStatus) {
            return response()->json(['message' => "Task is already '{$newStatus}'."], 422);
        }

        if (!$task->canTransitionTo($newStatus)) {
            $allowed = Task::$statusTransitions[$task->status] ?? null;
            $hint = $allowed
                ? "From '{$task->status}', the only allowed transition is to '{$allowed}'."
                : "Task is already 'done' and cannot be updated further.";

            return response()->json(['message' => "Invalid status transition. {$hint}", 'current_status' => $task->status], 422);
        }

        $task->update(['status' => $newStatus]);

        return response()->json(['message' => 'Task status updated successfully.', 'data' => $task->fresh()], 200);
    }

    // DELETE /api/tasks/{id}
    public function destroy(int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => "Task with ID {$id} not found."], 404);
        }

        if ($task->status !== 'done') {
            return response()->json([
                'message' => "Only 'done' tasks can be deleted. This task is currently '{$task->status}'."
            ], 403);
        }

        $task->delete();

        return response()->json(['message' => "Task '{$task->title}' deleted successfully."], 200);
    }

    // GET /api/tasks/report?date=YYYY-MM-DD
    public function report(Request $request): JsonResponse
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json(['message' => "A 'date' query parameter is required (YYYY-MM-DD)."], 422);
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !strtotime($date)) {
            return response()->json(['message' => 'Invalid date format. Use YYYY-MM-DD.'], 422);
        }

        $tasks = Task::forDate($date)->get();

        $summary = [];
        foreach (['high', 'medium', 'low'] as $priority) {
            foreach (['pending', 'in_progress', 'done'] as $status) {
                $summary[$priority][$status] = 0;
            }
        }

        foreach ($tasks as $task) {
            $summary[$task->priority][$task->status]++;
        }

        return response()->json(['date' => $date, 'summary' => $summary], 200);
    }
}