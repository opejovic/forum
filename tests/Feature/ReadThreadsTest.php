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
    function user_can_read_all_replies_associated_with_a_thread()
    {
        $reply = factory(Reply::class)->create(['thread_id' => $this->thread->id]);

        $response = $this->get("/threads/{$this->thread->channel->slug}/{$this->thread->id}");

        $response->assertViewIs('threads.show');
        $response->assertSee($reply->body);
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
}
