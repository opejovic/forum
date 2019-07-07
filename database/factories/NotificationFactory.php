<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use App\Models\User;
use Faker\Generator as Faker;
use Faker\Provider\Uuid;
use Illuminate\Notifications\DatabaseNotification;

$factory->define(DatabaseNotification::class, function (Faker $faker) {
	return [
		'id' => Uuid::uuid(),
		'type' => "App\Notifications\ThreadWasUpdated",
		'notifiable_type' => "App\Models\User",
		'notifiable_id' => auth()->id() ?: factory(User::class)->create()->id(),
		'data' => ["message" => "Notification message"],
		'read_at' => null,
		'created_at' => now(),
		'updated_at' => now(),
	];
});
