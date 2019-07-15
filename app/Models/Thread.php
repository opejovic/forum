<?php

namespace App\Models;

use App\Utilities\Visits;
use App\Traits\RecordsVisits;
use App\Filters\ThreadFilters;
use App\Traits\RecordsActivity;
use Illuminate\Support\Facades\Redis;
use App\Events\ThreadReceivedNewReply;
use App\Notifications\ThreadWasUpdated;
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * A Thread has many replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Create a reply for the thread.
     *
     * @param $reply
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    /**
     * Notify thread subscribers about the new reply.
     *
     *
     * @param \App\Models\Reply $reply
     * @return void
     */
    public function notifySubscribers($reply)
    {
        collect($this->subscriptions)->reject(function ($subscription) use ($reply) {
            return $reply->owner->is($subscription->user);
        })->each(function ($subscription) use ($reply) {
            $subscription->user->notify(new ThreadWasUpdated($this, $reply));
        });
    }
    
    /**
    * Apply the filters for the thread.
    *
    * @param \Illuminate\Database\Eloquent\Builder $query
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }
    
    /**
    * Subscribe a user to the thread.
    *
    * @param \App\Models\User $userId
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
     * @param \App\Models\User $userId
     *
     * @return void
     * @throws \Exception
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

    /**
	 * Does the thread have updates for the user?
	 * 
     * @param $user
     *
     * @return bool
     * @throws \Exception
     */
    public function hasUpdatesFor($user = null)
    {
        // Look into cache for the proper key
        // Compare that carbon instance with $thread->updated_at
        $user = $user ?: auth()->user();

        return $this->updated_at > cache($user->visitedThreadCacheKey($this));
	}


	/**
	 * Get the thread visit details. Count the visits or reset the cache count.
	 * 
	 * @return \App\Utilities\Visits
	 */
	public function visits()
	{
		return new Visits($this);
	}
}
