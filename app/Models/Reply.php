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
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread() 
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Can authenticated user update the reply?
     *
     * @return bool
     */
    public function getCanUpdateAttribute()
    {
        return auth()->user()->can('update', $this);
    }
}
