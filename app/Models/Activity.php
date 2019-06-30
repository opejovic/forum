<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * Attributes that are mass assignable.
     */
    protected $guarded = [];

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function subject()
    {
        return $this->morphTo();
    }
}
