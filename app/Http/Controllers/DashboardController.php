<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

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
            return view('dashboard', ['due_orders' => $due_orders, 'overdue_orders' => $overdue_orders]);
        }
        return abort(403, trans('error.unauthorized'));
    }
}
