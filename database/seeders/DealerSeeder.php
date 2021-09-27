<?php

namespace Database\Seeders;

use App\Models\Dealer;
use Illuminate\Database\Seeder;


class DealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Dealer::factory()->count(50)->create();
    }
}
