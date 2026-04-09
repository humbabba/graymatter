<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $showMine = $user && $request->boolean('mine');

        $allowedSorts = ['name', 'created_at'];
        $sort = in_array($request->input('sort'), $allowedSorts) ? $request->input('sort') : 'name';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if ($showMine) {
            $query = Project::with('creator')->where('created_by', $user->id);
        } else {
            $query = Project::with('creator');
        }

        $query->orderBy($sort, $direction);

        if ($request->filled('search')) {
            $query->search($request->search, ['name', 'description']);
        }

        if ($request->filled('from') || $request->filled('to')) {
            $query->createdBetween($request->from, $request->to);
        }

        $projects = $query->paginate(20)->withQueryString();

        return view('projects.index', compact('projects', 'showMine', 'sort', 'direction'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'content' => 'nullable|string|max:100000',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('projects.index')->with('status', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        if (!$project->canView(auth()->user())) {
            abort(403);
        }

        $project->load('creator');

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $user = auth()->user();

        if (!$project->canManage($user)) {
            if ($project->canView($user)) {
                return redirect()->route('projects.show', $project);
            }
            abort(403);
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        if (!$project->canManage(auth()->user())) {
            abort(403, 'You do not have permission to edit this project.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'content' => 'nullable|string|max:100000',
        ]);

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'project' => $project]);
        }

        return redirect()->route('projects.index')->with('status', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        if (!$project->canManage(auth()->user())) {
            abort(403, 'You do not have permission to delete this project.');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Project deleted successfully.');
    }

    public function copy(Project $project)
    {
        $copy = $project->duplicate();
        $copy->update(['created_by' => auth()->id()]);

        return redirect()->route('projects.edit', $copy)->with('status', 'Project copied successfully.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $request->file('image')->store('project-images', 'public');

        return response()->json([
            'url' => asset('storage/' . $path),
        ]);
    }
}
