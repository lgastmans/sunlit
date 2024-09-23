<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'state_id' => $this->faker->numberBetween($min = 1, $max = 37),
            'name' => $this->faker->company(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'zip_code' => $this->faker->postcode(),
            'contact_person' => $this->faker->name().' '.$this->faker->jobTitle(),
            'phone' => $this->faker->e164PhoneNumber(),
            'phone2' => $this->faker->e164PhoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
