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
     * Favorite a model.
     *
     * @return void
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        if ($this->favorites()->where($attributes)->doesntExist()) {
            $this->favorites()->create($attributes);
        }
    }

    /**
     * Unfavorite a model.
     *
     * @return void
     */
    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];
        $favorite = $this->favorites()->where($attributes);
        if ($favorite->exists()) {
            $favorite->delete();
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

    /**
     * Has the model been favorited?
     *
     * @return bool
     */
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }
}
