<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
	use RecordsActivity;

	/**
	 * Fields that are not mass-assignable.
	 */
    protected $guarded = [];

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function favorited()
    {
        return $this->morphTo();
    }
}
