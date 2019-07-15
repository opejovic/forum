@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
				@include('threads._list')
				
				{{ $threads->links() }}
			</div>
			
            <div class="col-md-4">
				<div class="card">
					<div class="card-header">
						Treding threads:
					</div>

					<div class="card-body">
						@foreach($trendingThreads as $trendingThread)
						<a href="{{ $trendingThread->path }}">
							<li class="list-group-item mb-1">
							{{ $trendingThread->title }}
							</li>
						</a>
						@endforeach
					</div>
				</div>
			</div>
        </div>
    </div>
@endsection
