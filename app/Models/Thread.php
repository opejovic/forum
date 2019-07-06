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
     * Apply the filters for the thread.
     *
     * @param Illuminate\Database\Eloquent\QueryBuilder $query
     * @param ThreadFilters $filters
     * @return void
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
    /**
     * Thread has many subscriptions.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Subscribe a user to the thread.
     *
     * @param App\Models\User $userId
     * @return void
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);        
    }

    /**
     * Unsubscribe the user from the thread.
     *
     * @param App\Models\User $userId
     * @return void
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->first()
            ->delete();
    }

    /**
     * Is the user subscribed to the thread?
     *
     * @return bool
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()->where('user_id', auth()->id())->exists();        
    }

}
