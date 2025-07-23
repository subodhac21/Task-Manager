<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Status;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    public function home(Request $request){
        $statuses = Status::all()->pluck('name', 'id')->toArray();
        return view('dashboard.index', [
            'statuses' => $statuses
        ]);
    }
    public function index(Request $request){
        $query = Task::with("status", 'assignee');

        when($request->user_id, function($q) use ($request) {
            $q->whereHas("assignee", function($q) use ($request) {
                $q->where('assigned_to', $request->user_id);
            });
        });

        when($request->status, function($q) use ($request) {
            $q->whereHas("status", function($q) use ($request) {
                $q->where('name', $request->status);
            });
        });

        when($request->date, function($q) use ($request) {
            $q->where('assigned_at', $request->date);
        });

        when($request->priority, function($q) use ($request) {
            $q->where('priority', $request->priority);
        });


        $task = $query->latest()->get();
        

        return TaskResource::collection($task);
    }


    public function store(Request $request){
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
            'priority' => 'required|in:low,medium,high',
            'image' => 'nullable|image|max:2048',
        ]);

        if($validation->fails()){
            return response()->json([
                'errors' => $validation->errors()
            ], 422);
        }

        $request->merge([
            'created_by' => Auth::id(),
            'assigned_at' => now(),
        ]);

        $task = Task::create($request->all());

        return response()->json([
            'message' => 'Task created successfully',
            'task' => new TaskResource($task)
        ], 201);

    }

    public function update(Request $request, Task $task){

        $validation = Validator::make($request->all(), [
            'status_id' => 'required|exists:statuses,id',
        ]);

        if($validation->fails()){
            return response()->json([
                'errors'=> $validation->errors()
            ]);
        }

        $task->update($request->all());

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => new TaskResource($task)
        ]);


    }

    public function updateTaskDetails(Request $request, Task $task){
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'priority' => 'required|in:low,medium,high',
        ]);

        if($validation->fails()){
            return response()->json([
                'errors' => $validation->errors()
            ], 422);
        }

        $task->update($request->all());

        return response()->json([
            'message' => 'Task details updated successfully',
            'task' => new TaskResource($task)
        ]);
    }

}
