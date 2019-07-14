<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_has_an_owner()
    {
        $owner = factory(User::class)->create();
        $reply = factory(Reply::class)->create(['user_id' => $owner->id]);

        $this->assertTrue($reply->owner->is($owner));
    }

    /** @test */
    function it_can_tell_if_its_been_favorited_by_auth_user()
    {
        auth()->login($user = factory(User::class)->create());
        $reply = factory(Reply::class)->create();
        $reply->favorite();

        $this->assertTrue($reply->isFavorited());
    }

    /** @test */
    function it_can_tell_if_it_was_just_published()
    {
        $reply = factory(Reply::class)->create();

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = now()->subMonth();
        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    function it_can_get_mentioned_users_from_its_body()
    {
        $john = factory(User::class)->create(['name' => 'JohnDoe', 'avatar_path' => null]);
		$reply = factory(Reply::class)->create(['body' => '@JohnDoe is mentioned in this reply.']);
		$this->assertContains($john->toArray(), $reply->mentionedUsers()->toArray());
	}
	
	/** @test */
	function it_wraps_mentioned_user_name_in_anchor_tags()
	{
		$john = factory(User::class)->create(['name' => 'JohnDoe', 'avatar_path' => null]);
		$reply = factory(Reply::class)->create(['body' => 'Hello @JohnDoe.']);
		
		$this->assertContains($john->toArray(), $reply->mentionedUsers()->toArray());
		$this->assertEquals('Hello <a href="/profiles/JohnDoe">@JohnDoe</a>.' ,$reply->body);
	}
}
