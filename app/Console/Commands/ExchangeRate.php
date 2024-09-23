<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ExchangeRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchangerate:update {--currency=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update currency exchange rate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = 'https://openexchangerates.org/api/latest.json?app_id=fe761d5c94a04335a88625034493234a&base=usd&symbols=inr';
        $response = Http::get($url);
        if ($response->successful()) {
            $data = $response->json();
            $dt = Carbon::parse($data['timestamp']);
            $updated_at = $dt->toDateTimeString();
            $rate = $data['rates']['INR'];
            if ($rate && $updated_at) {
                \Setting::set('purchase_order.exchange_rate', $rate);
                \Setting::set('exchange_rate_updated_at', $updated_at);
                \Setting::save();
            }
        }
    }
}
