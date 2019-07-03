@extends('layouts.app')

@section('content')
<thread-view inline-template :initial-replies-count="{{ $thread->replies_count }}">
    <div class="container">
        <div class="row justify-content-left">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h4>{{ $thread->title }}</h4>
                            <a href="{{ route('profiles.show', $thread->creator) }}">{{ $thread->creator->name }}</a>
                        </div>

                        <div>
                            <div class="mb-1">{{ $thread->created_at->diffForHumans() }}</div> 
                        </div>
                        
                    </div>

                    <div class="card-body">
                        {{ $thread->body }}
                    </div>

                    @can('update', $thread)
                        <div class="card-footer text-right">
                            <form method="POST" action="{{ route('threads.delete', [$thread->channel, $thread]) }}">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    @endcan
                </div>

                <replies :data="{{ $replies }}" @removed="repliesCount--"></replies>


                @if(auth()->check())
                    <form method="POST" action="{{ route('replies.store', [$thread->channel->slug, $thread->id]) }}">
                        @csrf
                        <div class="form-group">
                            <textarea class="form-control" id="body" name="body" rows="5" placeholder="Have something to say?"></textarea>
                        </div>

                        <button type="submit" class="btn btn-secondary">Post</button>
                    </form>
                @else
                    <p class="text-center">Please <a href="/login">sign in</a> or <a href="/register">register</a> in order to participate in this discussion.</p>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            Thread information
                        </div>
                    </div>

                    <div class="card-body">
                        This thread was published {{ $thread->created_at->diffForHumans() }} by <a href="#">{{ $thread->creator->name }}</a>.
                        <br>It currently has <span v-text="repliesCount"></span> {{ str_plural('comment', $thread->replies_count) }}.
                    </div>
                </div>            
            </div>
        </div>
    </div>
</thread-view>
@endsection
