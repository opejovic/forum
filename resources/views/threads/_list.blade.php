@forelse($threads as $thread)
	<div class="card mb-3">
		<div class="card-header d-flex justify-content-between align-items-center">
		<div>
				<a href="{{ $thread->path() }}">
					@if(auth()->check() && $thread->hasUpdatesFor(auth()->user()))
						<h4><strong>{{ $thread->title }}</strong></h4>
					@else
						<h4>{{ $thread->title }}</h4>
					@endif
				</a>

				<p>Posted by: 
					<a href="{{ route('profiles.show', $thread->creator) }}">{{ $thread->creator->name }}</a>
				</p>
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

		<div class="card-footer">
			{{ $thread->visits() }} Visits
		</div>
	</div>

@empty
	<p>There are no relevant results yet.</p>
@endforelse