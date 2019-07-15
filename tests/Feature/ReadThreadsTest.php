<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        $this->thread = factory(Thread::class)->create(); 
    }

    /** @test */
    function user_can_view_all_threads()
    {
        $response = $this->get('/threads');

        $response->assertViewIs('threads.index');
        $response->assertSee($this->thread->title);
    }

    /** @test */
    function user_can_view_a_single_thread()
    {
        $response = $this->get("/threads/{$this->thread->channel->slug}/{$this->thread->id}");

        $response->assertViewIs('threads.show');
        $response->assertSee($this->thread->title);
        $response->assertSee($this->thread->body);
    }

    /** @test */
    function user_can_filter_threads_according_to_a_channel()
    {
        $channel = factory(Channel::class)->create();
        $threadInChannel = factory(Thread::class)->create(['channel_id' => $channel->id]);
        $threadNotInChannel = factory(Thread::class)->create(['channel_id' => 123]);

        $response = $this->get("/threads/{$channel->slug}");

        $response->assertSee($threadInChannel->title);
        $response->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    function user_can_filter_threads_by_any_username()
    {
        $keanu = factory(User::class)->create(['name' => 'Keanu']);
        $threadByKeanu = factory(Thread::class)->create(['user_id' => $keanu->id]);
        $threadByJohn = factory(Thread::class)->create();

        $response = $this->get("/threads?by=Keanu");

        $response->assertSee($threadByKeanu->title);
        $response->assertDontSee($threadByJohn->title);
    }

    /** @test */
    function user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = factory(Thread::class)->create();
        factory(Reply::class, 2)->create(['thread_id' => $threadWithTwoReplies->id]);
        
        $threadWithOneReply = factory(Thread::class)->create();
        factory(Reply::class, 1)->create(['thread_id' => $threadWithOneReply->id]);
        
        $threadWithThreeReplies = factory(Thread::class)->create();
        factory(Reply::class, 3)->create(['thread_id' => $threadWithThreeReplies->id]);

        $response = $this->get("/threads?popular=1");

        $response->assertSeeInOrder([
            $threadWithThreeReplies->title,
            $threadWithTwoReplies->title,
            $threadWithOneReply->title,
        ]);
    }

    /** @test */
    function user_can_filter_unanswered_threads()
    {
        // Arrange: two threads, one with a reply, and other one without a reply
        $threadWithReply = factory(Thread::class)->create();
        factory(Reply::class)->create(['thread_id' => $threadWithReply->id]);
        $unansweredThread = factory(Thread::class)->create();
        $this->assertEquals(0, $unansweredThread->fresh()->replies_count);

        // Act: apply filter / visit endpoint with querystring
        $response = $this->get('/threads?unanswered=1');

        // Assert: only the thread without the reply is shown
        $response->assertSee($unansweredThread->title);
        $response->assertDontSee($threadWithReply->title);
    }

    /** @test */
    function user_can_request_all_replies_for_a_given_thread()
    {
        $thread = factory(Thread::class)->create();
        $replies = factory(Reply::class, 2)->create(['thread_id' => $thread->id]);

        $response = $this->json('GET', "/threads/{$thread->channel->slug}/{$thread->id}/replies");

        $response->assertStatus(200);
        $this->assertArraySubset(
            $replies->fresh()->toArray(), 
            $response->json()['data']
        );
	}

	/** @test */
	function we_record_new_visit_each_time_a_thread_is_read()
	{
		$this->withoutExceptionHandling();
		$thread = factory(Thread::class)->create(['visits' => 0]);
		$this->assertEquals(0, $thread->visits);

		// Act: visit the threads index page
		$this->get($thread->path());
		
		// Assert: see the threadone view count of 1 and thread two view count of 0
		$this->assertEquals(1, $thread->fresh()->visits);
	}

}
