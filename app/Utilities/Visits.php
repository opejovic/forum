<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Redis;

class Visits
{
	protected $thread;

	/**
	 * Visits class constructor.
	 * 
	 * @param \App\Models\Thread $thread
	 */
	public function __construct($thread)
	{
		$this->thread = $thread;
	}

	/**
	 * Get the model visits count.
	 *
	 * @return int
	 **/
	public function count()
	{
		return Redis::get($this->cacheKey()) ?? 0;
	}

	/**
	 * Record the visit on the model.
	 *
	 **/
	public function record()
	{
		Redis::incr($this->cacheKey());
	}

	/**
	 * Reset the cached visits count on the model.
	 *
	 **/
	public function reset()
	{
		Redis::del($this->cacheKey());
	}

	/**
	 * Set the redis cache key for the model.
	 *
	 * @return string
	 **/
	public function cacheKey()
	{
		return "thread.{$this->thread->id}.visits";
	}
}
