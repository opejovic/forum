<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_view_all_threads()
    {
        $threadA = factory(Thread::class)->create();
        $threadB = factory(Thread::class)->create();

        $response = $this->get('/threads');

        $response->assertViewIs('threads.index');
        $response->assertSee($threadA->title);
        $response->assertSee($threadB->title);
    }

    /** @test */
    function user_can_view_a_single_thread()
    {
        $thread = factory(Thread::class)->create();

        $response = $this->get("/threads/{$thread->id}");

        $response->assertViewIs('threads.show');
        $response->assertSee($thread->title);
        $response->assertSee($thread->body);
    }
}
