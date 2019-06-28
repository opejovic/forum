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
                            <input type="text" name="title" class="form-control" id="title" placeholder="Thread title goes here">
                        </div>

                        <div class="form-group">
                            <textarea class="form-control" id="body" name="body" rows="9" placeholder="What is on your mind?"></textarea>
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
