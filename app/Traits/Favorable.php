<?php 

namespace App\Traits;

use App\Models\Favorite;
use Illuminate\Database\Eloquent\Model;

trait Favorable
{
    /**
     * Model morphs many Favorite.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
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
        $attributes = ['user_id' => auth()->id()];
        if ($this->favorites()->where($attributes)->doesntExist()) {
            $this->favorites()->create($attributes);
        }
    }

    /**
     * Return the favorites count for the reply.
     *
     * @return integer
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();        
    }

    /**
     * Has reply been favorited?
     *
     * @return bool
     */
    public function isFavorited()
    {
        return !! $this->favorites->where('user_id', auth()->id())->count();
    }
}
