<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;

class ReportsController extends Controller
{
    //
    public function index(): View
    {
        return view('reports.stock');
    }

    public function closingStock(): View
    {
        return view('reports.closing-stock');
    }

    public function dstr(): View
    {
        return view('reports.dstr');
    }

    public function salesProductTotals(?string $report_format = 'quantity')
    {
        $curQuarter = getCurrentQuarter();

        if ($report_format == 'sales') {
            $user = Auth::user();
            if ($user->hasRole('super-admin')) {
                return view('reports.sales-product-totals', ['report_format' => 'sales', 'curQuarter' => $curQuarter]);
            }
        } elseif ($report_format == 'quantity') {
            return view('reports.sales-product-totals', ['report_format' => 'quantity', 'curQuarter' => $curQuarter]);
        }

        return abort(403, trans('error.unauthorized'));
    }

    /*
        for ajax call from blade file
        url     : "{{ route('ajax.dstr') }}",

    */
    public function getDstr(Request $request)
    {
        $arr = [];
        $columns = [];

        $month = date('n');
        if ($request->has('month_id')) {
            $month = $request->get('month_id');
        }

        $year = date('Y');
        if ($request->has('year_id')) {
            $year = $request->get('year_id');
        }

        $query = SaleOrderItem::with('sale_order')
            ->join('sale_orders', 'sale_orders.id', '=', 'sale_order_id')
            ->join('users', 'users.id', '=', 'sale_orders.user_id')
            ->join('warehouses', 'warehouses.id', '=', 'sale_orders.warehouse_id')
            ->join('dealers', 'dealers.id', '=', 'sale_orders.dealer_id')
            ->join('states', 'states.id', '=', 'dealers.state_id')
            ->join('products', 'products.id', '=', 'sale_order_items.product_id');
        // ->where('product_id', '=', $filter_product_id);

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $filter_from = date($year.'-'.$month.'-01');
        $filter_to = date($year.'-'.$month.'-'.$days);

        $query->whereBetween('sale_orders.dispatched_at', [$filter_from, $filter_to]);
        $query->orderBy('sale_orders.dispatched_at');

        $res = $query->get([
            'quantity_ordered',
            'sale_orders.dispatched_at',
            'dealers.company', 'dealers.city', 'dealers.zip_code', 'dealers.contact_person', 'dealers.email', 'dealers.email2', 'dealers.email3',
            'products.part_number', 'products.notes', 'products.kw_rating',
            'states.name',
        ]);

        $i = 0;
        foreach ($res as $key => $row) {
            $arr[$i]['date'] = Carbon::parse($row->dispatched_at)->toFormattedDateString();
            $arr[$i]['customer'] = $row->company;
            $arr[$i]['location'] = $row->city.' '.$row->zip_code.'<br>'.$row->name;
            $arr[$i]['part_number'] = $row->part_number;
            $arr[$i]['quantity'] = $row->quantity_ordered;
            $arr[$i]['capacity'] = $row->kw_rating;
            $arr[$i]['contact'] = $row->contact_person;
            $arr[$i]['email'] = $row->email.'<br>'.$row->email2.'<br>'.$row->email3;
            $arr[$i]['remarks'] = $row->notes;

            $i++;
        }

        $columns[0]['data'] = 'date';
        $columns[0]['title'] = 'Date';
        $columns[1]['data'] = 'customer';
        $columns[1]['title'] = 'Customer';
        $columns[2]['data'] = 'location';
        $columns[2]['title'] = 'Delivery Location';
        $columns[3]['data'] = 'part_number';
        $columns[3]['title'] = 'Part Number';
        $columns[4]['data'] = 'quantity';
        $columns[4]['title'] = 'Quantity';
        $columns[4]['className'] = 'text-end';
        $columns[5]['data'] = 'capacity';
        $columns[5]['title'] = 'Capacity';
        $columns[6]['data'] = 'contact';
        $columns[6]['title'] = 'Contact';
        $columns[7]['data'] = 'email';
        $columns[7]['title'] = 'Email';
        $columns[8]['data'] = 'remarks';
        $columns[8]['title'] = 'Remarks';

        $response = ['data' => $arr, 'columns' => $columns, 'footer' => 'TOTALS'];

        //return response()->json($response);
        return json_encode($response);
    }

