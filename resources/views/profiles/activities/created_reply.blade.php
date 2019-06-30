@component('profiles.activities.activity')
	@slot('heading')
		<div>
			You replied to a thread
			<a href="{{ $activity->subject->thread->path() }}#{{ $activity->subject->id}}">
				{{ $activity->subject->thread->title }}
			</a>
			
		</div>
		
		<span>{{ $activity->subject->created_at->diffForHumans() }}.</span>
	@endslot

	@slot('body')
		{{ $activity->subject->body }}
	@endslot
@endcomponent