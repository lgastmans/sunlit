<?php

namespace Database\Seeders;

use Akaunting\Setting\Facade as Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Setting::set("company.name" , "Sunlit Future");
        // Setting::set("company.address" , "");
        // Setting::set("company.city" , "Auroville");
        // Setting::set("company.zipcode" , "605101");
        // Setting::set("company.state" , "Tamil Nadu");
        // Setting::set("company.country" , "India");
        // Setting::set("company.phone" , "2622327");
        // Setting::set("company.email" , "rishi@sunlit.in");
        // Setting::set("company.gstin" , "0123456");

        // Setting::set("general.grid_rows" , "10");

        Setting::set('purchase_order.igst', '5');
        Setting::set('purchase_order.transport', '.5');
        Setting::set('purchase_order.customs_duty', '10');
        Setting::set('purchase_order.social_welfare_surcharge', '10');

        Setting::set('purchase_order.exchange_rate', '73.52');
        Setting::set('purchase_order.exchange_rate_updated_at', '2021-09-14 04:59:58');

        Setting::save();

    }
}
