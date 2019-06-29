@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create a new thread</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('threads.store') }}">
                        @csrf

                        <div class="form-group">
                            <select id="channel_id" name="channel_id" class="form-control @error('channel_id') is-invalid @enderror" required="true">
                                <option selected disabled value="">Choose a channel...</option>
    
                                @foreach(App\Models\Channel::all() as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }}
                                    </option>
                                @endforeach
    
                            </select>
                            @error('channel_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror                            
                        </div>                        

                        <div class="form-group">
                            <input class="form-control @error('title') is-invalid @enderror" id="title" type="text" name="title"   placeholder="Thread title goes here" value="{{ old('title') }}" required>

                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror                               
                        </div>

                        <div class="form-group">
                            <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="9" placeholder="What is on your mind?" value="{{ old('body') }}" required></textarea>

                            @error('body')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror 
                        </div>

                        <button type="submit" class="btn btn-primary">Publish Thread</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