    /*
        for ajax call from blade file
        url     : "{{ route('ajax.sales-product-totals') }}",

    */
    public function getSalesProductTotals(Request $request)
    {

        $month_short = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        $res = [];

        $draw = 1;
        if ($request->has('draw')) {
            $draw = $request->get('draw');
        }

        $product = '_ALL';
        if ($request->has('search')) {
            $value = $request->get('search')['value'];
            if (! is_null($value)) {
                $product = $value;
            }
        }

        $limit = 0;
        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }

        /**
         * period_monthly or period_quarterly
         */
        $report_format = 'Sales';
        if ($request->has('report_format')) {
            $report_format = $request->get('report_format');
        }

        $select_period = 'period_monthly';
        if ($request->has('select_period')) {
            $select_period = $request->get('select_period');
        }

        $month = date('n');
        if ($request->has('month_id')) {
            $month = $request->get('month_id');
        }

        $year = date('Y');
        if ($request->has('year_id')) {
            $year = $request->get('year_id');
        }

        $quarter = '_ALL';
        if ($request->has('quarterly_id')) {
            $quarter = $request->get('quarterly_id');
        }

        $category = '_ALL';
        if ($request->has('category')) {
            $category = $request->get('category');
            if (empty($category)) {
                $category = '_ALL';
            }
        }

        //dd($select_period, $year, $month, $quarter, $product);
        $sale_order = new SaleOrder;
        $res = $sale_order->calculateProductSalesTotals($select_period, $year, $month, $quarter, $product, 0);
        //dd("res", $res);

        /**
         * iterate through array and sort by part_number => months
         * or part_number => quarters
         */
        $data = [];
        foreach ($res as $month => $product_array) {
            // foreach ($product_array as $product)
            //     $data[ $product['part_number'] ][ $month ] = $product['amount_sold'];

            foreach ($product_array as $product) {

                $data[$product['id']]['part_number'] = $product['part_number'];
                $data[$product['id']]['description'] = $product['description'];

                if ($select_period == 'period_monthly') {
                    $data[$product['id']][$month] = ($report_format == 'sales' ? $product['amount_sold'] : $product['quantity_sold']);
                } elseif ($select_period == 'period_quarterly') {
                    $data[$product['id']][$month] = ($report_format == 'sales' ? $product['amount_sold'] : $product['quantity_sold']);
                } elseif ($select_period == 'period_yearly') {
                    $data[$product['id']][$month] = ($report_format == 'sales' ? $product['amount_sold'] : $product['quantity_sold']);
                }

            }
        }
        //dd($data);
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $arr = [];

        if ($select_period == 'period_monthly') {

            foreach ($data as $key => $value) {
                $total = 0;

                if (array_key_exists($month, $value)) {
                    $total = (float) $value[$month];
                }

                $arr[] = [
                    //"id" => $key,
                    'part_number' => $value['part_number'].'<br><small>'.$value['description'].'</small>',
                    $month => (array_key_exists($month, $value) ? ($report_format == 'sales' ? $fmt->formatCurrency($value[$month], 'INR') : number_format($value[$month], 0, '.', ',')) : 0),
                    'total' => $total,
                ];
            }
        } elseif ($select_period == 'period_quarterly') {
            foreach ($data as $key => $value) {
                $arr[$key] = [
                    //"id" => $key,
                    'part_number' => $value['part_number'].'<br><small>'.$value['description'].'</small>',
                    'Q1' => 0,
                    'Q2' => 0,
                    'Q3' => 0,
                    'Q4' => 0,
                    'total' => 0,
                ];

                $total = 0;

                if (array_key_exists('Q1', $value)) {
                    $total += (float) $value['Q1'];
                    $arr[$key]['Q1'] = ($report_format == 'sales' ? $fmt->formatCurrency($value['Q1'], 'INR') : number_format($value['Q1'], 0, '.', ','));
                }
                if (array_key_exists('Q2', $value)) {
                    $total += (float) $value['Q2'];
                    $arr[$key]['Q2'] = ($report_format == 'sales' ? $fmt->formatCurrency($value['Q2'], 'INR') : number_format($value['Q2'], 0, '.', ','));
                }
                if (array_key_exists('Q3', $value)) {
                    $total += (float) $value['Q3'];
                    $arr[$key]['Q3'] = ($report_format == 'sales' ? $fmt->formatCurrency($value['Q3'], 'INR') : number_format($value['Q3'], 0, '.', ','));
                }
                if (array_key_exists('Q4', $value)) {
                    $total += (float) $value['Q4'];
                    $arr[$key]['Q4'] = ($report_format == 'sales' ? $fmt->formatCurrency($value['Q4'], 'INR') : number_format($value['Q4'], 0, '.', ','));
                }

                $arr[$key]['total'] = (float) $total;
            }
        } else {
            foreach ($data as $key => $value) {

                $arr[$key] = [
                    //"id" => $key,
                    'part_number' => $value['part_number'].'<br><small>'.$value['description'].'</small>',
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                    6 => 0,
                    7 => 0,
                    8 => 0,
                    9 => 0,
                    10 => 0,
                    11 => 0,
                    12 => 0,
                    'total' => 0,
                ];

                $total = 0;

                for ($i = 1; $i <= 12; $i++) {
                    if (array_key_exists($i, $value)) {
                        $total += (float) $value[$i];
                        $arr[$key][$i] = ($report_format == 'sales' ? $fmt->formatCurrency($value[$i], 'INR') : number_format($value[$i], 0, '.', ','));
                    }
                }

                $arr[$key]['total'] = (float) $total;

            }
        }

