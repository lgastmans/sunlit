<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
//use Spatie\Activitylog\LogOptions;
//use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use NumberFormatter;

class SaleOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
    //    use LogsActivity;

    protected $fillable = ['dealer_id', 'warehouse_id', 'order_number', 'order_number_slug', 'status', 'user_id', 'amount', 'transport_charges', 'transport_tax', 'payment_terms', 'shipping_state_id', 'shipping_company', 'shipping_address', 'shipping_address2', 'shipping_address3', 'shipping_city', 'shipping_zip_code', 'shipping_gstin', 'shipping_contact_person', 'shipping_contact_person2', 'shipping_phone', 'shipping_phone2', 'shipping_email', 'shipping_email2'];

    protected $with = ['dealer', 'warehouse', 'user', 'items', 'state', 'sale_order_payments'];
    //protected static $recordEvents = ['created','updated','deleted'];

    const DRAFT = 1;

    const BLOCKED = 2;      // changed to Blocked, was Ordered

    const BOOKED = 3;       // changed to Booked, was Confirmed

    const DISPATCHED = 4;   // changed to Dispatched, was Shipped

    // const CUSTOMS = 5;
    // const CLEARED = 6;
    const DELIVERED = 7;    // remove

    /**
     * calculated fields for Sales Order
     */
    public $sub_total = 0;

    public $tax_total = 0;

    public $freight_charges = 0;   // = total weight * rate per kg / zone

    public $transport_total = 0;   // = freight_charges + with tax

    public $transport_tax_amount = 0;

    public $transport_tax_amount_unfmt = 0;

    public $tax_total_half = 0;

    public $total = 0;

    public $total_unfmt = 0;       // unformatted total

    public $total_spellout = '';

    public $total_advance = 0;

    public $balance_due = 0;

    public $tcs_amount = 0;

    protected function casts(): array
    {
        return [
            'blocked_at' => 'datetime',
            'booked_at' => 'datetime',
            'dispatched_at' => 'datetime',
            'paid_at' => 'datetime',
            'due_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }

    /*
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['dealer.company', 'warehouse.name,','order_number', 'status', 'user.name', 'blocked_at','booked_at','dispatched_at'])
        ->dontLogIfAttributesChangedOnly(['updated_at','shipping_company','shipping_state_id','shipping_gstin','shipping_phone','shipping_city','shipping_address2','shipping_address','shipping_contact_person','shipping_zip_code']);
    }
    */

    /**
     * Get the items associated with the sale order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    /**
     * Get the dealer associated with the sale order.
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * Get the warehouse associated with the sale order.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the user associated with the sale order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the state associated with the dealer.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'shipping_state_id');
    }

    public function sale_order_payments(): HasMany
    {
        return $this->hasMany(SaleOrderPayment::class);
    }

    public function canDispatch()
    {
        $response = [];
        $response['success'] = 1;
        // $response['item'] = 'test';
        //return response()->json($response);

        $inventory = new Inventory;

        foreach ($this->items as $item) {
            if (! $inventory->hasStock($this->warehouse_id, $item->product_id, $item->quantity_ordered)) {
                $response['success'] = 0;
                $response['item'] = 'Part number '.$item->product->part_number;
            }
        }

        return $response;
    }

    /**
     * Duplicate a Sales Order
     */
    public function duplicateOrder()
    {
        $clone = $this->replicate();
        $clone->push();

        foreach ($this->items as $item) {
            $newItem = $item->replicate()->fill(['sale_order_id' => $clone->id]);
            $newItem->save();
            //$clone->items()->attach($item);
        }

        $order_number_count = \Setting::get('sale_order.order_number') + 1;
        \Setting::set('sale_order.order_number', $order_number_count);
        \Setting::save();

        $order_number = \Setting::get('sale_order.prefix').$order_number_count.\Setting::get('sale_order.suffix');
        $order_number_slug = str_replace([' ', '/'], '-', $order_number);

        $clone->created_at = Carbon::now();
        $clone->status = SaleOrder::DRAFT;
        $clone->order_number = $order_number;
        $clone->order_number_slug = $order_number_slug;

        $clone->save();

        return $clone;
    }

    /**
     * Calculate the sales totals per product
     */
    public function calculateProductOverallSales($product_id)
    {
        $query = SaleOrder::query();

        $query->select('products.id', 'products.part_number')
            ->selectRaw('SUM(sale_order_items.quantity_ordered) AS quantity_sold')
            ->selectRaw('SUM(sale_order_items.selling_price * sale_order_items.quantity_ordered) / SUM(sale_order_items.quantity_ordered) AS avg_selling_price')
            ->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id')
            ->join('products', 'products.id', '=', 'sale_order_items.product_id')
            ->where('products.id', '=', intval($product_id))
            ->where('sale_orders.status', '>=', SaleOrder::DISPATCHED)
            ->whereNull('sale_order_items.deleted_at');

        $res = $query->get()->first();

        return $res->quantity_sold * $res->avg_selling_price;
    }

    public function calculateProductMonthSalesTotals($period = 'period_monthly', $month = '', $year = '', $part_number = '_ALL', $quarter = null)
    {

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        if (empty($month)) {
            $month = date('n');
        }

        if (empty($year)) {
            $year = date('Y');
        }

        if (is_null($quarter)) {
            $quarter = 'Q1';
        }

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $query = SaleOrder::query();

        $query->select('products.id', 'products.part_number', 'products.notes')
            ->selectRaw('SUM(sale_order_items.quantity_ordered) AS quantity_sold')
            ->selectRaw('SUM(sale_order_items.selling_price * sale_order_items.quantity_ordered) / SUM(sale_order_items.quantity_ordered) AS avg_selling_price')
            ->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id')
            ->join('products', 'products.id', '=', 'sale_order_items.product_id');

        if ($part_number != '_ALL') {
            $query->where('products.part_number', 'LIKE', '%'.$part_number.'%');
        }

        $query->where('sale_orders.status', '>=', SaleOrder::DISPATCHED);
        $query->whereNull('sale_order_items.deleted_at');

        if (($period == 'period_monthly') || ($period == 'period_yearly')) {
            $query->whereYear('sale_orders.dispatched_at', '=', $year);
            $query->whereMonth('sale_orders.dispatched_at', '=', $month);
        } else {
            if ($quarter == 'Q1') {
                $from = date($year.'-01-01');
                $to = date($year.'-03-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            } elseif ($quarter == 'Q2') {
                $from = date($year.'-04-01');
                $to = date($year.'-06-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            } elseif ($quarter == 'Q3') {
                $from = date($year.'-07-01');
                $to = date($year.'-09-30');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            } elseif ($quarter == 'Q4') {
                $from = date($year.'-10-01');
                $to = date($year.'-12-31');
                $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
            }
        }

        $query->groupBy('products.id');
        $query->orderBy('products.part_number', 'ASC');

        //dd($query->toSql());
        $rows = $query->get();

        $res = [];
        foreach ($rows as $product) {
            $res[$product->id]['id'] = $product->id;
            $res[$product->id]['part_number'] = $product->part_number;
            $res[$product->id]['description'] = $product->notes;
            $res[$product->id]['quantity_sold'] = $product->quantity_sold;
            $res[$product->id]['amount_sold'] = $product->quantity_sold * $product->avg_selling_price;
        }

        return $res;
    }

    public function calculateProductSalesTotals($period = '', $year = '', $month = '_ALL', $quarter = '_ALL', $part_number = '_ALL', $limit = 0)
    {
        $select_period = 'period_monthly';
        if (! empty($period)) {
            $select_period = $period;
        }

        if (empty($month)) {
            $month = date('n');
        }

        if (empty($year)) {
            $year = date('Y');
        }

        $products = Product::select('id', 'part_number');
        if ($part_number != '_ALL') {
            $products->where('part_number', 'like', '%'.$part_number.'%');
        }
        //dd("period ".$select_period,":month ".$month,":year ".$year,":quarter ".$quarter);

        $products = $products->orderBy('part_number')
            ->get();

        $res = [];

        /**
         * function 'calculateProductMonthSalesTotals' returns an array with data per product
         * iterate through this 'per product' array and group into a 'per month'/'per quarter' array
         */
        foreach ($products as $product) {
            if ($select_period == 'period_yearly') {
                for ($month = 1; $month <= 12; $month++) {
                    $res[$month] = $this->calculateProductMonthSalesTotals($select_period, $month, $year, $part_number);
                }

            } elseif ($select_period == 'period_monthly') {
                $res[$month] = $this->calculateProductMonthSalesTotals($select_period, $month, $year, $part_number);
            } else {
                if ($quarter == '_ALL') {
                    //$res['Q1'] = $this->calculateProductQuarterSalesTotals('Q1', $year);
                    $res['Q1'] = $this->calculateProductMonthSalesTotals($select_period, $month, $year, $part_number, 'Q1');
                    //$row_total += (float)$res[$category->name]['Q1']['total_amount_unfmt'];
                    //$totals['Q1'] += (float)$res[$category->name]['Q1']['total_amount_unfmt'];

                    $res['Q2'] = $this->calculateProductMonthSalesTotals($select_period, $month, $year, $part_number, 'Q2');
                    //$row_total += (float)$res[$category->name]['Q2']['total_amount_unfmt'];
                    //$totals['Q2'] += (float)$res[$category->name]['Q2']['total_amount_unfmt'];

                    $res['Q3'] = $this->calculateProductMonthSalesTotals($select_period, $month, $year, $part_number, 'Q3');
                    //$row_total += (float)$res[$category->name]['Q3']['total_amount_unfmt'];
                    //$totals['Q3'] += (float)$res[$category->name]['Q3']['total_amount_unfmt'];

                    $res['Q4'] = $this->calculateProductMonthSalesTotals($select_period, $month, $year, $part_number, 'Q4');
                    //$row_total += (float)$res[$category->name]['Q4']['total_amount_unfmt'];
                    //$totals['Q4'] += (float)$res[$category->name]['Q4']['total_amount_unfmt'];
                } else {
                    $res[$quarter] = $this->calculateProductMonthSalesTotals($select_period, $month, $year, $part_number, $quarter);
                }
            }
        }

        return $res;
    }

    /**
     * Calculate the sales totals per category
     */
    public function calculateMonthSalesTotals($month = '', $year = '', $category_id = 0)
    {
        if (empty($month)) {
            $month = date('n');
        }

        if (empty($year)) {
            $year = date('Y');
        }

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, 'INR');
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $query = SaleOrder::select('categories.name', 'products.part_number', 'products.model', 'products.kw_rating', 'sale_order_items.selling_price', 'sale_order_items.tax')
            ->selectRaw('SUM(sale_order_items.quantity_ordered) AS quantity_ordered')
            ->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id')
            ->join('products', 'products.id', '=', 'sale_order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('sale_orders.status', '>=', SaleOrder::DISPATCHED)
            ->where('categories.id', '=', $category_id)
            ->whereNull('sale_order_items.deleted_at');

        $query->whereYear('sale_orders.dispatched_at', '=', $year);
        $query->whereMonth('sale_orders.dispatched_at', '=', $month);

        $query->groupBy('products.id', 'sale_order_items.selling_price');
        $query->orderBy('categories.name', 'ASC');
        $query->orderBy('products.part_number', 'ASC');

        //return $query->toSql();
        $rows = $query->get();

        $total_amount = 0;
        $total_tax = 0;
        $total_qty = 0;
        foreach ($rows as $row) {
            $total_amount += ($row->selling_price * $row->quantity_ordered);
            $total_tax += ($row->selling_price * $row->quantity_ordered) * ($row->tax / 100);
            $total_qty += $row->quantity_ordered;
        }

        $res['total_amount_unfmt'] = $total_amount;
        $res['total_amount'] = $fmt->formatCurrency($total_amount, 'INR');
        $res['total_qty'] = $fmt->formatCurrency($total_qty, 'INR');

        return $res;
    }

    public function calculateQuarterSalesTotals($quarter = '', $year = '', $category_id = 0)
    {
        if (empty($quarter)) {
            $quarter = 'Q1';
        }

        if (empty($year)) {
            $year = date('Y');
        }

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $query = SaleOrder::select('categories.name', 'products.part_number', 'products.model', 'products.kw_rating', 'sale_order_items.selling_price', 'sale_order_items.tax')
            ->selectRaw('SUM(sale_order_items.quantity_ordered) AS quantity_ordered')
            ->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id')
            ->join('products', 'products.id', '=', 'sale_order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('sale_orders.status', '>=', SaleOrder::DISPATCHED)
            ->where('categories.id', '=', $category_id)
            ->whereNull('sale_order_items.deleted_at');

        if ($quarter == 'Q1') {
            $from = date($year.'-01-01');
            $to = date($year.'-03-31');
            $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
        } elseif ($quarter == 'Q2') {
            $from = date($year.'-04-01');
            $to = date($year.'-06-30');
            $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
        } elseif ($quarter == 'Q3') {
            $from = date($year.'-07-01');
            $to = date($year.'-09-30');
            $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
        } elseif ($quarter == 'Q4') {
            $from = date($year.'-10-01');
            $to = date($year.'-12-31');
            $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
        }

        $query->groupBy('products.id', 'sale_order_items.selling_price');
        $query->orderBy('categories.name', 'ASC');
        $query->orderBy('products.part_number', 'ASC');

        $rows = $query->get();

        $total_amount = 0;
        $total_tax = 0;
        $total_qty = 0;
        foreach ($rows as $row) {
            $total_amount += ($row->selling_price * $row->quantity_ordered);
            $total_tax += ($row->selling_price * $row->quantity_ordered) * ($row->tax / 100);
            $total_qty += $row->quantity_ordered;
        }

        $res['total_amount_unfmt'] = $total_amount;
        $res['total_amount'] = $fmt->formatCurrency($total_amount, 'INR');
        $res['total_qty'] = $fmt->formatCurrency($total_qty, 'INR');

        return $res;
    }

    /*
        This function calculates based on Category
        Should have been called "calculateCategorySalesTotals"
    */
    public function calculateSalesTotals($period = '', $year = '', $month = '_ALL', $quarter = '_ALL', $category = '_ALL')
    {
        $select_period = 'period_monthly';
        if (! empty($period)) {
            $select_period = $period;
        }

        if (empty($month)) {
            $month = date('n');
        }

        if (empty($year)) {
            $year = date('Y');
        }

        if (empty($quarter)) {
            $quarter = 'Q1';
        }

        $categories = Category::select('id', 'name');
        if ($category != '_ALL') {
            $categories->where('id', '=', $category);
        }
        $categories = $categories->orderBy('name')
            ->get();

        $res = [];

        $totals = [];
        if ($select_period == 'period_monthly') {
            for ($i = 1; $i <= 12; $i++) {
                $totals[$i] = 0;
            }
        } else {
            $totals['Q1'] = 0;
            $totals['Q2'] = 0;
            $totals['Q3'] = 0;
            $totals['Q4'] = 0;
        }

        foreach ($categories as $category) {

            if ($select_period == 'period_monthly') {
                if ($month == '_ALL') {
                    $row_total = 0;

                    for ($i = 1; $i <= 12; $i++) {
                        $dt = DateTime::createFromFormat('!m', $i);
                        $res[$category->name][$dt->format('F')] = $this->calculateMonthSalesTotals($i, $year, $category->id);
                        $row_total += (float) $res[$category->name][$dt->format('F')]['total_amount_unfmt'];

                        $totals[$i] += (float) $res[$category->name][$dt->format('F')]['total_amount_unfmt'];
                    }

                    $res[$category->name]['row_total'] = $row_total;
                } else {
                    $dt = DateTime::createFromFormat('!m', $month);
                    $res[$category->name][$dt->format('F')] = $this->calculateMonthSalesTotals($month, $year, $category->id);
                }
            } else {
                if ($quarter == '_ALL') {
                    $row_total = 0;

                    if ($select_period == 'period_quarterly') {
                        $res[$category->name]['Q1'] = $this->calculateQuarterSalesTotals('Q1', $year, $category->id);
                        $row_total += (float) $res[$category->name]['Q1']['total_amount_unfmt'];
                        $totals['Q1'] += (float) $res[$category->name]['Q1']['total_amount_unfmt'];

                        $res[$category->name]['Q2'] = $this->calculateQuarterSalesTotals('Q2', $year, $category->id);
                        $row_total += (float) $res[$category->name]['Q2']['total_amount_unfmt'];
                        $totals['Q2'] += (float) $res[$category->name]['Q2']['total_amount_unfmt'];

                        $res[$category->name]['Q3'] = $this->calculateQuarterSalesTotals('Q3', $year, $category->id);
                        $row_total += (float) $res[$category->name]['Q3']['total_amount_unfmt'];
                        $totals['Q3'] += (float) $res[$category->name]['Q3']['total_amount_unfmt'];

                        $res[$category->name]['Q4'] = $this->calculateQuarterSalesTotals('Q4', $year, $category->id);
                        $row_total += (float) $res[$category->name]['Q4']['total_amount_unfmt'];
                        $totals['Q4'] += (float) $res[$category->name]['Q4']['total_amount_unfmt'];
                    } else {
                        $res[$category->name]['Q1'] = $this->calculateQuarterSalesTotals('Q2', $year, $category->id);
                        $row_total += (float) $res[$category->name]['Q1']['total_amount_unfmt'];
                        $totals['Q1'] += (float) $res[$category->name]['Q1']['total_amount_unfmt'];

                        $res[$category->name]['Q2'] = $this->calculateQuarterSalesTotals('Q3', $year, $category->id);
                        $row_total += (float) $res[$category->name]['Q2']['total_amount_unfmt'];
                        $totals['Q2'] += (float) $res[$category->name]['Q2']['total_amount_unfmt'];

                        $res[$category->name]['Q3'] = $this->calculateQuarterSalesTotals('Q4', $year, $category->id);
                        $row_total += (float) $res[$category->name]['Q3']['total_amount_unfmt'];
                        $totals['Q3'] += (float) $res[$category->name]['Q3']['total_amount_unfmt'];

                        $res[$category->name]['Q4'] = $this->calculateQuarterSalesTotals('Q1', ($year + 1), $category->id);
                        $row_total += (float) $res[$category->name]['Q4']['total_amount_unfmt'];
                        $totals['Q4'] += (float) $res[$category->name]['Q4']['total_amount_unfmt'];
                    }

                    $res[$category->name]['row_total'] = $row_total;
                } else {
                    $res[$category->name][$quarter] = $this->calculateQuarterSalesTotals($quarter, $year, $category->id);
                }
            }
        }

        $totals['grand_total'] = 0;
        if ($select_period == 'period_monthly') {
            for ($i = 1; $i <= 12; $i++) {
                $totals['grand_total'] += $totals[$i];
            }
        } else {
            $totals['grand_total'] = $totals['Q1'] + $totals['Q2'] + $totals['Q3'] + $totals['Q4'];
        }

        uasort($res, fn ($a, $b) => $b['row_total'] <=> $a['row_total']);

        $res['column_total'] = $totals;

        return $res;
    }

    /**
     * Calculate the sales totals per state
     */
    public function calculateMonthStateSalesTotals($month = '', $year = '', $state_id = 0)
    {
        if (empty($month)) {
            $month = date('n');
        }

        if (empty($year)) {
            $year = date('Y');
        }

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $query = SaleOrder::select('states.name AS state_name', 'categories.name AS category_name', 'products.part_number', 'products.model', 'products.kw_rating', 'sale_order_items.selling_price', 'sale_order_items.tax')
            ->selectRaw('SUM(sale_order_items.quantity_ordered) AS quantity_ordered')
            ->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id')
            ->join('products', 'products.id', '=', 'sale_order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('dealers', 'dealers.id', '=', 'sale_orders.dealer_id')
            ->join('states', 'states.id', '=', 'dealers.state_id')
            ->where('sale_orders.status', '>=', SaleOrder::DISPATCHED)
            ->where('dealers.state_id', '=', $state_id)
            ->whereNull('sale_order_items.deleted_at');

        $query->whereYear('sale_orders.dispatched_at', '=', $year);
        $query->whereMonth('sale_orders.dispatched_at', '=', $month);

        $query->groupBy('products.id', 'sale_order_items.selling_price');
        $query->orderBy('categories.name', 'ASC');
        $query->orderBy('products.part_number', 'ASC');

        //return $query->toSql();
        $rows = $query->get();

        $total_amount = 0;
        $total_tax = 0;
        $total_qty = 0;
        foreach ($rows as $row) {
            $total_amount += ($row->selling_price * $row->quantity_ordered);
            $total_tax += ($row->selling_price * $row->quantity_ordered) * ($row->tax / 100);
            $total_qty += $row->quantity_ordered;
        }

        $res['total_amount_unfmt'] = $total_amount;
        $res['total_amount'] = $fmt->formatCurrency($total_amount, 'INR');
        $res['total_qty'] = $fmt->formatCurrency($total_qty, 'INR');

        return $res;
    }

    public function calculateQuarterStateSalesTotals($quarter = '', $year = '', $state_id = 0)
    {
        if (empty($quarter)) {
            $quarter = 'Q1';
        }

        if (empty($year)) {
            $year = date('Y');
        }

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $query = SaleOrder::select('states.name AS state_name', 'categories.name AS category_name', 'products.part_number', 'products.model', 'products.kw_rating', 'sale_order_items.selling_price', 'sale_order_items.tax')
            ->selectRaw('SUM(sale_order_items.quantity_ordered) AS quantity_ordered')
            ->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id')
            ->join('products', 'products.id', '=', 'sale_order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('dealers', 'dealers.id', '=', 'sale_orders.dealer_id')
            ->join('states', 'states.id', '=', 'dealers.state_id')
            ->where('sale_orders.status', '>=', SaleOrder::DISPATCHED)
            ->where('dealers.state_id', '=', $state_id)
            ->whereNull('sale_order_items.deleted_at');

        if ($quarter == 'Q1') {
            $from = date($year.'-01-01');
            $to = date($year.'-03-31');
            $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
        } elseif ($quarter == 'Q2') {
            $from = date($year.'-04-01');
            $to = date($year.'-06-30');
            $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
        } elseif ($quarter == 'Q3') {
            $from = date($year.'-07-01');
            $to = date($year.'-09-30');
            $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
        } elseif ($quarter == 'Q4') {
            $from = date($year.'-10-01');
            $to = date($year.'-12-31');
            $query->whereBetween('sale_orders.dispatched_at', [$from, $to]);
        }

        $query->groupBy('products.id', 'sale_order_items.selling_price');
        $query->orderBy('categories.name', 'ASC');
        $query->orderBy('products.part_number', 'ASC');

        //return $query->toSql();
        $rows = $query->get();

        $total_amount = 0;
        $total_tax = 0;
        $total_qty = 0;
        foreach ($rows as $row) {
            $total_amount += ($row->selling_price * $row->quantity_ordered);
            $total_tax += ($row->selling_price * $row->quantity_ordered) * ($row->tax / 100);
            $total_qty += $row->quantity_ordered;
        }

        $res['total_amount_unfmt'] = $total_amount;
        $res['total_amount'] = $fmt->formatCurrency($total_amount, 'INR');
        $res['total_qty'] = $fmt->formatCurrency($total_qty, 'INR');

        return $res;
    }

    public function calculateStateSalesTotals($period = '', $year = '', $month = '_ALL', $quarter = '_ALL', $state = '_ALL')
    {
        $select_period = 'period_monthly';
        if (! empty($period)) {
            $select_period = $period;
        }

        if (empty($month)) {
            $month = date('n');
        }

        if (empty($year)) {
            $year = date('Y');
        }

        if (empty($quarter)) {
            $quarter = 'Q1';
        }

        $states = State::select('id', 'name');
        if ($state != '_ALL') {
            $states->where('id', '=', $state);
        }
        $states = $states->orderBy('name')
            ->get();

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $res = [];

        $totals = [];
        if ($select_period == 'period_monthly') {
            for ($i = 1; $i <= 12; $i++) {
                $totals[$i] = 0;
            }
        } else {
            $totals['Q1'] = 0;
            $totals['Q2'] = 0;
            $totals['Q3'] = 0;
            $totals['Q4'] = 0;
        }

        foreach ($states as $row) {

            if ($select_period == 'period_monthly') {
                if ($month == '_ALL') {
                    $row_total = 0;

                    for ($i = 1; $i <= 12; $i++) {
                        $dt = DateTime::createFromFormat('!m', $i);
                        $res[$row->name][$dt->format('F')] = $this->calculateMonthStateSalesTotals($i, $year, $row->id);
                        $row_total += (float) $res[$row->name][$dt->format('F')]['total_amount_unfmt'];

                        $totals[$i] += (float) $res[$row->name][$dt->format('F')]['total_amount_unfmt'];
                    }

                    $res[$row->name]['row_total'] = $row_total; //$fmt->formatCurrency($row_total, "INR");

                } else {
                    $dt = DateTime::createFromFormat('!m', $month);
                    $res[$row->name][$dt->format('F')] = $this->calculateMonthStateSalesTotals($month, $year, $row->id);
                }
            } else {
                if ($quarter == '_ALL') {
                    $row_total = 0;

                    if ($select_period == 'period_quarterly') {
                        $res[$row->name]['Q1'] = $this->calculateQuarterStateSalesTotals('Q1', $year, $row->id, $select_period);
                        $row_total += (float) $res[$row->name]['Q1']['total_amount_unfmt'];
                        $totals['Q1'] += (float) $res[$row->name]['Q1']['total_amount_unfmt'];

                        $res[$row->name]['Q2'] = $this->calculateQuarterStateSalesTotals('Q2', $year, $row->id, $select_period);
                        $row_total += (float) $res[$row->name]['Q2']['total_amount_unfmt'];
                        $totals['Q2'] += (float) $res[$row->name]['Q2']['total_amount_unfmt'];

                        $res[$row->name]['Q3'] = $this->calculateQuarterStateSalesTotals('Q3', $year, $row->id, $select_period);
                        $row_total += (float) $res[$row->name]['Q3']['total_amount_unfmt'];
                        $totals['Q3'] += (float) $res[$row->name]['Q3']['total_amount_unfmt'];

                        $res[$row->name]['Q4'] = $this->calculateQuarterStateSalesTotals('Q4', $year, $row->id, $select_period);
                        $row_total += (float) $res[$row->name]['Q4']['total_amount_unfmt'];
                        $totals['Q4'] += (float) $res[$row->name]['Q4']['total_amount_unfmt'];
                    } else {
                        $res[$row->name]['Q1'] = $this->calculateQuarterStateSalesTotals('Q2', $year, $row->id, $select_period);
                        $row_total += (float) $res[$row->name]['Q1']['total_amount_unfmt'];
                        $totals['Q1'] += (float) $res[$row->name]['Q1']['total_amount_unfmt'];

                        $res[$row->name]['Q2'] = $this->calculateQuarterStateSalesTotals('Q3', $year, $row->id, $select_period);
                        $row_total += (float) $res[$row->name]['Q2']['total_amount_unfmt'];
                        $totals['Q2'] += (float) $res[$row->name]['Q2']['total_amount_unfmt'];

                        $res[$row->name]['Q3'] = $this->calculateQuarterStateSalesTotals('Q4', $year, $row->id, $select_period);
                        $row_total += (float) $res[$row->name]['Q3']['total_amount_unfmt'];
                        $totals['Q3'] += (float) $res[$row->name]['Q3']['total_amount_unfmt'];

                        $res[$row->name]['Q4'] = $this->calculateQuarterStateSalesTotals('Q1', ($year + 1), $row->id, $select_period);
                        $row_total += (float) $res[$row->name]['Q4']['total_amount_unfmt'];
                        $totals['Q4'] += (float) $res[$row->name]['Q4']['total_amount_unfmt'];
                    }

                    $res[$row->name]['row_total'] = $row_total;
                } else {
                    $res[$row->name][$quarter] = $this->calculateQuarterStateSalesTotals($quarter, $year, $row->id);
                }
            }
        }

        $totals['grand_total'] = 0;
        if ($select_period == 'period_monthly') {
            for ($i = 1; $i <= 12; $i++) {
                $totals['grand_total'] += $totals[$i];
            }
        } else {
            $totals['grand_total'] = $totals['Q1'] + $totals['Q2'] + $totals['Q3'] + $totals['Q4'];
        }

        uasort($res, fn ($a, $b) => $b['row_total'] <=> $a['row_total']);

        $res['column_total'] = $totals;

        return $res;
    }

    /**
     * Calculate the totals per Sale Order
     */
    public function calculateTotals()
    {
        $tax = 0;
        $rate_per_kg = 0;

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        if (is_null($this->transport_charges)) {
            $this->transport_charges = 0;
        }

        $this->sub_total = 0;
        $this->tax_total = 0;
        $this->freight_charges = (float) $this->transport_charges;
        $this->transport_total = 0; // = freight_charges + with tax
        $this->total = 0;
        $this->total_unfmt = 0;
        $this->total_advance = 0;
        $this->balance_due = 0;
        $this->tcs_amount = 0;

        if ($this->dealer) {
            if ($this->dealer->state->freight_zone_id) {
                $zone = FreightZone::find($this->dealer->state->freight_zone_id);
                $rate_per_kg = $zone->rate_per_kg;
            }
        }

        /**
         * calculate the totals
         */
        $tax = 0;
        foreach ($this->items as $item) {
            $this->sub_total += $item->quantity_ordered * $item->selling_price;
            $this->tax_total += ($item->quantity_ordered * $item->selling_price) * ($item->tax / 100);
            /*
                this feature left out until finalized
                with Sunlit Future
            */
            //$this->freight_charges += ($item->quantity_ordered * $item->product->weight_calculated) * $rate_per_kg;

            /*
                get the HSN breakdown according to the item's category
                the code below is from invent, to group tax totals according to HSN
                but this request was not required
                just in case in future this is requested...
            */
            /*
            $int_index = getColumn($arr_taxes, $sql_items->FieldByName('tax_id'));
            if ($int_index > -1)
                $arr_taxes[$int_index][2] += ($tax_amount * $total_quantity);
            */

            /*
                select the highest tax from the list of items
            */
            if ($item->tax > $tax) {
                $tax = $item->tax;
            }
        }

        /**
         * Up until January 2024, the tax on the transport charges was based on the highest tax in the list of products
         * after January 2024, the tax is set in the Global Settings
         */
        if (!is_null($this->transport_tax)) 
            $tax = $this->transport_tax;

        /**
         * add the Transport Charges to the totals
         */
        $this->sub_total += (float) $this->transport_charges;
        $this->transport_tax_amount = (float) ($this->transport_charges * $tax / 100);
        $this->transport_total = (float) $this->transport_charges + (float) $this->transport_tax_amount;
        $this->tax_total += (float) $this->transport_charges * $tax / 100;

        $this->total = $this->sub_total + $this->tax_total;

        /**
         * add the TCS, which is calculated on the total + tax
         */
        $this->tcs_amount = (float) $this->total * (float) ($this->tcs / 100);

        /**
         * and add the TCS amount to the total before rounding
         */
        $this->total = (float) $this->total + (float) $this->tcs_amount;

        $this->total = (float) round($this->total);

        foreach ($this->sale_order_payments as $payment) {
            $this->total_advance += $payment->amount;
        }

        $this->balance_due = $this->total - $this->total_advance;

        /**
         * for SGST and CGST
         */
        $this->tax_total_half = $this->tax_total / 2;

        $this->total_spellout = $this->expandAmount($this->total);

        /*
            eventually these fields should be formated in
            a get...Attribute function, like the
            total_advance and balance_due columns
        */
        $this->sub_total = $fmt->formatCurrency($this->sub_total, 'INR');
        $this->tax_total = $fmt->formatCurrency($this->tax_total, 'INR');
        $this->transport_total = $fmt->formatCurrency($this->transport_total, 'INR');
        $this->transport_tax_amount_unfmt = (float) $this->transport_tax_amount;
        $this->transport_tax_amount = $fmt->formatCurrency((float) $this->transport_tax_amount, 'INR');
        $this->tax_total_half = $fmt->formatCurrency($this->tax_total_half, 'INR');
        $this->total_unfmt = $this->total;
        $this->total = $fmt->formatCurrency($this->total, 'INR');

        return true;

    }

    public static function expandAmount($amount)
    {

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::SPELLOUT);

        if (strpos($amount, '.') !== false) {

            $numwords = explode('.', $amount);

            if (intval($numwords[1]) > 0) {
                $res = $fmt->format($numwords[0]).' and paise '.$fmt->format($numwords[1]).' only';
            } else {
                $res = $fmt->format((int) $numwords[0]).' only';
            }
        } else {
            $res = $fmt->format($amount).' only';
        }
        $res = 'INR '.$res;

        return ucfirst($res);
    }

    /**
     * Returns the ordered_at date for display Month Day, Year
     */
    public function getDisplayBlockedAtAttribute()
    {
        if ($this->blocked_at) {
            $dt = Carbon::parse($this->blocked_at);

            return $dt->toFormattedDateString();
        }

        return '';
    }

    public function setBlockedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['blocked_at'] = $dt->toDateTimeString();
    }

    /**
     * Returns the confirmed_at date for display Month Day, Year
     */
    public function getDisplayBookedAtAttribute()
    {
        if ($this->booked_at) {
            $dt = Carbon::parse($this->booked_at);

            return $dt->toFormattedDateString();
        }

        return '';
    }

    public function setBookedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['booked_at'] = $dt->toDateTimeString();
    }

    /**
     * Returns the shipped_at date for display Month Day, Year
     */
    public function getDisplayDispatchedAtAttribute()
    {
        if ($this->dispatched_at) {
            $dt = Carbon::parse($this->dispatched_at);

            return $dt->toFormattedDateString();
        }

        return '';
    }

    public function setDispatchedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['dispatched_at'] = $dt->toDateTimeString();
    }

    /**
     * Returns the shipped_at date for display Month Day, Year
     */
    public function getDisplayCreatedAtAttribute()
    {
        if ($this->dispatched_at) {
            $dt = Carbon::parse($this->dispatched_at);

            return $dt->toFormattedDateString();
        }

        return '';
    }

    /**
     * Returns the due_at date for display Month Day, Year
     */
    public function getDisplayDueAtAttribute()
    {
        if ($this->due_at) {
            $dt = Carbon::parse($this->due_at);

            return $dt->toFormattedDateString();
        }

        return '';
    }

    public function setDueAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['due_at'] = $dt->toDateTimeString();
    }

    /**
     * Returns the customs_at date for display Month Day, Year
     */
    public function getDisplayDeliveredAtAttribute()
    {
        if ($this->delivered_at) {
            $dt = Carbon::parse($this->delivered_at);

            return $dt->toFormattedDateString();
        }

        return '';
    }

    public function setDeliveredAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['delivered_at'] = $dt->toDateTimeString();
    }

    public function getDisplayStatusAttribute()
    {
        switch ($this->status) {
            case SaleOrder::DRAFT:
                $status = '<span class="badge badge-secondary-lighten">Draft</span>';
                break;
            case SaleOrder::BLOCKED:
                $status = '<span class="badge badge-info-lighten">Blocked</span>';
                break;
            case SaleOrder::BOOKED:
                $status = '<span class="badge badge-primary-lighten">Booked</span>';
                break;
            case SaleOrder::DISPATCHED:
                $status = '<span class="badge badge-dark-lighten">Dispatched</span>';
                break;
            case SaleOrder::DELIVERED:
                $status = '<span class="badge badge-success-lighten">Delivered</span>';
                break;
            default:
                $status = '<span class="badge badge-error-lighten">Unknown</span>';
        }

        return $status;
    }

    public static function getStatusList()
    {
        return [
            SaleOrder::DRAFT => 'Draft',
            //SaleOrder::BLOCKED => 'Blocked',
            SaleOrder::BOOKED => 'Booked',
            SaleOrder::DISPATCHED => 'Dispatched',
            //SaleOrder::DELIVERED => 'Delivered'
        ];
    }

    public function getDisplayTotalAdvanceAttribute()
    {
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        return $this->total_advance = $fmt->formatCurrency($this->total_advance, 'INR');
    }

    public function getDisplayBalanceDueAttribute()
    {
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        return $this->balance_due = $fmt->formatCurrency($this->balance_due, 'INR');
    }

    public function getDisplayTcsAmountAttribute()
    {
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        return $fmt->formatCurrency($this->tcs_amount, 'INR');
    }

    public function isOverdue()
    {
        if (Carbon::now()->greaterThan(Carbon::parse($this->due_at))) {
            return true;
        }

        return false;
    }

    public function getOrderedDaysAgoAttribute()
    {
        return Carbon::parse($this->ordered_at)->diffForHumans();
    }

    public function scopeOrdered($query)
    {
        return $query->where('status', '>=', SaleOrder::BLOCKED);
    }

    /* * Retrieve orders with status DELIVERED
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', SaleOrder::DELIVERED);
    }
}
