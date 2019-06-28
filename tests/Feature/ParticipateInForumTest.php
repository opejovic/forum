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
        $this->withoutExceptionHandling();
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
}