        /*
            sort, descending, on the total column
            for DESC sorting, you reverse the position of the a and the b
        */
        usort($arr, function ($a, $b) {
            //return $a['total'] <=> $b['total'];
            return $b['total'] <=> $a['total'];
        });

        /*
            if limit is set, slice the array
        */
        if ($limit > 0) {
            $arr = array_slice($arr, 0, $limit);
        }

        /*
            format the total column
        */
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i]['total'] = ($report_format == 'sales' ? $fmt->formatCurrency($arr[$i]['total'], 'INR') : number_format($arr[$i]['total'], 0, '.', ','));
        }

        $totalRecords = count($arr);
        $totalRecordswithFilter = count($arr);

        $columns = [];

        //$columns[0]['data'] = 'id';
        //$columns[0]['title'] = 'Id';

        $columns[0]['data'] = 'part_number';
        $columns[0]['title'] = 'Part Number';
        //$columns[0]['visible'] = false;
        //$columns[0]['className'] = "text-end";

        if ($select_period == 'period_monthly') {
            $columns[1]['data'] = $month;
            $columns[1]['title'] = $month_short[$month];
            $columns[1]['className'] = 'text-end';

            $columns[2]['data'] = 'total';
            $columns[2]['title'] = ($report_format == 'sales' ? 'Total<br>(Avg Selling Price)' : 'Total');
            $columns[2]['className'] = 'text-end';
        } elseif ($select_period == 'period_quarterly') {
            $columns[1]['data'] = 'Q1';
            $columns[1]['title'] = 'January 1 – March 31';
            $columns[1]['className'] = 'text-end';
            $columns[2]['data'] = 'Q2';
            $columns[2]['title'] = 'April 1 – June 30';
            $columns[2]['className'] = 'text-end';
            $columns[3]['data'] = 'Q3';
            $columns[3]['title'] = 'July 1 – September 30';
            $columns[3]['className'] = 'text-end';
            $columns[4]['data'] = 'Q4';
            $columns[4]['title'] = 'October 1 – December 31';
            $columns[4]['className'] = 'text-end';

            $columns[5]['data'] = 'total';
            $columns[5]['title'] = ($report_format == 'sales' ? 'Total<br>(Avg Selling Price)' : 'Total');
            $columns[5]['className'] = 'text-end';
        } elseif ($select_period == 'period_yearly') {
            for ($i = 1; $i <= 12; $i++) {
                $columns[$i]['data'] = $i;
                $columns[$i]['title'] = $month_short[$i];
                $columns[$i]['className'] = 'text-end';
            }

            $columns[13]['data'] = 'total';
            $columns[13]['title'] = ($report_format == 'sales' ? 'Total<br>(Avg Selling Price)' : 'Total');
            $columns[13]['className'] = 'text-end';
        }

        // $arr = array();
        // $arr[0]['id'] = '1';
        // $arr[0]['part_number'] = 'test';

        $response = ['data' => $arr, 'columns' => $columns, 'footer' => 'TOTALS'];

        //return response()->json($response);
        return json_encode($response);
    }
}
