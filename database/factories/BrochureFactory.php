<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class BrochureFactory extends Factory
{
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');

        return [
            'title' => implode(' ', $faker->words(5)),
            'is_choosen' => $faker->randomElement([0,1]),
            'file' => '',
        ];
    }
}
