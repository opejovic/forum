@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @forelse($threads as $thread)  
                <div class="card">
                    <div class="card-header">
                        <a href="{{ $thread->path() }}">
                            {{ $thread->title }}
                        </a>
                    </div>

                    <div class="card-body">
                        {{ $thread->body }}
                    </div>
                </div>
                <br>
            @empty
                <p>No threads created yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
