<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    /**
     * Attributes that are not mass-assignable.
     */
    protected $guarded = [];

    /**
     * Returns a string representation of a threads path.
     *
     * @return string
     */
    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    /**
     * Thread has a creator.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Thread belongs to a channel.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * A Thread has many replies.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Create a reply for the thread.
     *
     * @return App\Models\Reply
     */
    public function addReply($reply) 
    {
        return $this->replies()->create($reply);
    }
}
