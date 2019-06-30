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

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public static function feed($user, $take = 50)
    {
        return static::where('user_id', $user->id)
            ->latest()
            ->with('subject')
            ->take($take)
            ->get()
            ->groupBy(function ($activity) {
                return $activity->created_at->format('d-m-Y');
            });
    }
}
