@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @forelse($threads as $thread)  
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ $thread->path() }}">
                                <strong class="">{{ $thread->title }}</strong>
                            </a>
                        </div>

                        <div>
                            <strong>
                                <a href="{{ $thread->path() }}">
                                    {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}
                                </a>
                            </strong>
                        </div>
                    </div>

                    <div class="card-body">
                        {{ $thread->body }}
                    </div>
                </div>
                <br>
            @empty
                <p>There are no relevant results yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
