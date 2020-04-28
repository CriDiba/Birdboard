<div class="field mb-6">

    <div class="label text-sm mb-2 block" for="title">Title</div>

    <div class="control">
        <input value="{{ $project->title }}" type="text" class="input bg-transparent border border-muted-light rounded p-2 text-xs w-full" name="title" placeholder="My next awesome project" required>
    </div>

</div>

<div class="field mb-6">

    <div class="label text-sm mb-2 block" for="description">Description</div>

    <div class="control">
        <textarea rows="10" class="textarea bg-transparent border border-muted-light rounded p-2 text-xs w-full" name="description" placeholder="I should start learning piano." required>{{ $project->description }}</textarea>
    </div>

</div>

<div class="field">

    <div class="control">
        <button type="submit" class="button is-link mr-2">{{ $buttonText }}</button>
        <a href="{{ $project->path() }}" class="text-default">Cancel</a>
    </div>

</div>

@include ('errors')
