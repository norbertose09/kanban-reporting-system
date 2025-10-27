<?php

namespace App\Http\Controllers;
use App\Models\Project;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount([
            'tasks as total_tasks',
            'tasks as pending_tasks' => fn($query) => $query->where('status', 'pending'),
            'tasks as in_progress_tasks' => fn($query) => $query->where('status', 'in-progress'),
            'tasks as completed_tasks' => fn($query) => $query->where('status', 'done')
        ])
            ->get();

        return Inertia::render('Dashboard', [
            'projects' => $projects,
        ]);
    }

      public function create()
    {
        return Inertia::render('Projects/Create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create($validated);

        return redirect()->route('dashboard')->with('success', 'Project created successfully!');
    }


    public function show(Project $project)
    {
        $project->load([
            'tasks' => fn($query) => $query->orderBy('id', 'asc'),
            'tasks.assignee'
        ]);

        return Inertia::render('KanbanBoard', [
            'project' => $project,
            'tasksPending' => $project->tasks->where('status', 'pending')->values(),
            'tasksInProgress' => $project->tasks->where('status', 'in-progress')->values(),
            'tasksDone' => $project->tasks->where('status', 'done')->values(),
            'users' => \App\Models\User::select('id', 'name')->get(),
        ]);
    }
}
