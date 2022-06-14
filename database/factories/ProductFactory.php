<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    return [
      'name' => $this->faker->word(),
      'type' => $this->faker->randomElement(['pork', 'chicken', 'beef',]),
      'price' => $this->faker->randomFloat(0, 100, 500),
    ];
  }
}
