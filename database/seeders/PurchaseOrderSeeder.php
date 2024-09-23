<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        PurchaseOrder::factory()->count(50)->create();
    }
}
