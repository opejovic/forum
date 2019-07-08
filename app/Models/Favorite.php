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
     * Favorable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function favorable()
    {
        return $this->morphTo();
    }
}
