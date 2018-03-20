<?php

$factory->define( \Loaf\Settings\Setting::class, function (Faker\Generator $faker) {

    $attributes = [];

    $path = [];
    for( $i = 0; $i < $faker->numberBetween(1,3); $i++ )
        $path[] = $faker->word;

    $attributes['path'] = implode('.', $path );

    $type = $faker->randomElement( ['string', 'integer', 'array'] );

    $attributes['type'] = $type;

    switch( $type )
    {
        case 'string':
            $value = $faker->sentence;
            break;
        case 'integer':
            $value = $faker->numberBetween;
            break;
        case 'array':
            $value = $faker->words;
    }

    $attributes['value'] = $value;

    return $attributes;
    
});
