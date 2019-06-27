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
}