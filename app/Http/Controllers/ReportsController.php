<?php

namespace App\Http\Controllers;

use NumberFormatter;
use App\Models\SaleOrder;
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

    public function salesProductTotals(Request $request)
    {
        /*
            final code 
        */
        return view('reports.sales-product-totals');
    }


    /*
        for ajax call from blade file
        url     : "{{ route('ajax.sales-product-totals') }}", 

    */
    public function getSalesProductTotals(Request $request)
    {

        $res = array();

        $draw = 1;
        if ($request->has('draw'))
            $draw = $request->get('draw');

        $product="_ALL";
        if ($request->has('search')){
            $value = $request->get('search')['value'];
            if (!is_null($value))
                $product = $value;
        }

        $limit = 0;
        if ($request->has('limit'))
        {
            $limit = $request->get('limit');
        }

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
        $res = $sale_order->calculateProductSalesTotals('period_monthly', $year, "_ALL", "_ALL", $product);
        //dd($res);

        /*
            iterate through array and sort by part_number=>months
        */
        $data = array();
        foreach ($res as $month=>$product_array)
        {
            foreach ($product_array as $product)
                $data[ $product['part_number'] ][ $month ] = $product['amount_sold'];
        }

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $arr = array();
        foreach ($data as $part_number=>$month)
        {
            $total = 0;

            if (array_key_exists(1, $month))
                $total = (float)$month[1];
            if (array_key_exists(2, $month))
                $total += (float)$month[2];
            if (array_key_exists(3, $month))
                $total += (float)$month[3];
            if (array_key_exists(4, $month))
                $total += (float)$month[4];
            if (array_key_exists(5, $month))
                $total += (float)$month[5];
            if (array_key_exists(6, $month))
                $total += (float)$month[6];
            if (array_key_exists(7, $month))
                $total += (float)$month[7];
            if (array_key_exists(8, $month))
                $total += (float)$month[8];
            if (array_key_exists(9, $month))
                $total += (float)$month[9];
            if (array_key_exists(10, $month))
                $total += (float)$month[10];
            if (array_key_exists(11, $month))
                $total += (float)$month[11];
            if (array_key_exists(12, $month))
                $total += (float)$month[12];
            
            $arr[] = array(
                "part_number" => $part_number,
                "jan" => (array_key_exists(1, $month) ? $fmt->formatCurrency($month[1], "INR") : 0),
                "feb" => (array_key_exists(2, $month) ? $fmt->formatCurrency($month[2], "INR") : 0),
                "mar" => (array_key_exists(3, $month) ? $fmt->formatCurrency($month[3], "INR") : 0),
                "apr" => (array_key_exists(4, $month) ? $fmt->formatCurrency($month[4], "INR") : 0), 
                "may" => (array_key_exists(5, $month) ? $fmt->formatCurrency($month[5], "INR") : 0),
                "jun" => (array_key_exists(6, $month) ? $fmt->formatCurrency($month[6], "INR") : 0),
                "jul" => (array_key_exists(7, $month) ? $fmt->formatCurrency($month[7], "INR") : 0),
                "aug" => (array_key_exists(8, $month) ? $fmt->formatCurrency($month[8], "INR") : 0),
                "sep" => (array_key_exists(9, $month) ? $fmt->formatCurrency($month[9], "INR") : 0),
                "oct" => (array_key_exists(10, $month) ? $fmt->formatCurrency($month[10], "INR") : 0),
                "nov" => (array_key_exists(11, $month) ? $fmt->formatCurrency($month[11], "INR") : 0),
                "dec" => (array_key_exists(12, $month) ? $fmt->formatCurrency($month[12], "INR") : 0),
                "total" => $total
            );
        }

        /*
            sort, descending, on the total column
            for DESC sorting, you reverse the position of the a and the b
        */
        usort($arr, function($a, $b) {
            //return $a['total'] <=> $b['total'];
            return $b['total'] <=> $a['total'];
        });

        /*
            if limit is set, slice the array
        */
        if ($limit > 0)
            $arr = array_slice($arr, 0, $limit);

        /*
            format the total column
        */
        for ($i=0;$i<count($arr);$i++)
            $arr[$i]["total"] = $fmt->formatCurrency($arr[$i]['total'],"INR");

        $totalRecords = count($arr);
        $totalRecordswithFilter = count($arr);
        
        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $arr,
            'error' => null
        );

        return response()->json($response);
    }

}
