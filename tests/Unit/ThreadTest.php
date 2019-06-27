<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThreadTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function it_can_have_replies()
	{
	    $thread = factory(Thread::class)->create();
	    $replyA = factory(Reply::class)->create(['thread_id' => $thread->id]);
	    $replyB = factory(Reply::class)->create(['thread_id' => $thread->id]);
	    $replyC = factory(Reply::class)->create();

	    $replies = $thread->replies;

	    $this->assertTrue($replies->contains($replyA));
	    $this->assertTrue($replies->contains($replyB));
	    $this->assertFalse($replies->contains($replyC));
	}
}
