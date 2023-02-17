<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\State;
use App\Models\Inventory;
use App\Models\SaleOrder;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\SaleOrderItem;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('view dashboard')){
            $stock_filter = Inventory::getStockFilterList();
            $due_orders = PurchaseOrder::due()->get();   
            $overdue_orders = PurchaseOrder::overdue()->count();     
            
            $exchange_rate_update_ago = Carbon::parse( \Setting::get('exchange_rate_updated_at'))->diffForHumans();

           // $sale_overview = SaleOrderItem::getNumberAndTotalSaleByRange('monthly');
            // There's an issue with the json format returned by the call
            
            $sale_order = new SaleOrder;
            $cur_year = date('Y');
            $sale_order_totals = $sale_order->calculateSalesTotals("period_monthly",$cur_year);

            $state_totals = $sale_order->calculateStateSalesTotals("period_monthly",$cur_year);

            return view('dashboard', ['stock_filter' => $stock_filter,'due_orders' => $due_orders, 'overdue_orders' => $overdue_orders, 'exchange_rate_update_ago' => $exchange_rate_update_ago, 'sale_order_totals'=> $sale_order_totals, 'state_totals'=>$state_totals]);
        }
        return abort(403, trans('error.unauthorized'));
    }
}
