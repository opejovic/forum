<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div>
            <a href="#">{{ $reply->owner->name }}</a> said..
        </div>

        <div>{{ $reply->created_at->diffForHumans() }}</div>
    </div>

    <div class="card-body">
        {{ $reply->body }}
    </div>
</div>