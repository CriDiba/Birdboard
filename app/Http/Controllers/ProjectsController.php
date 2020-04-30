<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Project;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->accessibleProjects();

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        // if (auth()->user()->isNot($project->owner)) {
        //     abort(403);
        // }
        $this->authorize('update', $project);

        return view('projects.show', compact('project'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store()
    {
        //validate
        $attributes = $this->validateRequest();

        //$attributes['owner_id'] = auth()->id();

        //persist
        $project = auth()->user()->projects()->create($attributes);


        if (request()->has('tasks')) {
            foreach (request('tasks') as $task) {
                $project->addTask($task['body']);
            }
        }

    /*
        if ($tasks = request('tasks')) {
            $project->addTasks($tasks);
        }

        */


        if (request()->wantsJson()) {
            return ['message' => $project->path()];
        }

        //redirect
        return redirect($project->path());
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Project $project)
    {
        $this->authorize('update', $project);

        $attributes = $this->validateRequest();

        $project->update($attributes);

        return redirect($project->path());
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'description' => 'sometimes|required',
            'notes' => 'nullable'
        ]);
    }

    public function destroy(Project $project)
    {
        $this->authorize('manage', $project);

        $project->delete();
        
        return redirect('/projects');
    }

}

