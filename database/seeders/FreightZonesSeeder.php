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
                'zone' => 'SOUTH',
                'states' => 'KARNATAKA,TAMIL NADU,KERALA,ANDHRA PRADESH,TELANGANA,PONDICHERRY',
            ],
            [
                'zone' => 'WEST',
                'states' => 'MAHARASHTRA,MADHYA PRADESH,GUJARAT,CHHATTISGARH,GOA,DIU & DAMAN',
            ],
            [
                'zone' => 'NORTH',
                'states' => 'HIMACHAL PRADESH,PUNJAB,HARYANA,UTTARKHAND,UTTAR PRADESH,RAJASTHAN,DELHI',
            ],
            [
                'zone' => 'EAST',
                'states' => 'BIHAR,ORISSA,WEST BENGAL,JHARKHAND,ASSAM',
            ],
            [
                'zone' => 'NORTHEAST',
                'states' => 'NAGALAND,MIZORAM,MANIPUR,MEGHALAYA,ANDHRA PRADESH, TRIPURA, SIKKIM',
            ],
            [
                'zone' => 'JK',
                'states' => 'JAMMU,KASHMIR,LADAKH',
            ]
        ]);
    }
}
