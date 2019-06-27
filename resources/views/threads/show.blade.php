@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $thread->title }}</div>

                <div class="card-body">
                    {{ $thread->body }}
                </div>
            </div>
            <br>    
            @forelse($thread->replies as $reply)
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <a href="#">{{ $reply->owner->name }}</a> said..
                        </div>
                        
                        <div>{{ $reply->created_at->diffForHumans() }}</div>
                    </div>

                    <div class="card-body">
                        {{ $reply->body }}
                    </div>
                </div>
            <br>
            @empty
               <p>Zzzzzz. Still no replies for this thread.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection
