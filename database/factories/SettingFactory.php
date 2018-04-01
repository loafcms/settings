<?php

$factory->define(\Loaf\Settings\Setting::class, function (Faker\Generator $faker) {
    $attributes = [];

    $path = [];
    for ($i = 0; $i < $faker->numberBetween(1, 3); $i++) {
        $path[] = $faker->word;
    }

    $attributes['path'] = implode('.', $path);

    $type = $faker->randomElement(['string', 'integer', 'boolean']);

    $attributes['type'] = $type;

    switch ($type) {
        case 'string':
            $value = $faker->sentence;
            break;
        case 'integer':
            $value = $faker->numberBetween;
            break;
        case 'boolean':
            $value = $faker->boolean;
    }

    $attributes['value'] = $value;

    return $attributes;
});
