<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'state_id' => $this->faker->numberBetween($min = 1, $max = 37),
            'company' => $this->faker->company(),
            'address' => $this->faker->streetAddress(),
            'address2' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'zip_code' => $this->faker->postcode(),
            'gstin' => $this->faker->numerify('###############'),
            'contact_person' => $this->faker->name().' '.$this->faker->jobTitle(),
            'phone' => $this->faker->e164PhoneNumber(),
            'phone2' => $this->faker->e164PhoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
