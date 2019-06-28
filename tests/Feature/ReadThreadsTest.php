<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
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
        // Arrange: existing user, two threads belonging to different channels
        $channel = factory(Channel::class)->create();
        $threadInChannel = factory(Thread::class)->create(['channel_id' => $channel->id]);
        $threadNotInChannel = factory(Thread::class)->create(['channel_id' => 123]);

        // Act: user visits the specified filter-page
        $response = $this->get("/threads/{$channel->slug}");

        // Assert: user sees only the threads associated with the filter applied
        $response->assertSee($threadInChannel->title);
        $response->assertDontSee($threadNotInChannel->title);
    }
}
