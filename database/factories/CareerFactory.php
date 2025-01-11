<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class CareerFactory extends Factory
{
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');

        return [
            'title' => implode(' ', $faker->words(5)),
            'job_open' => $faker->date('Y-m-d'),
            'job_closed' => $faker->date('Y-m-d'),
            'desc' => implode("\n\n", $faker->paragraphs(4)),
        ];
    }
}
