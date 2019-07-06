<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function notification_is_prepared_for_subscribed_thread_users_that_do_not_own_the_reply()
    {
        $thread = factory(Thread::class)->create();
        auth()->login($replyCreator = factory(User::class)->create());
        $john = factory(User::class)->create();
        $thread->subscribe($john->id);
        $thread->subscribe(auth()->id());

        $thread->addReply([
            'body' => 'Leaving a reply',
            'user_id' => $replyCreator->id,
        ]);

        $this->assertCount(0, $replyCreator->fresh()->notifications);
        $this->assertCount(1, $john->fresh()->notifications);
    }

    /** @test */
    function user_can_fetch_all_notifications()
    {
        $this->withoutExceptionHandling();
        $thread = factory(Thread::class)->create();
        auth()->login($user = factory(User::class)->create());
        $thread->subscribe();

        $otherUser = factory(User::class)->create();

        $thread->addReply([
            'body' => 'Some random body here',
            'user_id' => $otherUser->id
        ]);

        $this->assertCount(1, $user->unreadNotifications);

        $response = $this->json('GET', "/profiles/{$user->name}/notifications");

        $response->assertStatus(200);
        $this->assertArraySubset(
            $user->fresh()->unreadNotifications->toArray(), 
            $response->json()
        );        
    }


    /** @test */
    function user_can_mark_notification_as_read()
    {
        // Arrange
        $thread = factory(Thread::class)->create();
        auth()->login($user = factory(User::class)->create());
        $thread->subscribe();

        $otherUser = factory(User::class)->create();

        $thread->addReply([
            'body' => 'Some random body here',
            'user_id' => $otherUser->id
        ]);

        $this->assertCount(1, $user->unreadNotifications);
        $notification = $user->unreadNotifications->first();

        // Act
        // user deletes the notifications
        $response = $this->json('DELETE', "/profiles/{$user->name}/notifications/{$notification->id}");

        // Assert
        $response->assertStatus(200);
        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }
}
