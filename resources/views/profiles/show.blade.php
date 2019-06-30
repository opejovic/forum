@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <span class="display-4">
                    Hey, {{ $profileUser->name }}.
                </span>

                <span>
                    Since {{ $profileUser->created_at->diffForHumans() }}.
                </span>
            </div>
            <hr>
        </div>
    </div>

    @foreach($threads as $thread)
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <a href="{{ $thread->path() }}">{{ $thread->title }}</a>

                <span>Published {{ $thread->created_at->diffForHumans() }}.</span>
            </div>

            <div class="card-body">
                {{ $thread->body }}
            </div>
        </div>
        <br>
    @endforeach

    {{ $threads->links() }}
    </div>
    }
</div>
@endsection
