<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //
        DB::table('states')->insert([
            [
                'name' => 'Andaman and Nicobar Islands',
                'code' => 35,
                'abbreviation' => 'AN',
            ],
            [
                'name' => 'Andhra Pradesh',
                'code' => 28,
                'abbreviation' => 'AP',
            ],
            [
                'name' => 'Andhra Pradesh (New)',
                'code' => 37,
                'abbreviation' => 'AD',
            ],
            [
                'name' => 'Arunachal Pradesh',
                'code' => 12,
                'abbreviation' => 'AR',
            ],
            [
                'name' => 'Assam',
                'code' => 18,
                'abbreviation' => 'AS',
            ],
            [
                'name' => 'Bihar',
                'code' => 10,
                'abbreviation' => 'BH',
            ],
            [
                'name' => 'Chandigarh',
                'code' => 4,
                'abbreviation' => 'CH',
            ],
            [
                'name' => 'Chattisgarh',
                'code' => 22,
                'abbreviation' => 'CT',
            ],
            [
                'name' => 'Dadra and Nagar Haveli',
                'code' => 26,
                'abbreviation' => 'DN',
            ],
            [
                'name' => 'Daman and Diu',
                'code' => 25,
                'abbreviation' => 'DD',
            ],
            [
                'name' => 'Delhi',
                'code' => 7,
                'abbreviation' => 'DL',
            ],
            [
                'name' => 'Goa',
                'code' => 30,
                'abbreviation' => 'GA',
            ],
            [
                'name' => 'Gujarat',
                'code' => 24,
                'abbreviation' => 'GJ',
            ],
            [
                'name' => 'Haryana',
                'code' => 6,
                'abbreviation' => 'HR',
            ],
            [
                'name' => 'Himachal Pradesh',
                'code' => 2,
                'abbreviation' => 'HP',
            ],
            [
                'name' => 'Jammu and Kashmir',
                'code' => 1,
                'abbreviation' => 'JK',
            ],
            [
                'name' => 'Jharkhand',
                'code' => 20,
                'abbreviation' => 'JH',
            ],
            [
                'name' => 'Karnataka',
                'code' => 29,
                'abbreviation' => 'KA',
            ],
            [
                'name' => 'Kerala',
                'code' => 32,
                'abbreviation' => 'KL',
            ],
            [
                'name' => 'Lakshadweep Islands',
                'code' => 31,
                'abbreviation' => 'LD',
            ],
            [
                'name' => 'Madhya Pradesh',
                'code' => 23,
                'abbreviation' => 'MP',
            ],
            [
                'name' => 'Maharashtra',
                'code' => 27,
                'abbreviation' => 'MH',
            ],
            [
                'name' => 'Manipur',
                'code' => 14,
                'abbreviation' => 'MN',
            ],
            [
                'name' => 'Meghalaya',
                'code' => 17,
                'abbreviation' => 'ME',
            ],
            [
                'name' => 'Mizoram',
                'code' => 15,
                'abbreviation' => 'MI',
            ],
            [
                'name' => 'Nagaland',
                'code' => 13,
                'abbreviation' => 'NL',
            ],
            [
                'name' => 'Odisha',
                'code' => 21,
                'abbreviation' => 'OR',
            ],
            [
                'name' => 'Pondicherry',
                'code' => 34,
                'abbreviation' => 'PY',
            ],
            [
                'name' => 'Punjab',
                'code' => 3,
                'abbreviation' => 'PB',
            ],
            [
                'name' => 'Rajasthan',
                'code' => 8,
                'abbreviation' => 'RJ',
            ],
            [
                'name' => 'Sikkim',
                'code' => 11,
                'abbreviation' => 'SK',
            ],
            [
                'name' => 'Tamil Nadu',
                'code' => 33,
                'abbreviation' => 'TN',
            ],
            [
                'name' => 'Telangana',
                'code' => 36,
                'abbreviation' => 'TS',
            ],
            [
                'name' => 'Tripura',
                'code' => 16,
                'abbreviation' => 'TR',
            ],
            [
                'name' => 'Uttar Pradesh',
                'code' => 9,
                'abbreviation' => 'UP',
            ],
            [
                'name' => 'Uttarakhand',
                'code' => 5,
                'abbreviation' => 'UT',
            ],
            [
                'name' => 'West Bengal',
                'code' => 19,
                'abbreviation' => 'WB',
            ],
        ]);
    }
}
