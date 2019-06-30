@component('profiles.activities.activity')
	@slot('heading')
		<div>
			 
			<a href="{{ $activity->subject->favorited->thread->path() }}#reply-{{ $activity->subject->favorited->id}}">
				You	favorited a reply in {{ $activity->subject->favorited->thread->title }}
			</a>
			
		</div>
		
		<span>{{ $activity->subject->created_at->diffForHumans() }}.</span>
	@endslot

	@slot('body')
		{{ $activity->subject->favorited->body }}
	@endslot
@endcomponent