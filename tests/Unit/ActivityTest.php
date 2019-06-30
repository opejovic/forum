<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\Constraints\SeeInOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function it_records_activity_when_thread_is_created()
	{
		$this->assertCount(0, Activity::all());
	    auth()->login($user = factory(User::class)->create());
	    $thread = factory(Thread::class)->create(['user_id' => auth()->id()]);

	    $this->assertDatabaseHas('activities', [
	    	'user_id' => auth()->id(),
	    	'type' => 'created_thread',
	    	'subject_id' => $thread->id,
	    	'subject_type' => 'App\Models\Thread',
	    ]);

	    $this->assertCount(1, Activity::all());
	    $activity = Activity::first();
	    $this->assertEquals($activity->subject->id, $thread->id);
	}

	/** @test */
	function it_records_activity_when_reply_is_created()
	{
		$this->assertCount(0, Activity::all());
	    auth()->login($user = factory(User::class)->create());
	    $reply = factory(Reply::class)->create(['user_id' => $user->id]);

	    $this->assertDatabaseHas('activities', [
	    	'user_id' => auth()->id(),
	    	'type' => 'created_reply',
	    	'subject_id' => $reply->id,
	    	'subject_type' => 'App\Models\Reply',
	    ]);

	    $activity = Activity::first();
	    $this->assertEquals($activity->subject->id, $reply->id);
	}

	/** @test */
	function it_can_fetch_a_feed_for_any_user()
	{
		auth()->login($user = factory(User::class)->create());
		factory(Thread::class, 2)->create(['user_id' => auth()->id()]);
	    auth()->user()->activities()->first()->update(['created_at' => now()->subWeek()]);

	    $feed = Activity::feed(auth()->user());

	    $this->assertTrue($feed->keys()->contains(
	    	now()->format('d-m-Y')
	   	));

	   	$this->assertTrue($feed->keys()->contains(
	    	now()->subWeek()->format('d-m-Y')
	   	));
	}
}
