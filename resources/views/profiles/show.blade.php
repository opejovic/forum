@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center">
                <div>
					<avatar-form :profileuser="{{ $profileUser }}"></avatar-form>
                </div>

                <span>
                    Member since {{ $profileUser->created_at->diffForHumans() }}.
                </span>
            </div>
            <hr>

            @forelse($activities as $date => $activity)
                <h4 class="text-center">{{ $date }}</h4>
                @foreach ($activity as $record)
                    @if (view()->exists("profiles.activities.{$record->type}"))
                        @include("profiles.activities.{$record->type}", ['activity' => $record])
                    @endif
                    <br>
                @endforeach

            @empty
                <p>There is no activity for this user yet.</p>
            @endforelse

        </div>
    </div>
</div>
@endsection
