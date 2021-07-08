<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            //"id" => $record->id,
            //"state_id" =>$this->faker->randomDigit(),
            "company" =>$this->faker->name(),
            "address" =>$this->faker->name(),
            "address2" =>$this->faker->name(),
            "city" =>$this->faker->name(),
            "zip_code" =>$this->faker->randomDigit(),
            "gstin" =>$this->faker->name(),
            "contact_person" =>$this->faker->name(),
            "phone" =>$this->faker->phoneNumber(),
            "phone2" =>$this->faker->phoneNumber(),
            "email" =>$this->faker->unique()->safeEmail()
        ];
    }
}
