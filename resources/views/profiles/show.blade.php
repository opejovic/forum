@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center">
                <span class="display-4">
                    Hey, {{ $profileUser->name }}.
                </span>

                <span>
                    Since {{ $profileUser->created_at->diffForHumans() }}.
                </span>
            </div>
            <hr>

            @foreach($activities as $date => $activity)
                <h4 class="text-center">{{ $date }}</h4>
                @foreach ($activity as $record)
                    @if (view()->exists("profiles.activities.{$record->type}"))
                        @include("profiles.activities.{$record->type}", ['activity' => $record])
                    @endif
                    <br>
                @endforeach
            @endforeach

        </div>
    </div>
</div>
@endsection
