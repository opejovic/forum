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
     * summary
     *
     * @return void
     * @author 
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
}
