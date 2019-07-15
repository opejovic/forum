<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Thread;
use App\Utilities\Trending;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrendingThreadsTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();
		$this->trending = new Trending;
		$this->trending->reset($this->trending->cacheKey());
	}

	/** @test */
	function it_records_thread_scores_when_the_thread_is_read()
	{
		// Arrange: create a thread, and assert there are no trending threads.
		$thread = factory(Thread::class)->create();
		$this->assertEmpty($this->trending->get());

		// Act: visit the threads page
		$this->get($thread->path());

		// Assert: the trending threads count is one.
		$this->assertCount(1, $this->trending->get());
	}
}
