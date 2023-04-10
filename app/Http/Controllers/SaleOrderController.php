<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

use DB;
use PDF;
use NumberFormatter;
use App\Models\Dealer;
use App\Models\Inventory;
use App\Models\SaleOrder;
use Illuminate\Http\Request;
use App\Models\SaleOrderItem;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSaleOrderRequest;

class SaleOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list sale orders')){
            $status = SaleOrder::getStatusList();
            return view('sale_orders.index', ['status' => $status]);

        }
        return abort(403, trans('error.unauthorized'));
    }


    public function getListForDatatables(Request $request)
    {
        $draw = 1;
        if ($request->has('draw'))
            $draw = $request->get('draw');

        $start = 0;
        if ($request->has('start'))
            $start = $request->get("start");

        $length = 10;
        if ($request->has('length')) {
            $length = $request->get("length");
        }

        $order_column = 'order_number';
        $order_dir = 'ASC';
        $order_arr = array();
        if ($request->has('order')) {
            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];

            // the sale order datatable isn't the same in index than in warehouse>sale orders 
            if ($request->has('source') && $request->source == "warehouses"){
                switch ($column_index){
                    case 1:
                        $order_column = "dealers.company";
                        break;
                    case 4:
                        $order_column = "users.name";
                        break;
                    default:
                        $order_column = $column_arr[$column_index]['data'];
                }
            }else{
                switch ($column_index){
                    case 1:
                        $order_column = "sale_orders.warehouse_id";
                        break;
                    case 2:
                        $order_column = "dealers.company";
                        break;
                    case 7:
                        $order_column = "users.name";
                        break;
                    default:
                        $order_column = $column_arr[$column_index]['data'];
                }
                
                $order_dir = $order_arr[0]['dir'];
            }
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = SaleOrder::count();        

        $query = SaleOrder::query();
        $query->join('dealers', 'dealers.id', '=', 'dealer_id');
        $query->join('warehouses', 'warehouses.id', '=', 'warehouse_id');
        $query->join('users', 'users.id', '=', 'user_id');

        if ($request->has('filter_warehouse_id')){
            $query->where('sale_orders.warehouse_id', '=', $request->filter_warehouse_id);
        }

        if ($request->has('source') && $request->source == "warehouses"){
            if (!empty($column_arr[0]['search']['value'])){
                $query->where('sale_orders.order_number', 'like', '%'.$column_arr[0]['search']['value'].'%');
            }
            if (!empty($column_arr[1]['search']['value'])){
                $query->where('dealers.company', 'like', '%'.$column_arr[1]['search']['value'].'%');
            }
            if (!empty($column_arr[2]['search']['value'])){
                $query->where('sale_orders.status', 'like', $column_arr[2]['search']['value']);
            }
            if (!empty($column_arr[3]['search']['value'])){
                $query->where('sale_orders.blocked_at', 'like', convertDateToMysql($column_arr[3]['search']['value']));
            }
            if (!empty($column_arr[4]['search']['value'])){
                $query->where('users.name', 'like', $column_arr[4]['search']['value'].'%');
            }
        }else{
            if (!empty($column_arr[0]['search']['value'])){
                $query->where('sale_orders.order_number', 'like', '%'.$column_arr[0]['search']['value'].'%');
            }
            if (!empty($column_arr[1]['search']['value'])){
                $query->where('warehouses.name', 'like', '%'.$column_arr[1]['search']['value'].'%');
            }
            if (!empty($column_arr[2]['search']['value'])){
                $query->where('dealers.company', 'like', '%'.$column_arr[2]['search']['value'].'%');
            }
            if (!empty($column_arr[3]['search']['value'])){
                $query->where('sale_orders.blocked_at', 'like', convertDateToMysql($column_arr[3]['search']['value']));
            }
            if (!empty($column_arr[4]['search']['value'])){
                $query->where('sale_orders.due_at', 'like', convertDateToMysql($column_arr[4]['search']['value']));
            }
            if (!empty($column_arr[5]['search']['value'])){
                $query->where('sale_orders.amount', 'like', $column_arr[5]['search']['value'].'%');
            }
            if (!empty($column_arr[6]['search']['value']) && $column_arr[6]['search']['value'] != "all"){
                $query->where('sale_orders.status', 'like', $column_arr[6]['search']['value']);
            }
            if (!empty($column_arr[7]['search']['value'])){
                $query->where('users.name', 'like', $column_arr[7]['search']['value'].'%');
            }
        }
        
        if ($request->has('search')){
            $search = $request->get('search')['value'];
            $query->where( function ($q) use ($search){
                $q->where('sale_orders.order_number', 'like', '%'.$search.'%')
                    ->orWhere('sale_orders.amount', 'like', $search.'%')
                    ->orWhere('dealers.company', 'like', '%'.$search.'%')
                    ->orWhere('users.name', 'like', '%'.$search.'%');
            });    
        }

        $totalRecordswithFilter = $query->count();


        if ($length > 0)
            $query->skip($start)->take($length);

        $query->orderBy($order_column, $order_dir);
        $orders = $query->get(['sale_orders.*', 'warehouses.name', 'dealers.company', 'users.name']);

        $arr = array();
        foreach($orders as $order)
        {
            $total_amount = "";
            if (isset($order->amount)) {
                $curOrder = SaleOrder::find($order->id);
                $curOrder->calculateTotals();
                $total_amount = $curOrder->total;
            }

            $arr[] = array(
                "id" => $order->id,
                "order_number" => $order->order_number,
                "order_number_slug" => $order->order_number_slug,
                "warehouse" => $order->warehouse->name,
                "dealer" => $order->dealer->company,
                "blocked_at" => $order->display_blocked_at,
                "due_at" => $order->display_due_at,
                //(isset($order->amount)) ? trans('app.currency_symbol_inr')." ".$order->amount : "",
                "amount" => $total_amount, 
                "status" => $order->display_status,
                "user" => $order->user->display_name
            );
        }

        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $arr,
            'error' => null
        );
        return response()->json($response);
    }


    
    public function getListForReport(Request $request)
    {

        $select_period = 'period_monthly';
        if ($request->has('select_period'))
            $select_period = $request->get('select_period');

        $month = date('n');
        if ($request->has('month_id'))
            $month = $request->get('month_id');
            
        $year = date('Y');
        if ($request->has('year_id'))
            $year = $request->get('year_id');
        
        $quarter = '';
        if ($request->has('quarterly_id'))
            $quarter = $request->get('quarterly_id');

        $query = SaleOrder::query();

        $query->select('categories.name', 'products.id', 'products.part_number', 'products.model', 'products.kw_rating', DB::raw('SUM(sale_order_items.quantity_ordered) AS quantity_ordered'), DB::raw('SUM(sale_order_items.selling_price * sale_order_items.quantity_ordered) / SUM(sale_order_items.quantity_ordered) AS avg_selling_price'), 'sale_order_items.tax');

        $query->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id');
        $query->join('products', 'products.id', '=', 'sale_order_items.product_id');

        $query->join('categories', 'categories.id', '=', 'products.category_id');

        $query->where('sale_orders.status', '>=', '4');
        $query->whereNull('sale_order_items.deleted_at');

        $column_arr = $request->get('columns');

        if (!empty($column_arr[1]['search']['value']))
            $query->where('products.part_number', 'like', '%'.$column_arr[1]['search']['value'].'%');

        if (!empty($column_arr[2]['search']['value']))
            $query->where('products.model', 'like', '%'.$column_arr[2]['search']['value'].'%');

        if (!empty($column_arr[3]['search']['value']))
            $query->where('products.kw_rating', 'like', '%'.$column_arr[3]['search']['value'].'%');


        if ($select_period=='period_monthly')
        {
            $query->whereYear('sale_orders.dispatched_at', '=', $year);
            $query->whereMonth('sale_orders.dispatched_at', '=', $month);
        }
        elseif ($select_period=='period_quarterly')
        {
            if ($quarter=='Q1')
            {
                $from = date($year.'-01-01');
                $to = date($year.'-03-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
            elseif ($quarter=='Q2')
            {
                $from = date($year.'-04-01');
                $to = date($year.'-06-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
            elseif ($quarter=='Q3')
            {
                $from = date($year.'-07-01');
                $to = date($year.'-09-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
            elseif ($quarter=='Q4')
            {
                $from = date($year.'-10-01');
                $to = date($year.'-12-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
        }

        //$query->groupBy('products.id', 'sale_order_items.selling_price');
        $query->groupBy('products.id');
        $query->orderBy('categories.name','ASC');
        $query->orderBy('products.part_number', 'ASC');

        //dd($query->toSql());
        $rows = $query->get();
        //$rows = $query->toSql(); return response()->json($rows);

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0); 

        $arr = array();
        $footer = array('label'=>'Totals', 'total_quantity'=>0, 'total_taxable_value'=>0, 'total_tax_amount'=>0, 'total_amount'=>0, 'total_category'=>array());

        $current_category = '';
        $total_category_amount = 0;
        $total_category_qty = 0;

        foreach($rows as $row)
        {
            $footer['total_quantity'] += $row->quantity_ordered;
            $footer['total_taxable_value'] += ($row->avg_selling_price * $row->quantity_ordered);
            $footer['total_tax_amount'] += ($row->avg_selling_price * $row->quantity_ordered) * ($row->tax/100);
            $footer['total_amount'] += ((int)$row->avg_selling_price * (int)$row->quantity_ordered); // * (1+$row->tax/100);

            if ($current_category !== $row->name)
            {
                $total_category_amount = ((int)$row->avg_selling_price * (int)$row->quantity_ordered); // * (1+$row->tax/100);
                $total_category_qty = $row->quantity_ordered;

                $footer['total_category'][$row->name]['category'] = $row->name;
                $footer['total_category'][$row->name]['total_amount'] = $total_category_amount;
                $footer['total_category'][$row->name]['total_qty'] = $total_category_qty;

                $current_category = $row->name;
            }
            else {
                $total_category_amount += ((int)$row->avg_selling_price * (int)$row->quantity_ordered); // * (1+$row->tax/100);
                $total_category_qty += $row->quantity_ordered;
                $footer['total_category'][$row->name]['total_amount'] = $total_category_amount;
                $footer['total_category'][$row->name]['total_qty'] = $total_category_qty;
            }
            
            $price = $fmt->formatCurrency($row->avg_selling_price, "INR");
            $taxable_value = $fmt->formatCurrency(($row->avg_selling_price * $row->quantity_ordered), "INR");
            $tax_amount = $fmt->formatCurrency(($row->avg_selling_price * $row->quantity_ordered) * ($row->tax/100), "INR");
            //$amount = $fmt->formatCurrency(($row->avg_selling_price * $row->quantity_ordered * (1+$row->tax/100)), "INR");
            $amount = $fmt->formatCurrency(((int)$row->avg_selling_price * (int)$row->quantity_ordered), "INR");

            $arr[] = array(
                "id" => $row->id,
                "category" => $row->name,
                "part_number" => $row->part_number,
                "model" => $row->model,
                "kw_rating" => $row->kw_rating,
                "quantity" => $row->quantity_ordered,
                "price" => $price, //$row->selling_price,
                "taxable_value" => $taxable_value,
                "tax" => $row->tax."%",
                "tax_amount" => $tax_amount,
                "amount" => $amount
            );
        }

        $footer['total_taxable_value'] = $fmt->formatCurrency($footer['total_taxable_value'], "INR");
        $footer['total_tax_amount'] = $fmt->formatCurrency($footer['total_tax_amount'], "INR");
        $footer['total_amount'] = $fmt->formatCurrency($footer['total_amount'], "INR");
        
        //$ret = array("data"=>$arr, "footer"=>'TOTALS');
                
        $response = array(
            //"draw" => $draw,
            //"recordsTotal" => $totalRecords,
            //"recordsFiltered" => $totalRecordswithFilter,
            "data" => array("data"=>$arr, "footer"=>$footer), //$arr,

            "error" => null
        );
        return response()->json($response);
    }


    public function getListForDealerReport(Request $request)
    {
        $dealer_id = 0;
        if ($request->has('dealer_id'))
            $dealer_id = $request->get('dealer_id');

        /**
         * if the dealer is not set, then force category-wise listing
         */
        if ($dealer_id !== null)
        {
            $select_format = 'format_datewise';
            if ($request->has('select_format'))
                $select_format = $request->get('select_format');
        }
        else {
            $dealer_id = 0;
            $select_format = 'format_category';
        }

        $select_period = 'period_monthly';
        if ($request->has('select_period'))
            $select_period = $request->get('select_period');

        $month = date('n');
        if ($request->has('month_id'))
            $month = $request->get('month_id');
            
        $year = date('Y');
        if ($request->has('year_id'))
            $year = $request->get('year_id');
        
        $quarter = '';
        if ($request->has('quarterly_id'))
            $quarter = $request->get('quarterly_id');

        /*
            SELECT so.dispatched_at, so.order_number_slug, p.part_number, soi.quantity_ordered, soi.selling_price, soi.tax
            FROM `sale_orders` so
            INNER JOIN `sale_order_items` soi ON (soi.sale_order_id = so.id)
            INNER JOIN `products` p ON (p.id = soi.product_id)
            WHERE so.status >= 4 AND (so.dealer_id = 11) AND (
            YEAR(`dispatched_at`)=2022 AND MONTH(`dispatched_at`) BETWEEN 10 AND 11)

            select  `categories`.`name`, `products`.`part_number`, SUM(`sale_order_items`.`quantity_ordered`) AS quantity, `sale_order_items`.`selling_price`, `sale_order_items`.`tax`
            from `sale_orders` 
            inner join `sale_order_items` on `sale_order_items`.`sale_order_id` = `sale_orders`.`id` 
            inner join `products` on `products`.`id` = `sale_order_items`.`product_id`
            inner join `categories` on `categories`.`id` = `products`.`category_id`
            where `sale_orders`.`status` >= 4 and `sale_orders`.`dealer_id` = 345 and year(`sale_orders`.`dispatched_at`) = 2022 and month(`sale_orders`.`dispatched_at`) = 11
            and `sale_orders`.`deleted_at` is null 
            group by `products`.`id`
            order by `categories`.`name` asc

        */

        $query = SaleOrder::query();

        if ($select_format=='format_datewise')
            $query->select('sale_orders.dispatched_at', 'sale_orders.order_number_slug', 'products.part_number', 'products.model', 'products.kw_rating', 'sale_order_items.quantity_ordered', 'sale_order_items.selling_price', 'sale_order_items.tax');
        else
            $query->select('categories.name', 'products.part_number', 'products.model', 'products.kw_rating', DB::raw('SUM(sale_order_items.quantity_ordered) AS quantity_ordered'), DB::raw('SUM(sale_order_items.selling_price * sale_order_items.quantity_ordered) / SUM(sale_order_items.quantity_ordered) AS selling_price'), 'sale_order_items.tax');

        $query->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id');
        $query->join('products', 'products.id', '=', 'sale_order_items.product_id');

        if ($select_format=='format_category')
            $query->join('categories', 'categories.id', '=', 'products.category_id');

        $query->where('sale_orders.status', '>=', '4');
        $query->whereNull('sale_order_items.deleted_at');

        if ($dealer_id !== 0)
            $query->where('sale_orders.dealer_id', '=', $dealer_id);

        $column_arr = $request->get('columns');

        if ($select_format=='format_datewise')
        {
            if (!empty($column_arr[2]['search']['value']))
                $query->where('products.part_number', 'like', '%'.$column_arr[2]['search']['value'].'%');

            if (!empty($column_arr[3]['search']['value']))
                $query->where('products.model', 'like', '%'.$column_arr[3]['search']['value'].'%');

            if (!empty($column_arr[4]['search']['value']))
                $query->where('products.kw_rating', 'like', '%'.$column_arr[4]['search']['value'].'%');

        }
        elseif ($select_format=='format_category')
        {
            if (!empty($column_arr[1]['search']['value']))
                $query->where('products.part_number', 'like', '%'.$column_arr[1]['search']['value'].'%');

            if (!empty($column_arr[2]['search']['value']))
                $query->where('products.model', 'like', '%'.$column_arr[2]['search']['value'].'%');

            if (!empty($column_arr[3]['search']['value']))
                $query->where('products.kw_rating', 'like', '%'.$column_arr[3]['search']['value'].'%');

        }

        if ($select_period=='period_monthly')
        {
            $query->whereYear('sale_orders.dispatched_at', '=', $year);
            $query->whereMonth('sale_orders.dispatched_at', '=', $month);
        }
        elseif ($select_period=='period_quarterly')
        {
            if ($quarter=='Q1')
            {
                $from = date($year.'-01-01');
                $to = date($year.'-03-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
            elseif ($quarter=='Q2')
            {
                $from = date($year.'-04-01');
                $to = date($year.'-06-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
            elseif ($quarter=='Q3')
            {
                $from = date($year.'-07-01');
                $to = date($year.'-09-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
            elseif ($quarter=='Q4')
            {
                $from = date($year.'-10-01');
                $to = date($year.'-12-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
        }

        if ($select_format=='format_datewise')
        {
            $query->orderBy('sale_orders.dispatched_at','ASC');
            $query->orderBy('sale_orders.order_number_slug','ASC');
        }
        else
        {
            //$query->groupBy('products.id', 'sale_order_items.selling_price');
            $query->groupBy('products.id');
            if ($dealer_id !== 0) {
                $query->orderBy('categories.name','ASC');
                $query->orderBy('products.part_number', 'ASC');
            }
            else
                $query->orderBy('sale_order_items.quantity_ordered', 'DESC');
        }

        $rows = $query->get();
        //$rows = $query->toSql(); return response()->json($rows);

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, ''); 
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $arr = array();
        $footer = array('label'=>'Totals', 'total_quantity'=>0, 'total_taxable_value'=>0, 'total_tax_amount'=>0, 'total_amount'=>0);

        foreach($rows as $row)
        {
            $footer['total_quantity'] += $row->quantity_ordered;
            $footer['total_taxable_value'] += ($row->selling_price * $row->quantity_ordered);
            $footer['total_tax_amount'] += ($row->selling_price * $row->quantity_ordered) * ($row->tax/100);
            $footer['total_amount'] += ((int)$row->selling_price * (int)$row->quantity_ordered);
            
            $price = $fmt->formatCurrency($row->selling_price, "INR");
            $taxable_value = $fmt->formatCurrency(($row->selling_price * $row->quantity_ordered), "INR");
            $tax_amount = $fmt->formatCurrency(($row->selling_price * $row->quantity_ordered) * ($row->tax/100), "INR");
            //$amount = $fmt->formatCurrency(($row->selling_price * $row->quantity_ordered * (1+$row->tax/100)), "INR");
            $amount = $fmt->formatCurrency(((int)$row->selling_price * (int)$row->quantity_ordered), "INR");

            if ($select_format=='format_datewise')
                $arr[] = array(
                    "invoice_date" => $row->display_dispatched_at,
                    "invoice_number" => $row->order_number_slug,
                    "part_number" => $row->part_number,
                    "model" => $row->model,
                    "kw_rating" => $row->kw_rating,
                    "quantity" => $row->quantity_ordered,
                    "price" => $price, //$row->selling_price,
                    "taxable_value" => $taxable_value,
                    "tax" => $row->tax."%",
                    "tax_amount" => $tax_amount,
                    "amount" => $amount
                );
            else
                $arr[] = array(
                    "category" => $row->name,
                    "part_number" => $row->part_number,
                    "model" => $row->model,
                    "kw_rating" => $row->kw_rating,
                    "quantity" => $row->quantity_ordered,
                    "price" => $price, //$row->selling_price,
                    "taxable_value" => $taxable_value,
                    "tax" => $row->tax."%",
                    "tax_amount" => $tax_amount,
                    "amount" => $amount
                );

        }

        $footer['total_taxable_value'] = $fmt->formatCurrency($footer['total_taxable_value'], "INR");
        $footer['total_tax_amount'] = $fmt->formatCurrency($footer['total_tax_amount'], "INR");
        $footer['total_amount'] = $fmt->formatCurrency($footer['total_amount'], "INR");
        
        //$ret = array("data"=>$arr, "footer"=>'TOTALS');
                
        $response = array(
            //"draw" => $draw,
            //"recordsTotal" => $totalRecords,
            //"recordsFiltered" => $totalRecordswithFilter,
            "data" => array("data"=>$arr, "footer"=>$footer), //$arr,

            "error" => null
        );
        return response()->json($response);

    }


    public function getListForStateReport(Request $request)
    {
        $state_id = 0;
        if ($request->has('state_id'))
            $state_id = $request->get('state_id');

        $select_period = 'period_monthly';
        if ($request->has('select_period'))
            $select_period = $request->get('select_period');

        $month = date('n');
        if ($request->has('month_id'))
            $month = $request->get('month_id');
            
        $year = date('Y');
        if ($request->has('year_id'))
            $year = $request->get('year_id');
        
        $quarter = '';
        if ($request->has('quarterly_id'))
            $quarter = $request->get('quarterly_id');

        /*
            select `states`.`name`, `categories`.`name`, `products`.`part_number`, `products`.`model`, `products`.`kw_rating`, SUM(sale_order_items.quantity_ordered) AS quantity_ordered 
            from `sale_orders` 
            inner join `sale_order_items` on `sale_order_items`.`sale_order_id` = `sale_orders`.`id` 
            inner join `products` on `products`.`id` = `sale_order_items`.`product_id` 
            inner join `categories` on `categories`.`id` = `products`.`category_id`
            inner join `dealers` on `dealers`.`id` = `sale_orders`.`dealer_id`
            inner join `states` on `states`.`id` = `dealers`.`state_id`
            where `sale_orders`.`status` >= 4 and `dealers`.`state_id` = 19 and `sale_orders`.`dispatched_at` between '2023-01-01' and '2023-02-28' 
                and `sale_orders`.`deleted_at` is null 
                and `sale_order_items`.`deleted_at` is null
            group by `products`.`id`
            order by `categories`.`name` asc;
        */

        $query = SaleOrder::query();

        $query->select('states.name AS state_name', 'categories.name AS category_name', 'products.part_number', 'products.model', 'products.kw_rating', DB::raw('SUM(sale_order_items.quantity_ordered) AS quantity_ordered'), DB::raw('SUM(sale_order_items.selling_price * sale_order_items.quantity_ordered) / SUM(sale_order_items.quantity_ordered) AS selling_price'), 'sale_order_items.tax');

        $query->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id');
        $query->join('products', 'products.id', '=', 'sale_order_items.product_id');
        $query->join('categories', 'categories.id', '=', 'products.category_id');
        $query->join('dealers', 'dealers.id', '=', 'sale_orders.dealer_id');
        $query->join('states', 'states.id', '=', 'dealers.state_id');

        $query->where('sale_orders.status', '>=', '4');
        $query->where('dealers.state_id', '=', $state_id);
        $query->whereNull('sale_order_items.deleted_at');

        $column_arr = $request->get('columns');

        if (!empty($column_arr[2]['search']['value']))
            $query->where('products.part_number', 'like', '%'.$column_arr[2]['search']['value'].'%');

        if (!empty($column_arr[3]['search']['value']))
            $query->where('products.model', 'like', '%'.$column_arr[3]['search']['value'].'%');

        if (!empty($column_arr[4]['search']['value']))
            $query->where('products.kw_rating', 'like', '%'.$column_arr[4]['search']['value'].'%');


        if ($select_period=='period_monthly')
        {
            $query->whereYear('sale_orders.dispatched_at', '=', $year);
            $query->whereMonth('sale_orders.dispatched_at', '=', $month);
        }
        elseif ($select_period=='period_quarterly')
        {
            if ($quarter=='Q1')
            {
                $from = date($year.'-01-01');
                $to = date($year.'-03-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
            elseif ($quarter=='Q2')
            {
                $from = date($year.'-04-01');
                $to = date($year.'-06-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
            elseif ($quarter=='Q3')
            {
                $from = date($year.'-07-01');
                $to = date($year.'-09-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
            elseif ($quarter=='Q4')
            {
                $from = date($year.'-10-01');
                $to = date($year.'-12-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
        }

        //$query->groupBy('products.id', 'sale_order_items.selling_price');
        $query->groupBy('products.id');
        $query->orderBy('categories.name','ASC');
        //$query->orderBy('sale_order_items.quantity_ordered', 'DESC');
        $query->orderBy('products.part_number', 'ASC');

        $rows = $query->get();
        //$rows = $query->toSql(); return response()->json($rows);

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0); 

        $arr = array();
        $footer = array('label'=>'Totals', 'total_quantity'=>0, 'total_taxable_value'=>0, 'total_tax_amount'=>0, 'total_amount'=>0);

        foreach($rows as $row)
        {
            $footer['total_quantity'] += $row->quantity_ordered;
            $footer['total_taxable_value'] += ($row->selling_price * $row->quantity_ordered);
            $footer['total_tax_amount'] += ($row->selling_price * $row->quantity_ordered) * ($row->tax/100);
            $footer['total_amount'] += ((int)$row->selling_price * (int)$row->quantity_ordered) * (1+$row->tax/100);
            
            $price = $fmt->formatCurrency($row->selling_price, "INR");
            $taxable_value = $fmt->formatCurrency(($row->selling_price * $row->quantity_ordered), "INR");
            $tax_amount = $fmt->formatCurrency(($row->selling_price * $row->quantity_ordered) * ($row->tax/100), "INR");
            //$amount = $fmt->formatCurrency(((int)$row->selling_price * (int)$row->quantity_ordered * (1+$row->tax/100)), "INR");
            $amount = $fmt->formatCurrency(((int)$row->selling_price * (int)$row->quantity_ordered), "INR");

            $arr[] = array(
                "state" => $row->state_name,
                "category" => $row->category_name,
                "part_number" => $row->part_number,
                "model" => $row->model,
                "kw_rating" => $row->kw_rating,
                "quantity" => $row->quantity_ordered,
                "price" => $price, //$row->selling_price,
                "taxable_value" => $taxable_value,
                "tax" => $row->tax."%",
                "tax_amount" => $tax_amount,
                "amount" => $amount
            );
        }

        $footer['total_taxable_value'] = $fmt->formatCurrency($footer['total_taxable_value'], "INR");
        $footer['total_tax_amount'] = $fmt->formatCurrency($footer['total_tax_amount'], "INR");
        $footer['total_amount'] = $fmt->formatCurrency($footer['total_amount'], "INR");
        
        //$ret = array("data"=>$arr, "footer"=>'TOTALS');
                
        $response = array(
            //"draw" => $draw,
            //"recordsTotal" => $totalRecords,
            //"recordsFiltered" => $totalRecordswithFilter,
            "data" => array("data"=>$arr, "footer"=>$footer), //$arr,

            "error" => null
        );
        return response()->json($response);
    }

    public function getSalesTotals(Request $request)
    {
        /**
         * period_monthly or period_quarterly
         */
        $period = 'period_monthly';
        if ($request->has('period'))
            $period = $request->get('period');

        $year = date('Y');
        if ($request->has('year'))
            $year = $request->get('year');

        $month = date('n');
        if ($request->has('month'))
        {
            $month = $request->get('month');
            if (empty($month))
                $month = date('n');
        }

        $quarter = 'Q1';
        if ($request->has('quarter'))
        {
            $quarter = $request->get('quarter');

            if (empty($quarter))
                $quarter = 'Q1';
        }

        $category = "_ALL";
        if ($request->has('category'))
        {
            $category = $request->get('category');
            if (empty($category))
                $category = "_ALL";
        }
        
        $sale_order = new SaleOrder();
        $res = $sale_order->calculateSalesTotals($period, $year, $month, $quarter, $category);
        //$sale_order_totals = $sale_order->calculateSalesTotals("period_quarterly",$cur_year);

        return view('sale_orders.dashboard-totals-table', ['period'=> $period, 'type' => 'category', 'totals' => $res]);
    }

    public function getStateSalesTotals(Request $request)
    {
        /**
         * period_monthly or period_quarterly
         */
        $period = 'period_monthly';
        if ($request->has('period'))
            $period = $request->get('period');

        $year = date('Y');
        if ($request->has('year'))
            $year = $request->get('year');

        $month = date('n');
        if ($request->has('month'))
        {
            $month = $request->get('month');
            if (empty($month))
                $month = date('n');
        }

        $quarter = 'Q1';
        if ($request->has('quarter'))
        {
            $quarter = $request->get('quarter');
            if (empty($quarter))
                $quarter = 'Q1';
        }

        $state = "_ALL";
        if ($request->has('state'))
        {
            $state = $request->get('state');
            if (empty($state))
                $state = "_ALL";
        }
        
        $sale_order = new SaleOrder();
        $res = $sale_order->calculateStateSalesTotals($period, $year, $month, $quarter, $state);
        return view('sale_orders.dashboard-totals-table', ['period'=> $period, 'type' => 'states', 'totals' => $res]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->can('edit sale orders')){
            $order = new SaleOrder();

            $order_number_count = \Setting::get('sale_order.order_number') +1;
            $order_number = \Setting::get('sale_order.prefix').$order_number_count.\Setting::get('sale_order.suffix');

            return view('sale_orders.form', ['order' => $order, 'order_number' => $order_number, 'order_number_count' => $order_number_count ]);
        }
        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreSaleOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSaleOrderRequest $request)
    {
        $validatedData = $request->validated();

        $order = SaleOrder::create($validatedData);

        if ($order) {
        
            /**
             * retrieve and store the "shipped to" address from the dealer
             */
            $dealer = Dealer::find($order->dealer_id);
            
            if ($dealer){
                if (!$dealer->has_shipping_address){
                    $order->shipping_company = $dealer->shipping_company;
                    $order->shipping_gstin = $dealer->shipping_gstin;
                    $order->shipping_contact_person =  $dealer->shipping_contact_person;
                    $order->shipping_phone =  $dealer->shipping_phone;
                    $order->shipping_address = $dealer->shipping_address;
                    $order->shipping_address2 = $dealer->shipping_address2;
                    $order->shipping_city = $dealer->shipping_city;
                    $order->shipping_zip_code = $dealer->shipping_zip_code;
                    $order->shipping_state_id = $dealer->shipping_state_id;
                }
                else {
                    $order->shipping_company = $dealer->company;
                    $order->shipping_gstin = $dealer->gstin;
                    $order->shipping_contact_person = $dealer->contact_person;
                    $order->shipping_phone =  $dealer->phone;
                    $order->shipping_address = $dealer->address;
                    $order->shipping_address2 = $dealer->address2;
                    $order->shipping_city = $dealer->city;
                    $order->shipping_zip_code = $dealer->zip_code;
                    $order->shipping_state_id = $dealer->state_id;
                }

                $order->payment_terms = \Setting::get('sale_order.terms');

                $order->update();
            }

            $order_number_count = \Setting::get('sale_order.order_number') +1;
            \Setting::set('sale_order.order_number', $order_number_count);
            \Setting::save();

            activity()
               ->performedOn($order)
               ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
               ->log('Created Proforma Invoice');

            return redirect(route('sale-orders.cart', $order->order_number_slug)); 
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'sale order']));        
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $order_number
     * @return \Illuminate\Http\Response
     */
    public function cart($order_number_slug)
    {
        $order = SaleOrder::where('order_number_slug', '=', $order_number_slug)->first();
        if ($order){
            if ($order->status == SaleOrder::DRAFT)
                return view('sale_orders.cart', ['order' => $order ]);

            return redirect(route('sale-orders.show', $order->order_number_slug)); 
        }
        
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $order_number
     * @return \Illuminate\Http\Response
     */
    public function show($order_number_slug)
    {
        $user = Auth::user();
        if ($user->can('view sale orders')){
            $order = SaleOrder::with('state')->where('order_number_slug', '=', $order_number_slug)->first();
            $order->calculateTotals();

            $activities = Activity::where('subject_id', $order->id)
                //->where('properties->order_number', $order->order_number)
                //->where('subject_type', 'App\Models\SaleOrder')
                //->where('subject_type', 'App\Models\SaleOrderPayment')
                ->orderBy('updated_at', 'desc')
                ->get();

            if ($order)
                return view('sale_orders.show', ['order' => $order , 'activities' => $activities]);

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'sale order']));
        }
        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SaleOrder  $saleOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(SaleOrder $saleOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->get('field') == "order_number"){
            $hasOrderNumber = SaleOrder::where('order_number', 'LIKE', $request->get('order_number'))->count();
            if ($hasOrderNumber){
                return response()->json([
                    'success' => 'false',
                    'errors'  => "This order number is already used by another order",
                ], 409);
            }
            $order = SaleOrder::find($id);
            $order->order_number = $request->get('order_number');

            activity()
               ->performedOn($order)
               ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
               ->log('Proforma Invoice updated to '.$order->order_number);


            $order->update();
            return response()->json(['success'=>'true','code'=>200, 'message'=> 'OK', 'field' => $request->get('field')]);
        }

        if (($request->get('field') == "amount") || ($request->get('field') == "quantity"))
        {
            $order = SaleOrder::find($id);
            $items = SaleOrderItem::where('sale_order_id', "=", $id)->get();
            $order->amount = 0;
            foreach($items as $item){
                $order->amount += $item->total_price;
            }

            /*
            activity()
               ->performedOn($order)
               ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
               ->log('Updated Sale Order Amount to '.$order->amount);
            */

            /*
                set transport_charges to calculated field 'freight_charges'
            */
            $order->calculateTotals();
            $order->transport_charges = $order->freight_charges; 

            $order->update();

            return response()->json(['success'=>'true','code'=>200, 'message'=>'OK', 'field'=>$request->get('field'), 'freight_charges'=>$order->freight_charges, 'transport_charges'=>$order->transport_total, 'total_cost'=>$order->total]);
        }

        if ($request->get('field') == "transport_charges"){
            $order = SaleOrder::find($id);
            $items = SaleOrderItem::where('sale_order_id', "=", $id)->get();
            $order->transport_charges = $request->get('value');

            activity()
               ->performedOn($order)
               ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
               ->log('Transport Charges updated to '.number_format($order->transport_charges,2));

            $order->update();

            $order->calculateTotals();

            return response()->json(['success'=>'true', 'total'=>$order->total, 'tax_total'=>$order->tax_total, 'freight_charges'=>$order->freight_charges, 'transport_charges'=>$order->transport_total, 'code'=>200, 'message'=> 'OK', 'field' => $request->get('field')]);
        }

        if ($request->get('field') == 'payment_terms')
        {
            $order = SaleOrder::find($id);
            $order->payment_terms = $request->get('value');
            $order->update();
        }

        if (str_starts_with($request->get('field'), 'shipping_') == "shipping_")
        {
            $order = SaleOrder::find($id);

            $field_name = $request->get('field');
            $order->$field_name = $request->get('value');
            $order->update();
        }        

    }


        /**
     * Update the blocked_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //              blocked     this function should eventually be renamed "blocked"
    public function blocked(Request $request, $id)
    {
        $validated = $request->validate([
            'blocked_at' => 'required|date'
        ]);

        $order = SaleOrder::find($id);
        $order->blocked_at = $request->get('blocked_at');
        $order->status = SaleOrder::BLOCKED;
        $items = SaleOrderItem::where('sale_order_id', "=", $id)->select('quantity_ordered', 'selling_price', 'tax')->get();
        $order->amount = 0;
        foreach($items as $item){
            $order->amount += $item->total_price; 
        }
    
        activity()
           ->performedOn($order)
           ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
           ->log('Status updated to <b>Blocked</b>');

        /*
            set transport_charges to calculated field 'freight_charges'
        */
        $order->calculateTotals();
        $order->transport_charges = $order->freight_charges; 

        $order->update();
 

        /*
            - trigger stock out, update Blocked Stock (add) 
        */  
        $inventory = new Inventory();
        $inventory->updateStock($order);

        return redirect(route('sale-orders.show', $order->order_number_slug))->with('success', 'order blocked'); 
    }


     /**
     * Update the booked_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     //             booked      this function should eventually be renamed "booked"
    public function booked(Request $request, $id)
    {
        $validated = $request->validate([
            'booked_at' => 'required|date'
        ]);
        $order = SaleOrder::with('items')->find($id);
        $order->booked_at = $request->get('booked_at');
        $order->status = SaleOrder::BOOKED;

        activity()
           ->performedOn($order)
           ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
           ->log('Status updated to <b>Booked</b>');

        $order->update();

        $inventory = new Inventory();
        $inventory->updateStock($order);
        
        return redirect(route('sale-orders.show', $order->order_number_slug))->with('success', 'order booked'); 
    }


    /**
     * Update the dispatched_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //              dispatched      this function should eventually be renamed "dispatched"
    public function dispatched(Request $request, $id)
    {
        $validated = $request->validate([
            'dispatched_at' => 'required|date',
            // 'due_at' => 'required|date',
            // 'tracking_number' => 'required',
            'courier' => 'required',
            //'transport_charges' => 'required'
        ]);
        $order = SaleOrder::find($id);

        $check = $order->canDispatch();
        //$check = json_decode($order->canDispatch());
        // $res = implode(" ",$check);
        // \Debugbar::info(">>".$res);
        
        if ($check['success'] == 1) {

            $order->dispatched_at = $request->get('dispatched_at');
            $order->due_at = $request->get('due_at');
            $order->tracking_number = $request->get('tracking_number');
            $order->courier = $request->get('courier');
            //$order->transport_charges = $request->get('transport_charges');
            $order->status = SaleOrder::DISPATCHED;

            activity()
               ->performedOn($order)
               ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
               ->log('Status updated to <b>Dispatched</b>');

            $order->update();

            $inventory = new Inventory();
            $inventory->updateStock($order);

            return redirect(route('sale-orders.show', $order->order_number_slug))->with('success', 'order dispatched'); 
        }
        return back()->withErrors([trans('error.inventory_insufficient_stock', ['field' => $check['item']])]);
    }


    /**
     * Update the delivered_at and status of an order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delivered(Request $request, $id)
    {
        $order = SaleOrder::find($id);
        $order->delivered_at = $request->get('delivered_at');
        $order->status = SaleOrder::DELIVERED;
        $order->update();

        return redirect(route('sale-orders.show', $order->order_number_slug))->with('success', 'order delivered'); 
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->can('delete sale orders')){
            $order = SaleOrder::find($id);

            $inventory = new Inventory();
            $inventory->deleteStock($order);

            activity()
               ->performedOn($order)
               ->withProperties(['order_number' => $order->order_number, 'status' => $order->status])
               ->log('Sale Order deleted');

            $order->items()->delete();
            $order->delete();

            if($request->ajax())
                return response()->json(['deleted successfully '.$order->order_number_slug]);
            else
                return redirect(route('sale-orders'))->with('success', trans('app.record_deleted', ['field' => 'Sale Order']));
        }
        return abort(403, trans('error.unauthorized'));
    }


    public function proforma($order_number_slug)
    {
        $settings = \Setting::all();

        $order = SaleOrder::where('order_number_slug', '=', $order_number_slug)->first();
        $order->calculateTotals();
        
        return view('sale_orders.view_proforma', ['order' => $order, 'settings' => $settings]);
    }


    public function exportProformaToPdf($order_number_slug)
    {
        $settings = \Setting::all();

        $order = SaleOrder::where('order_number_slug', '=', $order_number_slug)->first();
        $order->calculateTotals();
        view()->share('order', $order);
        view()->share('settings', $settings);
        $pdf = PDF::loadView('sale_orders.proforma',  ['order'=> $order]);

        // download PDF file with download method
        return $pdf->download($order_number_slug.' - '.$order->dealer->company.'.pdf');
    }

    public function report()
    {
        return view('sale_orders.report');
    }

    public function dealerReport()
    {
        //$dealer = Dealer::all();

        return view('sale_orders.dealer-report');//, ['dealer' => $dealer ]);
    }

    public function stateReport()
    {
        return view('sale_orders.state-report');   
    }
}
