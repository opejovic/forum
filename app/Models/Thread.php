<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function path()
    {
        return "/threads/{$this->id}";
    }
}
