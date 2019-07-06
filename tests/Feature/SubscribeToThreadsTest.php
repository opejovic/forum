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
        $user = factory(User::class)->create();
        // Act: user posts to thread subscription endpoint and new reply is left for that thread
        $response = $this->actingAs($user)->json('POST', "{$thread->path()}/subscriptions");

        // Assert: assert that the subscription is stored, and user has new notification
        // Temp for now
        $this->assertCount(1, $thread->subscriptions);
        // $this->assertCount(1, $user->notifications);

    }
}
