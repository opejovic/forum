<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    /**
     * Reply has an owner.
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner() 
    {
    	return $this->belongsTo(User::class, 'user_id');
    }
}
