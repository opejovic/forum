<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function guests_cannot_publish_threads()
    {
        $this->get('threads/create')->assertRedirect('login'); 
        $this->json('POST', "/threads", [])->assertStatus(401); // unauthorized
    }

    /** @test */
    function an_authenticated_user_can_publish_new_threads()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $channel = factory(Channel::class)->create();

        $response = $this->actingAs($user)->json('POST', "/threads", [
            'channel_id' => $channel->id,
            'title' => 'More Cowbell',
            'body' => 'I post here some random paragraph',
        ]);

        $this->assertCount(1, $user->fresh()->threads);
        $thread = Thread::first();
        $response->assertRedirect("/threads/{$thread->channel->slug}/{$thread->id}");
        $this->assertEquals('More Cowbell', $thread->title);
        $this->assertEquals('I post here some random paragraph', $thread->body);
    }
}
