<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function authenticated_users_can_view_their_profiles()
    {
        $john = factory(User::class)->create([
            'name' => 'JohnDoe',
        ]);

        $response = $this->get("/profiles/{$john->name}");

        $response->assertViewIs('profiles.show');
        $response->assertSee($john->name);
    }

    /** @test */
    function profiles_display_all_threads_associated_by_the_user()
    {
        $john = factory(User::class)->create([
            'name' => 'JohnDoe',
        ]);
        $johnsThread = factory(Thread::class)->create(['user_id' => $john->id]);

        $response = $this->get("/profiles/{$john->name}");

        $response->assertViewIs('profiles.show');
        $response->assertSee($johnsThread->title);   
        $response->assertSee($johnsThread->body);   
    }
}
