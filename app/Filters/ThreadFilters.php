<?php

namespace App\Filters;

use App\Models\User;

class ThreadFilters extends Filters
{
	protected $filters = ['by', 'my', 'popular'];

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function by($username)
    {
        $user = User::whereName($username)->firstOrFail();
        return $this->builder->where('user_id', $user->id);
    }

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function my()
    {
        return $this->builder->where('user_id', auth()->id());
    }

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function popular()
    {
        if ($this->request->popular == 1) {
            return $this->builder->orderBy('replies_count', 'desc');
        };
    }
}
