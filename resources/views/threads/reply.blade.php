<reply inline-template :attributes="{{ $reply }}" v-cloak>
    <div class="card">
        <div id="reply-{{ $reply->id }}" class="card-header d-flex justify-content-between">
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
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" rows="2" v-model="body"></textarea>
                </div>

                <button class="btn btn-sm btn-primary" @click="update">Update</button>
                <button class="btn btn-sm btn-link" @click="editing = false">Cancel</button>
            </div>

            <div v-else v-text="body">
            </div>
        </div>

        @can('update', $reply)
        <div class="card-footer d-flex">
            <button class="btn btn-sm btn-outline-secondary mr-1" @click="editing = true">Edit</button>
            <form method="POST" action="{{ route('replies.delete', $reply) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    Delete
                </button>
            </form>
        </div>
        @endcan
    </div>
</reply>