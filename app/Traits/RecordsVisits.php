<?php

namespace App\Traits;

use Illuminate\Support\Facades\Redis;

trait RecordsVisits
{
	/**
	 * Get the model visits count.
	 *
	 * @return int
	 **/
	public function visits()
	{
		return Redis::get("thread.{$this->id}.visits") ?? 0;
	}

	/**
	 * Record the visit on the model.
	 *
	 **/
	public function recordVisit()
	{
		Redis::incr($this->cacheKey());
	}

	/**
	 * Record the cached visits count on the model.
	 *
	 **/
	public function resetVisits()
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
		return "thread.{$this->id}.visits";
	}
}
