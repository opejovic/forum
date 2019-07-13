@component('profiles.activities.activity')
	@slot('heading')
		<div>
			 
			<a href="{{ $activity->subject->favorable->thread->path() }}#reply-{{ $activity->subject->favorable->id}}">
				You	favorable a reply in {{ $activity->subject->favorable->thread->title }}
			</a>
			
		</div>
		
		<span>{{ $activity->subject->created_at->diffForHumans() }}.</span>
	@endslot

	@slot('body')
		{!! $activity->subject->favorable->body !!}
	@endslot
@endcomponent
