<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'warehouse_id' => $this->faker->numberBetween(1, 5),
            'supplier_id' => $this->faker->numberBetween(11, 20),
            'order_number' => $this->faker->bothify('BM#####'),
            'boe_number' => $this->faker->randomNumber(5, true),
            'ordered_at' => $this->faker->dateTimeThisMonth('+ 2 days'),
            'expected_at' => $this->faker->dateTimeThisMonth(),
            'received_at' => $this->faker->dateTimeThisMonth('+ 5 weeks'),
            'credit_period' => $this->faker->randomNumber(2, true),
            'amount_usd' => $this->faker->randomNumber(4, true),
            'amount_inr' => $this->faker->randomNumber(8, true),
            'customs_ex_rate' => $this->faker->randomNumber(2, true),
            'se_ex_rate' => $this->faker->randomNumber(2, true),
            'duty_amount' => $this->faker->randomNumber(3, true),
            'social_surcharge' => $this->faker->randomNumber(3, true),
            'igst' => $this->faker->randomNumber(3, true),
            'bank_charges' => $this->faker->randomNumber(3, true),
            'clearing_charges' => $this->faker->randomNumber(3, true),
            'transport_charges' => $this->faker->randomNumber(3, true),
            'se_due_date' => $this->faker->dateTimeThisMonth('+ 2 weeks'),
            'se_payment_date' => $this->faker->dateTimeThisMonth('+ 5 weeks'),
            'status' => $this->faker->numberBetween(1, 7),
            'user_id' => 1,
        ];
    }
}
