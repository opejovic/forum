<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThreadSubscription extends Model
{
    protected $guarded = [];

    /**
     * ThreadSubscription belongs to a User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
