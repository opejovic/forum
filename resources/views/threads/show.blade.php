@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h4>{{ $thread->title }}</h4>
                        <a href="#">{{ $thread->creator->name }}</a>
                    </div>

                    <div>{{ $thread->created_at->diffForHumans() }}</div>
                    
                </div>

                <div class="card-body">
                    {{ $thread->body }}
                </div>
            </div>
            <br>    
            @forelse($thread->replies as $reply)
                @include('threads.reply')
            <br>
            @empty
               <p>Zzzzzz. Still no replies for this thread.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection
