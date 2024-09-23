<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => $this->faker->numberBetween($min = 1, $max = 3),
            'supplier_id' => $this->faker->numberBetween($min = 15, $max = 24),
            'tax_id' => $this->faker->numberBetween($min = 1, $max = 6),
            'code' => $this->faker->bothify('?????-#####'),
            'name' => $this->faker->bothify('?????-#####'),
            'model' => $this->faker->word(2),
            'minimum_quantity' => $this->faker->numberBetween(0, 100),
            'purchase_price' => $this->faker->numerify('######'),
            'cable_length' => $this->faker->numberBetween(0, 100),
            'kw_rating' => $this->faker->numberBetween(0, 100),
            'part_number' => $this->faker->bothify('?????-#####'),
            'notes' => $this->faker->text(),
        ];
    }
}
