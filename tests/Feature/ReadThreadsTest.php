<?php

namespace Tests\Feature;

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
        // Arrange: create a thread, and a reply to that thread
        $reply = factory(Reply::class)->create(['thread_id' => $this->thread->id]);

        // Act: visit the threads page
        $response = $this->get("/threads/{$this->thread->channel->slug}/{$this->thread->id}");

        // Assert: see the thread, and a reply to that thread
        $response->assertViewIs('threads.show');
        $response->assertSee($reply->body);
    }
}
