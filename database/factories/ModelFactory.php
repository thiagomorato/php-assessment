<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Task::class, function (Faker\Generator $faker) {
    return [
        'type' => $faker->word(),
        'content' => $faker->sentence(),
        'sort_order' => $faker->randomDigitNotNull(),
        'done' => $faker->boolean()
    ];
});
