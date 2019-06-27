<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    /**
     * Returns a string representation of a threads path.
     *
     * @return string
     */
    public function path()
    {
        return "/threads/{$this->id}";
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
}
