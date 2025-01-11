<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');

        return [
            'title' => implode(' ', $faker->words(5)),
            'subtitle' => implode(' ', $faker->words(3)),
            'slug' => $faker->word() . '-' . $faker->word(),
            // 'image' => $faker->imageUrl(640, 480, 'animals', true),
            'image' => '',
            'desc' => implode("\n\n", $faker->paragraphs(4)),
        ];
    }
}
