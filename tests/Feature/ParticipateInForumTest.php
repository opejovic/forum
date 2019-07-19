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
        $this->assertEquals(0, $thread->fresh()->replies_count);

        $this->actingAs($user)
            ->from($thread->path())
            ->json('POST', "/threads/{$thread->channel->slug}/{$thread->id}/replies", [
                'body' => 'More Cowbell, please.',
            ])->assertStatus(201);

        $this->assertCount(1, $thread->fresh()->replies);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test */
    function a_reply_requires_a_body()
    {
        $thread = factory(Thread::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->from($thread->path())
            ->json('POST', "/threads/{$thread->channel->slug}/{$thread->id}/replies", [
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
            ->json('POST', "/threads/{$thread->channel->slug}/{$thread->id}/replies", [
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
        $user = factory(User::class)->create();
        $thread = factory(Thread::class)->create();
        $reply = factory(Reply::class)->create([
            'thread_id' => $thread->id,
            'user_id'   => $user->id
        ]);
        $this->assertCount(1, $user->replies);

        $response = $this->actingAs($user)->json('DELETE', "/replies/{$reply->id}");

        $response->assertStatus(200);
        $this->assertCount(0, $user->fresh()->replies);
        $this->assertEquals(0, $thread->fresh()->replies_count);
    }

    /** @test */
    function unauthorized_users_cannot_update_replies()
    {
        $john = factory(User::class)->create();
        $johnsReply = factory(Reply::class)->create([
            'user_id' => $john->id,
            'body'    => 'Before Update'
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
        $user = factory(User::class)->create();
        $reply = factory(Reply::class)->create([
            'user_id' => $user->id,
            'body'    => 'Before Update'
        ]);

        $response = $this->actingAs($user)->json('PATCH', "/replies/{$reply->id}", [
            'body' => 'Updated'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated', $reply->fresh()->body);
    }

    /** @test */
    function replies_that_contain_spam_may_not_be_created()
    {
        $user = factory(User::class)->create();
        $thread = factory(Thread::class)->create();

        $this->actingAs($user)->json('POST', "threads/{$thread->channel->slug}/{$thread->id}/replies", [
            'body' => 'Yahoo Customer Support'
        ])->assertJsonValidationErrors('body');

        $this->assertCount(0, $thread->replies);
    }
    
    /** @test */
    function users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $thread = factory(Thread::class)->create();
        auth()->login($user = factory(User::class)->create());

        $this->actingAs($user)->json('POST', "{$thread->path()}/replies", [
            'body' => 'First reply'
        ])->assertStatus(201);

        $this->assertCount(1, $user->replies);

        $this->actingAs($user->fresh())->json('POST', "{$thread->path()}/replies", [
            'body' => 'Second reply'
        ])->assertStatus(422);

        $this->assertCount(1, $user->fresh()->replies);
	}
	
	/** @test */
	function authenticated_users_cannot_participate_in_forum_unless_their_email_is_confirmed()
	{
		$user = factory(User::class)->create(['email_verified_at' => null]);
	
		$this->actingAs($user)->get(route('threads.create'))->assertRedirect('email/verify'); // unauthorized
	}
}
