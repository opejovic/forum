<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    /**
     * Route key name for the model.
     *
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Channel has many threads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
		return $this->hasMany(Thread::class);        
    }
}
