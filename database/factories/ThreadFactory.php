<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Thread::class, function (Faker $faker) {
    $title = $faker->sentence;
	
	return [
        'user_id' => function () {
        	return factory(User::class)->create()->id;
        },
        'channel_id' => function () {
            return factory(Channel::class)->create()->id;
        },
		'title' => $title,
		'slug' => str_slug($title),
		'body' => $faker->text,
		'visits' => 0,
    ];
});
