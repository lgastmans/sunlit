<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    //

    public function index()
    {
        return view('inventory-movement.index');
    }
}
