<?php

namespace App\Models;

use App\Traits\Favorable;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favorable, RecordsActivity;

    protected $guarded = [];
    protected $with = ['owner', 'favorites'];
    protected $appends = ['favoritesCount', 'isFavorited', 'canUpdate'];

    /**
     * Models boot method.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleting(function ($reply) {
            $reply->thread->decrement('replies_count');
        });
    }

    /**
     * Reply has an owner.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Reply belongs to a Thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Was reply published less than a minute ago?
     *
     * @return boolean
     */
    public function wasJustPublished()
    {
        return $this->created_at->gt(now()->subMinute());
    }

    /**
     * Return all of the mentioned users from the reply body.
     *
     * @return \Illuminate\Support\Collection
     */
    public function mentionedUsers()
    {
		preg_match_all('/@([\w\-]+)/', $this->body, $names);
		
		return User::whereIn('name', $names[1])->get();
    }

    /**
     * Can authenticated user update the reply?
     *
     * @return bool
     */
    public function getCanUpdateAttribute()
    {
        if (auth()->check()) {
            return auth()->user()->can('update', $this);
        }

        return false;
    }

    /**
     * Wrap mentioned user names withing an anchor tag.
     *
     * @param  string  $body
     * @return void
     */
    public function setBodyAttribute($body)
    {
		$this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }
}
