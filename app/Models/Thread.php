<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    /**
     * Attributes that are not mass-assignable.
     */
    protected $guarded = [];
    
    /**
     * Relationships that are included in query.
     */
    protected $with = ['channel'];
    /**
     * summary
     *
     * @return void
     * @author 
     */
    protected static function boot()
    {
        parent::boot();

        // Include replies count in every thread query.
        static::addGlobalScope('replyCount', function ($builder) {
            $builder->withCount('replies');
        });

        // Include thread creator in every thread query.
        static::addGlobalScope('creator', function ($builder) {
            $builder->with('creator');
        });

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });
    }

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

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}
