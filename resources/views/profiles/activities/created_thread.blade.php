@component('profiles.activities.activity')
	@slot('heading')
		<div>
			You published a thread 
			<a href="{{ $activity->subject->path() }}">
				{{ $activity->subject->title }}
			</a>
		</div>
		
		<span>{{ $activity->subject->created_at->diffForHumans() }}.</span>
	@endslot

	@slot('body')
		{{ $activity->subject->body }}
	@endslot
@endcomponent