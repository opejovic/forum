<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Reply extends Model
{
	protected $guarded = [];
    protected $with = ['favorites'];
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
     * summary
     *
     * @return void
     * @author 
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    /**
     * Favorite a reply.
     *
     * @return void
     * @author 
     */
    public function favorite()
    {
        $attributes = ['user_id' => Auth::user()->id];
        if ($this->favorites()->where($attributes)->doesntExist()) {
            $this->favorites()->create($attributes);
        }
    }

    /**
     * Has reply been favorited?
     *
     * @return bool
     */
    public function isFavorited()
    {
        return !! $this->favorites->where('user_id', Auth::user()->id)->count();
    }
}
