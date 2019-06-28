<?php

namespace Tests\Unit;

use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChannelTest extends TestCase
{
	use RefreshDatabase;
	
	/** @test */
	function it_can_have_many_threads()
	{
	    $channel = factory(Channel::class)->create();

	    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $channel->threads);
	}
}
