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
        $this->withoutExceptionHandling();
        // Arrange: existing thread, and authenticated user.
        $thread = factory(Thread::class)->create();
        auth()->login($user = factory(User::class)->create());

        // Act: user posts to thread subscription endpoint and new reply is left for that thread
        $this->json('POST', "{$thread->path()}/subscriptions");
        $thread->addReply([
            'body' => 'Leaving a reply',
            'user_id' => auth()->id(),
        ]);

        // Assert: assert that the subscription is stored, and user has new notification
        // Temp for now
        $this->assertCount(1, $thread->subscriptions);
        // $this->assertCount(1, $user->notifications);

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
