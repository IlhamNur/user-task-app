<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Task::with('user')->select('tasks.*');

            if (Auth::user()->role !== 'admin') {
                $query->where('user_id', Auth::id());
            }

            return DataTables::of($query)
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn-edit" data-id="' . $row->id . '">Edit</button>
                        <button class="btn-delete" data-id="' . $row->id . '">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
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

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'user_id' => Auth::id(),
        ]);

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
        $this->authorizeTask($task);
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to-do,in-progress,done',
        ]);

        $task->update($request->only('title', 'description', 'status'));

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorizeTask($task);
        $task->delete();

        return response()->json(['success' => true]);
    }

    protected function authorizeTask(Task $task)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $task->user_id !== $user->id) {
            abort(403, 'Tidak diizinkan.');
        }
    }
}
