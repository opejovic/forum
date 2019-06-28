<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThreadTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function it_has_a_creator()
	{
		$creator = factory(User::class)->create();    
	    $thread = factory(Thread::class)->create(['user_id' => $creator->id]);

	    $this->assertTrue($thread->creator->is($creator));
	}

	/** @test */
	function it_belongs_to_a_channel()
	{
	    $thread = factory(Thread::class)->create();

	    $this->assertInstanceOf('App\Models\Channel', $thread->channel);
	}

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

	/** @test */
	function it_can_add_a_reply()
	{
	    $thread = factory(Thread::class)->create();

	    $thread->addReply([
	    	'body' => 'Code thoughts',
	    	'user_id' => '1'
	    ]);

	    $this->assertCount(1, $thread->replies);
	}

	/** @test */
	function it_can_get_paths_string_representation()
	{
	    $thread = factory(Thread::class)->create();

	    $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->id}", $thread->path());
	}
}
