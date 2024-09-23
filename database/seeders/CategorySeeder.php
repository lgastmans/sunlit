<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //
        DB::table('categories')->insert([
            [
                'name' => 'Solar Panel',
            ],
            [
                'name' => 'Inverter',
            ],
        ]);
    }
}
