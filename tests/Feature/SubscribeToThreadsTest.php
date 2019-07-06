<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_subscribe_to_threads()
    {
        $thread = factory(Thread::class)->create();
        auth()->login($user = factory(User::class)->create());

        $this->json('POST', "{$thread->path()}/subscriptions");
        
        $this->assertCount(1, $thread->subscriptions);
    }

    /** @test */
    function user_can_unsubscribe_from_threads()
    {
        $this->withoutExceptionHandling();

        $thread = factory(Thread::class)->create();
        auth()->login($user = factory(User::class)->create());
        $thread->subscribe();
        $this->assertCount(1, $thread->subscriptions);

        $response = $this->json('DELETE', "{$thread->path()}/subscriptions");

        // Assert: assert that the subscription is stored, and user has new notification
        // Temp for now
        $this->assertCount(0, $thread->fresh()->subscriptions);
        // $this->assertCount(1, $user->notifications);

    }
}
