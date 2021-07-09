<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('taxes')->insert([
            [
                'name' => 'None',
                'amount' => 0,
            ],
            [
                'name' => 'GST 0.25%',
                'amount' => 0.25,
            ],
            [
                'name' => 'GST 5%',
                'amount' => 5,
            ],
            [
                'name' => 'GST 12%',
                'amount' => 12,
            ],
            [
                'name' => 'GST 18%',
                'amount' => 18,
            ],
            [
                'name' => 'GST 28%',
                'amount' => 28,
            ],
        ]);        
    }
}
