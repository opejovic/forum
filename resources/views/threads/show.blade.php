@extends('layouts.app')

@section('header')
	<link href="{{ asset('css/jquery.atwho.css') }}" rel="stylesheet">
@endsection

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

                <replies @removed="repliesCount--" @added="repliesCount++"></replies>

            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            Thread information
                        </div>
                    </div>

                    <div class="card-body">
                        This thread was published {{ $thread->created_at->diffForHumans() }} by 
                        <a href="{{ route('profiles.show', $thread->creator)}}">{{ $thread->creator->name }}</a>.
                        <br>It currently has <span v-text="repliesCount"></span> {{ str_plural('comment', $thread->replies_count) }}.
                    </div>

                    <div class="card-footer" v-if="signedIn">
                        <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}"></subscribe-button>
                    </div>
                </div>            
            </div>
        </div>
    </div>
</thread-view>
@endsection
