@extends('layouts.app')

@section('content')
    
    <header class="flex items-center mb-3">
        <div class="flex justify-between items-center w-full">
            <h2 class="text-muted text-base font-light">My Projects</h2>
            <a href="/projects/create" class="button">New Projects</a>
        </div>
    </header>

    <main class="lg:flex lg:flex-wrap -mx-3">

        @forelse ($projects as $project)

            <div class="lg:w-1/3 px-3 pb-6">
                @include('projects.card')
            </div>
            
        @empty

            <div>No project yet</div>

        @endforelse

    </main>

@endsection