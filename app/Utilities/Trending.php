<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Redis;

class Trending
{
	/**
	 * Get the top 5 trending threads.
	 *
	 * @return array
	 **/
	public function get()
	{
		return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 4));
	}

	/**
	 * Push the thread to trending list.
	 *
	 * @param \App\Models\Thread $thread
	 **/
	public function push($thread)
	{
		Redis::zincrby($this->cacheKey(), 1, json_encode([
			'title' => $thread->title,
			'path' => $thread->path(),
		]));
	}

	/**
	 * Get the cache key.
	 *
	 * @return string
	 **/	
	public function cacheKey()
	{
		return app()->environment('testing') ? 'testing_trending_threads' : 'trending_threads';
	}

	/**
	 * Clear the redis cache key.
	 *
	 * @param string $key
	 **/
	public function reset($key)
	{
		Redis::del($key);
	}


}
