<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('taxes')->insert([
            [
                'name' => 'None',
                'amount' => 0,
            ],
            [
                'name' => 'GST 0.25%',
                'amount' => 25,
            ],
            [
                'name' => 'GST 5%',
                'amount' => 500,
            ],
            [
                'name' => 'GST 12%',
                'amount' => 1200,
            ],
            [
                'name' => 'GST 18%',
                'amount' => 1800,
            ],
            [
                'name' => 'GST 28%',
                'amount' => 2800,
            ],
        ]);
    }
}
