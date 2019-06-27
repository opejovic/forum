<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Thread;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Thread::class, function (Faker $faker) {
    return [
        'user_id' => function () {
        	return factory(User::class)->create()->id;
        },
        'title' => $faker->sentence,
        'body' => $faker->text,
    ];
});