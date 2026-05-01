<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\id_ID\Restaurant($faker));
        $faker->addProvider(new \Mmo\Faker\PicsumProvider($faker));

        return [
            'name' => $faker->foodName() . ' ' . $faker->word(),
            'image' => $faker->picsumUrl(640, 480),
            'description' => $faker->sentence(),
            'price' => $faker->numberBetween(10, 100) * 1000,
            'stock' => $faker->numberBetween(0, 100),
        ];
    }
}
