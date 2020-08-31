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

use App\Shop\Permissions\Permission;
/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Permission::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->unique()->word,
        'display_name' => '',
        'description' => ''
    ];
});
