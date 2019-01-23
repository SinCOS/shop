<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Shop::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'status' => 0,
        'concat_phone' => $faker->phoneNumber,
        'note' => '',
        'agent_id'=> 1,
        'work_id' => 2,


    ];
});
