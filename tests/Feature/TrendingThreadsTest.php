<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Thread;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrendingThreadsTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();
		
		Redis::del('trending_threads');
	}

	/** @test */
	function it_records_thread_scores_when_the_thread_is_read()
	{
		// Arrange:  
		$thread = factory(Thread::class)->create();
		$this->assertEmpty(Redis::zrevrange('trending_threads', 0, -1));

		// Act: 
		$this->get($thread->path());

		// Assert: 
		$this->assertCount(1, Redis::zrevrange('trending_threads', 0, -1));
	}
}
