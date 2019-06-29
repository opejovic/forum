<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div>
            <a href="#">{{ $reply->owner->name }}</a> said {{ $reply->created_at->diffForHumans() }}
        </div>

        @auth
	        <div>
	        	<form method="POST" action="{{ route('reply.favorite', $reply) }}">
	        		@csrf
	        		<button type="submit" class="btn {{ $reply->favorited() ? 'btn-primary' : 'btn-secondary' }}" {{ $reply->favorited() ? 'disabled' : '' }}>
	        			{{ $reply->favorites->count() }} {{ str_plural('favorite', $reply->favorites->count()) }}
	        		</button>
	        	</form>
	        </div>
        @endauth
    </div>

    <div class="card-body">
        {{ $reply->body }}
    </div>
</div>