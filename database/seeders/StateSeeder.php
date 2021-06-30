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
    public function run()
    {
        //
        DB::table('states')->insert([
            [
                'state' => 'Andaman and Nicobar Islands',
                'code' => 35,
                'abbreviation' => 'AN',
            ],
            [
                'state' => 'Andhra Pradesh',
                'code' => 28,
                'abbreviation' => 'AP',
            ],
            [
                'state' => 'Andhra Pradesh (New)',
                'code' => 37,
                'abbreviation' => 'AD',
            ],
            [
                'state' => 'Arunachal Pradesh',
                'code' => 12,
                'abbreviation' => 'AR',
            ],
            [
                'state' => 'Assam',
                'code' => 18,
                'abbreviation' => 'AS',
            ],
            [
                'state' => 'Bihar',
                'code' => 10,
                'abbreviation' => 'BH',
            ],
            [
                'state' => 'Chandigarh',
                'code' => 4,
                'abbreviation' => 'CH',
            ],
            [
                'state' => 'Chattisgarh',
                'code' => 22,
                'abbreviation' => 'CT',
            ],
            [
                'state' => 'Dadra and Nagar Haveli',
                'code' => 26,
                'abbreviation' => 'DN',
            ],
            [
                'state' => 'Daman and Diu',
                'code' => 25,
                'abbreviation' => 'DD',
            ],
            [
                'state' => 'Delhi',
                'code' => 7,
                'abbreviation' => 'DL',
            ],
            [
                'state' => 'Goa',
                'code' => 30,
                'abbreviation' => 'GA',
            ],
            [
                'state' => 'Gujarat',
                'code' => 24,
                'abbreviation' => 'GJ',
            ],
            [
                'state' => 'Haryana',
                'code' => 6,
                'abbreviation' => 'HR',
            ],
            [
                'state' => 'Himachal Pradesh',
                'code' => 2,
                'abbreviation' => 'HP',
            ],
            [
                'state' => 'Jammu and Kashmir',
                'code' => 1,
                'abbreviation' => 'JK',
            ],
            [
                'state' => 'Jharkhand',
                'code' => 20,
                'abbreviation' => 'JH',
            ],
            [
                'state' => 'Karnataka',
                'code' => 29,
                'abbreviation' => 'KA',
            ],
            [
                'state' => 'Kerala',
                'code' => 32,
                'abbreviation' => 'KL',
            ],
            [
                'state' => 'Lakshadweep Islands',
                'code' => 31,
                'abbreviation' => 'LD',
            ],
            [
                'state' => 'Madhya Pradesh',
                'code' => 23,
                'abbreviation' => 'MP',
            ],
            [
                'state' => 'Maharashtra',
                'code' => 27,
                'abbreviation' => 'MH',
            ],
            [
                'state' => 'Manipur',
                'code' => 14,
                'abbreviation' => 'MN',
            ],
            [
                'state' => 'Meghalaya',
                'code' => 17,
                'abbreviation' => 'ME',
            ],
            [
                'state' => 'Mizoram',
                'code' => 15,
                'abbreviation' => 'MI',
            ],
            [
                'state' => 'Nagaland',
                'code' => 13,
                'abbreviation' => 'NL',
            ],
            [
                'state' => 'Odisha',
                'code' => 21,
                'abbreviation' => 'OR',
            ],
            [
                'state' => 'Pondicherry',
                'code' => 34,
                'abbreviation' => 'PY',
            ],
            [
                'state' => 'Punjab',
                'code' => 3,
                'abbreviation' => 'PB',
            ],
            [
                'state' => 'Rajasthan',
                'code' => 8,
                'abbreviation' => 'RJ',
            ],
            [
                'state' => 'Sikkim',
                'code' => 11,
                'abbreviation' => 'SK',
            ],
            [
                'state' => 'Tamil Nadu',
                'code' => 33,
                'abbreviation' => 'TN',
            ],
            [
                'state' => 'Telangana',
                'code' => 36,
                'abbreviation' => 'TS',
            ],
            [
                'state' => 'Tripura',
                'code' => 16,
                'abbreviation' => 'TR',
            ],
            [
                'state' => 'Uttar Pradesh',
                'code' => 9,
                'abbreviation' => 'UP',
            ],
            [
                'state' => 'Uttarakhand',
                'code' => 5,
                'abbreviation' => 'UT',
            ],
            [
                'state' => 'West Bengal',
                'code' => 19,
                'abbreviation' => 'WB',
            ],
        ]);
    }
}
