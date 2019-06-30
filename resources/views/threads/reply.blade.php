<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div>
            <a href="{{ route('profiles.show', $reply->owner) }}">
                {{ $reply->owner->name }}
            </a> 
            said {{ $reply->created_at->diffForHumans() }}
        </div>

        @auth
	        <div>
	        	<form method="POST" action="{{ route('reply.favorite', $reply) }}">
	        		@csrf
	        		<button type="submit" class="btn {{ $reply->isFavorited() ? 'btn-primary' : 'btn-secondary' }}" {{ $reply->isFavorited() ? 'disabled' : '' }}>
	        			{{ $reply->favorites_count }} {{ str_plural('favorite', $reply->favorites_count) }}
	        		</button>
	        	</form>
	        </div>
        @endauth
    </div>

    <div class="card-body">
        <a name="{{ $reply->id }}"></a>
        {{ $reply->body }}
    </div>
</div>