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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(LArtie\Backend\Models\Alert::class, function (Faker\Generator $faker) {
    return [
        'alert_id' => $faker->randomNumber(),
        'name' => $faker->name,
        'description' => $faker->text,
        'active' => true,
    ];
});

$factory->define(LArtie\Backend\Models\Event::class, function (Faker\Generator $faker) {
    return [
        'type' => str_random(10),
        'value' => $faker->numberBetween(1, 20),
        'data_source' => str_random(),
        'datetime_recorded' => $faker->dateTime,
        'alert_id' => factory(\LArtie\Backend\Models\Alert::class)->create()->id,
    ];
});

$factory->define(LArtie\Backend\Models\FlightNumber::class, function (Faker\Generator $faker) {
    return [
        'carrier_code' => 'AA',
        'flight_number' => '100',
        'departure_time' => $faker->dateTime,
        'arrival_time' => $faker->dateTime,
        'flight_id' => factory(\LArtie\Backend\Models\FlightStatus::class)->create()->id,
    ];
});

$factory->define(LArtie\Backend\Models\FlightStatus::class, function (Faker\Generator $faker) {
    return [
        'flight_id' => $faker->randomNumber(),
        'departure_iata' => 'MQF',
        'arrival_iata' => 'DME',
        'status' => '',
        'departure_date_local' => $faker->dateTime,
        'departure_date_utc' => $faker->dateTime,
        'arrival_date_local' => $faker->dateTime,
        'arrival_date_utc' => $faker->dateTime,
    ];
});

$factory->define(LArtie\Backend\Models\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'telegram_id' => $faker->randomNumber(),
    ];
});

$factory->define(\LArtie\Google\Models\Token::class, function (Faker\Generator $faker) {
    return [
        'access_token' => bcrypt(str_random()),
        'token_type' => str_random(),
        'expires_in' => $faker->unixTime,
        'id_token' => $faker->randomNumber(),
        'refresh_token' => bcrypt(str_random()),
        'created' => $faker->dateTime,
    ];
});
