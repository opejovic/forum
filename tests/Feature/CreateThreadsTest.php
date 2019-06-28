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

    /** @test */
    function a_thread_requires_a_valid_channel()
    {
        $this->publishThread(['channel_id' => null])->assertJsonValidationErrors('channel_id');

        // non existent channel
        $this->publishThread(['channel_id' => 999999])->assertJsonValidationErrors('channel_id');
    }

    /** @test */
    function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])->assertJsonValidationErrors('title');
    }

    /** @test */
    function a_thread_title_should_be_atleast_2_characters_long()
    {
        $this->publishThread(['title' => 'A'])->assertJsonValidationErrors('title');
    }

    /** @test */
    function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])->assertJsonValidationErrors('body');
    }

    /** @test */
    function a_threads_body_should_be_atleast_2_characters_long()
    {
        $this->publishThread(['body' => 'A'])->assertJsonValidationErrors('body');
    }

    // helpers
    private function publishThread($overrides = null)
    {
        $user = factory(User::class)->create();
        $this->assertCount(0, $user->fresh()->threads);

        $response = $this->actingAs($user)->json('POST', "/threads", $this->validParams($overrides));

        return $response;
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'channel_id' => factory(Channel::class)->create()->id,
            'title' => 'MoreCowbell',
            'body' => 'Some Random Thread Body',
        ], $overrides);
    } 
}
