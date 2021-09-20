<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryMovementController extends Controller
{
    //

    public function index()
    {
        $user = Auth::user();
        if ($user->can('list inventories'))
            return view('inventory-movement.index');
    
        return abort(403, trans('error.unauthorized'));
    }
}
