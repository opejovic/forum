<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function it_can_publish_a_thread()
	{
	    $user = factory(User::class)->create();
	    $this->assertCount(0, $user->fresh()->threads);

	    $user->publishThread([
	    	'channel_id' => 1,
	    	'title' => 'Samurai',
	    	'body' => 'Samurais are japanse warriors. They follow bushido.',
	    ]);

	    $this->assertCount(1, $user->fresh()->threads);
	}

	/** @test */
	function it_has_many_threads()
	{
	    $user = factory(User::class)->create();
	    $thread = factory(Thread::class)->create(['user_id' => $user->id]);

	    $this->assertTrue($user->threads->contains($thread));
	}

	/** @test */
	function it_can_have_many_replies()
	{
	    $user = factory(User::class)->create();
	    $reply = factory(Reply::class)->create(['user_id' => $user->id]);

	    $this->assertTrue($user->replies->contains($reply));
	}
}
