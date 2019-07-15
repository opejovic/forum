<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Support\Facades\Redis;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

		$this->assertTrue($thread->replies->contains($replyA));
		$this->assertTrue($thread->replies->contains($replyB));
		$this->assertFalse($thread->replies->contains($replyC));
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

	/** @test */
	function thread_can_be_subscribed_to()
	{
		$thread = factory(Thread::class)->create();

		$thread->subscribe($userId = 1);

		$this->assertCount(1, $thread->subscriptions);
	}

	/** @test */
	function thread_can_be_unsubscribed_from()
	{
		$thread = factory(Thread::class)->create();
		$thread->subscribe($userId = 1);

		$thread->unsubscribe($userId);

		$this->assertCount(0, $thread->subscriptions);
	}

	/** @test */
	function it_can_tell_if_authenticated_user_is_subscribed_to_it()
	{
		$thread = factory(Thread::class)->create();
		$user = factory(User::class)->create();
		auth()->login($user);
		$this->assertFalse($thread->isSubscribedTo);

		$thread->subscribe();

		$this->assertTrue($thread->isSubscribedTo);
	}

	/** @test */
	function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
	{
		Notification::fake();

		$thread = factory(Thread::class)->create();
		auth()->login($user = factory(User::class)->create());
		$thread->subscribe();

		$thread->addReply([
			'body' => 'Some body here',
			'user_id' => factory(User::class)->create()->id,
		]);

		Notification::assertSentTo($user, ThreadWasUpdated::class);
	}

	/** @test */
	function thread_can_check_if_the_authenticated_use_has_read_all_replies()
	{
		auth()->login($user = factory(User::class)->create());
		$thread = factory(Thread::class)->create();
		$this->assertTrue($thread->hasUpdatesFor($user));

		auth()->user()->read($thread);

		$this->assertFalse($thread->hasUpdatesFor($user));
	}

	/** @test */
	function it_records_a_score_whenever_its_visited()
	{
		$thread = factory(Thread::class)->create();
		$thread->visits()->reset();
		$this->assertEquals(0, $thread->visits()->count());

		$thread->visits()->record();
		$this->assertEquals(1, $thread->visits()->count());
	}
}
