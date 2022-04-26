<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FreightZonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('freight_zones')->insert([
            [
                'name' => 'SOUTH',
                'rate_per_kg' => 8,
            ],
            [
                'name' => 'WEST',
                'rate_per_kg' => 8,
            ],
            [
                'name' => 'NORTH',
                'rate_per_kg' => 8,
            ],
            [
                'name' => 'EAST',
                'rate_per_kg' => 8,
            ],
            [
                'name' => 'NORTHEAST',
                'rate_per_kg' => 8,
            ],
            [
                'name' => 'JK',
                'rate_per_kg' => 8,
            ]
        ]);
    }
}
