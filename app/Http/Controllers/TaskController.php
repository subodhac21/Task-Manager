<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function home(Request $request)
    {
        $statuses = Status::all()->pluck('name', 'id')->toArray();
        return view('dashboard.index', [
            'statuses' => $statuses,
            'users' => User::all()->pluck('name', 'id')->toArray(),
        ]);
    }
    public function index(Request $request)
    {
        $query = Task::with('status', 'assignee');

        $query->when($request->assigned_to, function ($q) use ($request) {
            $q->whereHas('assignee', function ($q) use ($request) {
                $q->where("assigned_to", $request->assigned_to);
            });
        });

        $query->when($request->status, function ($q) use ($request) {
            $q->whereHas('status', function ($q) use ($request) {
                $q->where('id', $request->status);
            });
        });

        $query->when($request->date, function ($q) use ($request) {
            $q->where('assigned_at', $request->date);
        });

        $query->when($request->priority, function ($q) use ($request) {
            $q->where('priority', $request->priority);
        });



        $task = $query->latest()->get();

        return TaskResource::collection($task);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
            'priority' => 'required|in:low,medium,high',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validation->fails()) {
            return response()->json(
                [
                    'errors' => $validation->errors(),
                ],
                422,
            );
        }

        $validated = $validation->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('tasks', 'public');
        }

        $validated['created_by'] = Auth::id();
        $validated['created_at'] = now();

        $task = Task::create($validated);

        return response()->json(
            [
                'message' => 'Task created successfully',
                'task' => new TaskResource($task),
            ],
            201,
        );
    }

    public function update(Request $request, Task $task)
    {
        $validation = Validator::make($request->all(), [
            'status_id' => 'required|exists:statuses,id',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
            ]);
        }

        $task->update($request->all());

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => new TaskResource($task),
        ]);
    }

    public function updateTaskDetails(Request $request, Task $task)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'priority' => 'required|in:low,medium,high',
        ]);

        if ($validation->fails()) {
            return response()->json(
                [
                    'errors' => $validation->errors(),
                ],
                422,
            );
        }

        $validated = $validation->validated();

        if ($request->hasFile('image')) {
            if ($task->image) {
                Storage::disk('public')->delete($task->image);
            }
           $validated['image'] = $request->file('image')->store('tasks', 'public');
        }

        $validated['updated_at'] = now();

        $task->update($validated);

        return response()->json([
            'message' => 'Task details updated successfully',
            'task' => new TaskResource($task),
        ]);
    }

    public function assignUser(Request $request){
        $validation = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:users,id',
        ]);
        if($validation->fails()){
            return response()->json([
                'error' => true,
                'errors' => $validation->errors(),
            ], 422);
        }

        $task = Task::where('id', $request->task_id)->first();
        $task->assigned_to  = $request->user_id;
        $task->save();
        return response()->json([
            'success'=> true,
            'message' => 'Task assigned successfully',
            'task' => new TaskResource($task),
        ], 200);
    }
}
