<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function guests_cannot_reply_in_forum_threads()
    {
        $this->post('/threads/slug/1/replies', [])->assertRedirect('login');
    }

    /** @test */
    function an_authenticated_user_can_reply_in_forum_threads()
    {
        $thread = factory(Thread::class)->create();
        $user = factory(User::class)->create();
        $this->assertCount(0, $thread->fresh()->replies);

        $this->actingAs($user)
            ->from($thread->path())
            ->post("/threads/{$thread->channel->slug}/{$thread->id}/replies", [
                'body' => 'More Cowbell, please.',
            ])->assertRedirect($thread->path());

        $response = $this->get($thread->path());
        $response->assertSee('More Cowbell, please');
        $this->assertCount(1, $thread->fresh()->replies);
    }

    /** @test */
    function a_reply_requires_a_body()
    {
        $thread = factory(Thread::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->from($thread->path())
            ->json('POST',"/threads/{$thread->channel->slug}/{$thread->id}/replies", [
                'body' => null,
            ])->assertJsonValidationErrors('body');
    }

    /** @test */
    function a_replies_body_should_be_atleast_2_characters_long()
    {
        $thread = factory(Thread::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->from($thread->path())
            ->json('POST',"/threads/{$thread->channel->slug}/{$thread->id}/replies", [
                'body' => 'A',
            ])->assertJsonValidationErrors('body');
    }

    /** @test */
    function guests_cannot_delete_replies()
    {
        $this->json('DELETE', "/replies/1")->assertStatus(401); // unauthorized
    }

    /** @test */
    function unauthorized_users_cannot_delete_replies()
    {
        $john = factory(User::class)->create();
        $johnsReply = factory(Reply::class)->create(['user_id' => $john->id]);
        $this->assertCount(1, $john->replies);
        $otherUser = factory(User::class)->create();

        $response = $this->actingAs($otherUser)->json('DELETE', "/replies/{$johnsReply->id}");

        $response->assertStatus(403);
        $this->assertCount(1, $john->fresh()->replies);
    }

    /** @test */
    function authorized_users_can_delete_replies()
    {
        // Arrange: authorized user, and a reply
        $user = factory(User::class)->create();
        $reply = factory(Reply::class)->create(['user_id' => $user->id]);
        $this->assertCount(1, $user->replies);
        // Act: submit a delete request for the reply
        $response = $this->actingAs($user)->json('DELETE', "/replies/{$reply->id}");

        // Assert: the reply has been deleted
        $response->assertStatus(200);
        $this->assertCount(0, $user->fresh()->replies);
    }

    /** @test */
    function unauthorized_users_cannot_update_replies()
    {
        $john = factory(User::class)->create();
        $johnsReply = factory(Reply::class)->create([
            'user_id' => $john->id,
            'body' => 'Before Update'
        ]);
        $notJohn = factory(User::class)->create();

        $response = $this->actingAs($notJohn)->json('PATCH', "/replies/{$johnsReply->id}", [
            'body' => 'Updated'
        ]);

        $response->assertStatus(403);
        $this->assertEquals('Before Update', $johnsReply->fresh()->body);
    }

    /** @test */
    function authorized_users_can_update_replies()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $reply = factory(Reply::class)->create([
            'user_id' => $user->id,
            'body' => 'Before Update'
        ]);

        $response = $this->actingAs($user)->json('PATCH', "/replies/{$reply->id}", [
            'body' => 'Updated'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated', $reply->fresh()->body);
    }
}
