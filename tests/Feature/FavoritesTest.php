<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function guests_cannot_favorite_anything()
    {
        $this->json('POST', "/replies/1/favorites")->assertStatus(401);
    }

    /** @test */
    function authenticated_users_can_favorite_any_reply()
    {
        // Arrange: create a reply and
        $user = factory(User::class)->create(); 
        $reply = factory(Reply::class)->create();

        // Act: submit a post to /replies/{reply}/favorites
        $response = $this->actingAs($user)->json('POST', "/replies/{$reply->id}/favorites");

        // Assert: the favorites table has a record
        $this->assertCount(1, $reply->favorites);
    }

    /** @test */
    function authenticated_users_can_unfavorite_a_reply()
    {
        $this->withoutExceptionHandling();
        // Arrange: create a reply and
        $user = factory(User::class)->create(); 
        $reply = factory(Reply::class)->create();

        $response = $this->actingAs($user)->json('POST', "/replies/{$reply->id}/favorites");
        $this->assertCount(1, $reply->favorites);

        $response = $this->actingAs($user)->json('DELETE', "/replies/{$reply->id}/favorites");
        $this->assertCount(0, $reply->fresh()->favorites);
    }

    /** @test */
    function authenticated_users_may_only_favorite_a_reply_once()
    {
        $this->withoutExceptionHandling();
        // Arrange: create a reply and
        $user = factory(User::class)->create(); 
        $reply = factory(Reply::class)->create();
        $response = $this->actingAs($user)->json('POST', "/replies/{$reply->id}/favorites");
        $this->assertCount(1, $reply->favorites);

        // Act: submit a post to /replies/{reply}/favorites
        $response = $this->actingAs($user)->json('POST', "/replies/{$reply->id}/favorites");
        $this->assertCount(1, $reply->fresh()->favorites);

        // Assert: the favorites table has a record
    }
}
