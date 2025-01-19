<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;
use Modules\Notifications\Entities\NotificationTemplate;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(NotificationTemplate::class,
    function (Faker $faker) {
        return [
            'body' => $faker->word,
            'key' => $faker->word,
            'subject' => $faker->word,
            'send_copy_to' => $faker->word,
            'status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    });
