<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class BrandFactory extends Factory
{
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');

        return [
            'title' => implode(' ', $faker->words(5)),
            'category' => $faker->randomElement([0,1]),
            'image' => '',
        ];
    }
}
