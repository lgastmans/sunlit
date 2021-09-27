<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
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
            $due_orders = PurchaseOrder::due()->get();   
            $overdue_orders = PurchaseOrder::overdue()->count();     
            
            $exchange_rate_update_ago = Carbon::parse( \Setting::get('exchange_rate_updated_at'))->diffForHumans();

            return view('dashboard', ['due_orders' => $due_orders, 'overdue_orders' => $overdue_orders, 'exchange_rate_update_ago' => $exchange_rate_update_ago]);
        }
        return abort(403, trans('error.unauthorized'));
    }
}
