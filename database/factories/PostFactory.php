<?php

use Faker\Generator as Faker;
use App\User;
use App\Post;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id,
        'title' => $faker->title,
        'content' => $faker->text
    ];
});
