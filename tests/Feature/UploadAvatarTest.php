<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadAvatarTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function guests_cannot_upload_avatars()
	{
		$this->json('POST', "/api/users/1/avatar", [])->assertStatus(401); // unauthorized
	}

	/** @test */
	function avatar_must_be_a_valid_image_file()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)->json('POST', "/api/users/{$user->id}/avatar", [
			'avatar' => 'not-an-image',
		])->assertStatus(422);
	}

	/** @test */
	function an_authenticated_user_may_add_an_avatar_to_their_profile()
	{
		Storage::fake();

		$user = factory(User::class)->create();

		$this->actingAs($user)->json("POST", "/api/users/{$user->id}/avatar", [
			'avatar' => $file = UploadedFile::fake()->image('avatar.jpg'),
		]);
		
		$this->assertEquals("avatars/{$file->hashName()}", $user->avatar_path);
		Storage::disk('public')->assertExists("avatars/{$file->hashName()}");
	}
}
