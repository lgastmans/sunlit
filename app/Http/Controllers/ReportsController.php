<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{

    //
    public function index() {
        return view('reports.stock');
    }


    public function closingStock()
    {
        return view('reports.closing-stock');
    }

    public function salesProductTotals()
    {
        return view('reports.sales-product-totals');
    }

}
