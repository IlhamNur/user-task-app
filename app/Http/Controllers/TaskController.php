<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Task::with('user');

            if ($request->user()->role !== 'admin') {
                $query->where('user_id', $request->user()->id);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($task) {
                    return view('tasks.partials.actions', compact('task'))->render();
                })
                ->editColumn('status', function ($task) {
                    return ucfirst($task->status);
                })
                ->make(true);
        }

        return view('tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to-do,in-progress,done',
        ]);

        $data = $request->only('title', 'description', 'status');
        $data['user_id'] = $request->user()->id;

        $task = Task::create($data);

        return response()->json(['success' => true, 'task' => $task]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to-do,in-progress,done',
        ]);

        $data = $request->only('title', 'description', 'status');
        $data['user_id'] = $request->user()->id;

        $task = Task::create($data);

        return response()->json(['success' => true, 'task' => $task]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorizeTaskOwner($task);
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorizeTaskOwner($task);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to-do,in-progress,done',
        ]);

        $task->update($request->only('title', 'description', 'status'));
        return response()->json(['success' => true, 'task' => $task]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorizeTaskOwner($task);
        $task->delete();
        return response()->json(['success' => true]);
    }

    protected function authorizeTaskOwner(Task $task)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $task->user_id !== $user->id) {
            abort(403);
        }
    }
}
