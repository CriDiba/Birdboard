<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProjectInvitationsRequest;
use App\User;
use App\Project;

class ProjectInvitationsController extends Controller
{
    public function store(Project $project, ProjectInvitationsRequest $request)
    {
        $user = User::whereEmail(request('email'))->first();

        $project->invite($user);

        return redirect($project->path());
    }
}
