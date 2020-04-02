@extends('layouts.app')

@section('content')

    <header class="flex items-center mb-3">
        <div class="flex justify-between items-center w-full">
            <p class="text-gray-600 text-sm font-normal">
                <a href="/projects">My Projects</a> / {{ $project->title }}
            </p>

            <a href="/projects/create" class="button">New Projects</a>
        </div>
    </header>


    <main>
        <div class="lg:flex -mx-3">
            <div class="lg:w-3/4 px-3 mb-6">

                <div class="mb-8">
                    <h2 class="text-lg text-gray-500 font-normal mb-3">Tasks</h2>
                    {{-- Tasks --}}

                    @forelse ($project->tasks as $task)
                        <div class="card mb-3">{{ $task->body }}</div>

                    @empty     

                    @endforelse

                    <div class="card mb-3">
                        <form action="{{ $project->path() . '/tasks' }}" method="POST">
                            @csrf

                            <input placeholder="Add a new task..." type="text" class="w-full" name="body">
                        </form>
                    </div>



   
                </div>

                <div>
                    <h2 class="text-lg text-gray-500 font-normal mb-3">General Notes</h2>
                    {{-- General Notes --}}
                    <textarea class="card w-full" style="min-height:200px">Lorem Ipsum</textarea>
                </div>

            </div>

            <div class="lg:w-1/4 px-3 lg:py-10">
                @include('projects.card')

            </div>
        </div>
    </main>


    
@endsection