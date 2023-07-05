<?php

namespace App\Models;

use DateTime;
use Carbon\Carbon;
use NumberFormatter;
//use Spatie\Activitylog\LogOptions;
//use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
//    use LogsActivity;

    protected $fillable = ['dealer_id', 'warehouse_id', 'order_number', 'order_number_slug', 'status', 'user_id', 'transport_charges ', 'payment_terms', 'shipping_state_id', 'shipping_company', 'shipping_address', 'shipping_address2', 'shipping_address3', 'shipping_city', 'shipping_zip_code', 'shipping_gstin', 'shipping_contact_person', 'shipping_contact_person2', 'shipping_phone', 'shipping_phone2', 'shipping_email','shipping_email2' ];
    protected $dates = ['blocked_at', 'booked_at', 'dispatched_at', 'paid_at', 'due_at', 'shipped_at'];
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
    var $sub_total = 0;
    var $tax_total = 0;
    var $freight_charges = 0;   // = total weight * rate per kg / zone
    var $transport_total = 0;   // = freight_charges + with tax
    var $transport_tax_amount = 0;
    var $transport_tax_amount_unfmt = 0;
    var $tax_total_half = 0;
    var $total = 0;
    var $total_unfmt = 0;       // unformatted total
    var $total_spellout = '';
    var $total_advance = 0;
    var $balance_due = 0;

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
    public function items()
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    /**
     * Get the dealer associated with the sale order.
     */
    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * Get the warehouse associated with the sale order.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the user associated with the sale order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the state associated with the dealer.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'shipping_state_id');
    }

    public function sale_order_payments()
    {
        return $this->hasMany(SaleOrderPayment::class);
    }

    public function canDispatch()
    {
        $response=array();
        $response['success'] = 1;
        // $response['item'] = 'test';
        //return response()->json($response);
        
        $inventory = new Inventory;

        foreach ($this->items as $item)
        {
            if (!$inventory->hasStock($this->warehouse_id, $item->product_id, $item->quantity_ordered))
            {
                $response['success'] = 0;
                $response['item'] = "Part number ".$item->product->part_number;
            }
        }
        
        return $response;
    }

    /**
     * 
     * Calculate the sales totals per product
     * 
     */
    public function calculateProductMonthSalesTotals($month="", $year="", $part_number="_ALL")
    {

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
        
        if (empty($month))
            $month = date('n');
            
        if (empty($year))
            $year = date('Y');

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $query = SaleOrder::query();

        $query->select('products.id', 'products.part_number', 'products.notes')
            ->selectRaw('SUM(sale_order_items.quantity_ordered) AS quantity_sold')
            ->selectRaw('SUM(sale_order_items.selling_price * sale_order_items.quantity_ordered) / SUM(sale_order_items.quantity_ordered) AS avg_selling_price')
            ->join('sale_order_items', 'sale_order_items.sale_order_id', '=', 'sale_orders.id')
            ->join('products', 'products.id', '=', 'sale_order_items.product_id');

        if ($part_number != "_ALL")
            $query->where('products.part_number', 'LIKE', "%".$part_number."%");

        $query->where('sale_orders.status', '>=', SaleOrder::DISPATCHED);
        $query->whereNull('sale_order_items.deleted_at');

        $query->whereYear('sale_orders.dispatched_at', '=', $year);
        $query->whereMonth('sale_orders.dispatched_at', '=', $month);


        $query->groupBy('products.id');
        $query->orderBy('products.part_number', 'ASC');

        //dd($query->toSql());
        $rows = $query->get();

        $res = array();
        foreach($rows as $product)
        {
            $res[$product->id]['id'] = $product->id;
            $res[$product->id]['part_number'] = $product->part_number;
            $res[$product->id]['description'] = $product->notes;
            $res[$product->id]['quantity_sold'] = $product->quantity_sold;
            $res[$product->id]['amount_sold'] = $product->quantity_sold * $product->avg_selling_price;
        }

        return $res;
    }

    public function calculateProductSalesTotals($period="", $year="", $month="_ALL", $quarter="_ALL", $part_number="_ALL", $limit=0)
    {
        $select_period = 'period_monthly';
        if (!empty($period))
            $select_period = $period;

        if (empty($month))
            $month = date('n');
            
        if (empty($year))
            $year = date('Y');
        
        if (empty($quarter))
            $quarter = 'Q1';

        $products = Product::select('id', 'part_number');
        if ($part_number!='_ALL') {
            $products->where('part_number','like', "%".$part_number."%");
        }

        $products = $products->orderBy('part_number')
            ->get();

        $res = array();
        
        /*
            set the arrays to zero
        */
        $totals = array();
        if ($select_period=='period_monthly')
        {
            for ($i=1;$i<=12;$i++)
            {
                $totals[$i] = 0;
            }
        }
        else {
            $totals['Q1'] = 0;
            $totals['Q2'] = 0;
            $totals['Q3'] = 0;
            $totals['Q4'] = 0;
        }
        
        foreach ($products as $product)
        {

            if ($select_period=='period_monthly')
            {
                if ($month=="_ALL")
                {
                    $row_total = 0;

                    for ($month=1;$month<=12;$month++)
                    {
                        if ($part_number!='_ALL')
                            $res[$month] = $this->calculateProductMonthSalesTotals($month, $year, $part_number);
                        else
                            $res[$month] = $this->calculateProductMonthSalesTotals($month, $year);
                    }
                }
                else
                {
                    /*
                    $dt = DateTime::createFromFormat('!m', $month);
                    $res[$category->name][$dt->format('F')] = $this->calculateMonthSalesTotals($month, $year, $category->id);
                    */
                }
            }
            else
            {
                if ($quarter=="_ALL")
                {
                    $row_total = 0;
                    /*
                    $res[$category->name]['Q1'] = $this->calculateQuarterSalesTotals('Q1', $year, $category->id);
                    $row_total += (float)$res[$category->name]['Q1']['total_amount_unfmt'];
                    $totals['Q1'] += (float)$res[$category->name]['Q1']['total_amount_unfmt'];

                    $res[$category->name]['Q2'] = $this->calculateQuarterSalesTotals('Q2', $year, $category->id);
                    $row_total += (float)$res[$category->name]['Q2']['total_amount_unfmt'];
                    $totals['Q2'] += (float)$res[$category->name]['Q2']['total_amount_unfmt'];

                    $res[$category->name]['Q3'] = $this->calculateQuarterSalesTotals('Q3', $year, $category->id);
                    $row_total += (float)$res[$category->name]['Q3']['total_amount_unfmt'];
                    $totals['Q3'] += (float)$res[$category->name]['Q3']['total_amount_unfmt'];

                    $res[$category->name]['Q4'] = $this->calculateQuarterSalesTotals('Q4', $year, $category->id);
                    $row_total += (float)$res[$category->name]['Q4']['total_amount_unfmt'];
                    $totals['Q4'] += (float)$res[$category->name]['Q4']['total_amount_unfmt'];

                    $res[$category->name]['row_total'] = $row_total;
                    */
                }
                else
                {
                    //$res[$category->name][$quarter] = $this->calculateQuarterSalesTotals($quarter, $year, $category->id);
                }
            }
        }

        //$res['column_total'] = $totals;

        return $res;

    }

    /**
     * 
     * Calculate the sales totals per category
     * 
     */
    public function calculateMonthSalesTotals($month="", $year="", $category_id=0)
    {
        if (empty($month))
            $month = date('n');
            
        if (empty($year))
            $year = date('Y');

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
        $query->orderBy('categories.name','ASC');
        $query->orderBy('products.part_number', 'ASC');

        //return $query->toSql();
        $rows = $query->get();

        $total_amount = 0;
        $total_tax = 0;
        $total_qty = 0;
        foreach($rows as $row)
        {
            $total_amount += ($row->selling_price * $row->quantity_ordered);
            $total_tax += ($row->selling_price * $row->quantity_ordered) * ($row->tax/100);
            $total_qty += $row->quantity_ordered;
        }

        $res['total_amount_unfmt'] = $total_amount;
        $res['total_amount'] = $fmt->formatCurrency($total_amount, "INR");
        $res['total_qty'] = $fmt->formatCurrency($total_qty, "INR");

        return $res;
    }

    public function calculateQuarterSalesTotals($quarter="", $year="", $category_id=0)
    {
        if (empty($quarter))
            $quarter = 'Q1';
            
        if (empty($year))
            $year = date('Y');

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

        $query->groupBy('products.id', 'sale_order_items.selling_price');
        $query->orderBy('categories.name','ASC');
        $query->orderBy('products.part_number', 'ASC');

        $rows = $query->get();        

        $total_amount = 0;
        $total_tax = 0;
        $total_qty = 0;
        foreach($rows as $row)
        {
            $total_amount += ($row->selling_price * $row->quantity_ordered);
            $total_tax += ($row->selling_price * $row->quantity_ordered) * ($row->tax/100);
            $total_qty += $row->quantity_ordered;
        }
        
        $res['total_amount_unfmt'] = $total_amount;
        $res['total_amount'] =$fmt->formatCurrency($total_amount, "INR");
        $res['total_qty'] = $fmt->formatCurrency($total_qty, "INR");

        return $res;        
    }

    /*
        This function calculates based on Category
        Should have been called "calculateCategorySalesTotals"
    */
    public function calculateSalesTotals($period="", $year="", $month="_ALL", $quarter="_ALL", $category="_ALL")
    {
        $select_period = 'period_monthly';
        if (!empty($period))
            $select_period = $period;

        if (empty($month))
            $month = date('n');
            
        if (empty($year))
            $year = date('Y');
        
        if (empty($quarter))
            $quarter = 'Q1';

        $categories = Category::select('id', 'name');
        if ($category!='_ALL')
            $categories->where('id','=',$category);
        $categories = $categories->orderBy('name')
            ->get();

        $res = array();
        
        $totals = array();
        if ($select_period=='period_monthly')
        {
            for ($i=1;$i<=12;$i++)
            {
                $totals[$i] = 0;
            }
        }
        else {
            $totals['Q1'] = 0;
            $totals['Q2'] = 0;
            $totals['Q3'] = 0;
            $totals['Q4'] = 0;
        }
        
        foreach ($categories as $category)
        {

            if ($select_period=='period_monthly')
            {
                if ($month=="_ALL")
                {
                    $row_total = 0;

                    for ($i=1;$i<=12;$i++)
                    {
                        $dt = DateTime::createFromFormat('!m', $i);
                        $res[$category->name][$dt->format('F')] = $this->calculateMonthSalesTotals($i, $year, $category->id);
                        $row_total += (float)$res[$category->name][$dt->format('F')]['total_amount_unfmt'];

                        $totals[$i] += (float)$res[$category->name][$dt->format('F')]['total_amount_unfmt'];
                    }

                    $res[$category->name]['row_total'] = $row_total;
                }
                else
                {
                    $dt = DateTime::createFromFormat('!m', $month);
                    $res[$category->name][$dt->format('F')] = $this->calculateMonthSalesTotals($month, $year, $category->id);
                }
            }
            else
            {


                if ($quarter=="_ALL")
                {
                    $row_total = 0;
                    
                    $res[$category->name]['Q1'] = $this->calculateQuarterSalesTotals('Q1', $year, $category->id);
                    $row_total += (float)$res[$category->name]['Q1']['total_amount_unfmt'];
                    $totals['Q1'] += (float)$res[$category->name]['Q1']['total_amount_unfmt'];

                    $res[$category->name]['Q2'] = $this->calculateQuarterSalesTotals('Q2', $year, $category->id);
                    $row_total += (float)$res[$category->name]['Q2']['total_amount_unfmt'];
                    $totals['Q2'] += (float)$res[$category->name]['Q2']['total_amount_unfmt'];

                    $res[$category->name]['Q3'] = $this->calculateQuarterSalesTotals('Q3', $year, $category->id);
                    $row_total += (float)$res[$category->name]['Q3']['total_amount_unfmt'];
                    $totals['Q3'] += (float)$res[$category->name]['Q3']['total_amount_unfmt'];

                    $res[$category->name]['Q4'] = $this->calculateQuarterSalesTotals('Q4', $year, $category->id);
                    $row_total += (float)$res[$category->name]['Q4']['total_amount_unfmt'];
                    $totals['Q4'] += (float)$res[$category->name]['Q4']['total_amount_unfmt'];

                    $res[$category->name]['row_total'] = $row_total;
                }
                else
                    $res[$category->name][$quarter] = $this->calculateQuarterSalesTotals($quarter, $year, $category->id);
            }
        }

        $res['column_total'] = $totals;

        return $res;
    }


    /**
     * 
     * Calculate the sales totals per state
     * 
     */
    public function calculateMonthStateSalesTotals($month="", $year="", $state_id=0)
    {
        if (empty($month))
            $month = date('n');
            
        if (empty($year))
            $year = date('Y');

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
        $query->orderBy('categories.name','ASC');
        $query->orderBy('products.part_number', 'ASC');

        //return $query->toSql();
        $rows = $query->get();

        $total_amount = 0;
        $total_tax = 0;
        $total_qty = 0;
        foreach($rows as $row)
        {
            $total_amount += ($row->selling_price * $row->quantity_ordered);
            $total_tax += ($row->selling_price * $row->quantity_ordered) * ($row->tax/100);
            $total_qty += $row->quantity_ordered;
        }

        $res['total_amount_unfmt'] = $total_amount;
        $res['total_amount'] =$fmt->formatCurrency($total_amount, "INR");
        $res['total_qty'] = $fmt->formatCurrency($total_qty, "INR");

        return $res;
    }

    public function calculateQuarterStateSalesTotals($quarter="", $year="", $state_id=0)
    {
        if (empty($quarter))
            $quarter = 'Q1';
            
        if (empty($year))
            $year = date('Y');

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

        $query->groupBy('products.id', 'sale_order_items.selling_price');
        $query->orderBy('categories.name','ASC');
        $query->orderBy('products.part_number', 'ASC');

        //return $query->toSql();
        $rows = $query->get();

        $total_amount = 0;
        $total_tax = 0;
        $total_qty = 0;
        foreach($rows as $row)
        {
            $total_amount += ($row->selling_price * $row->quantity_ordered);
            $total_tax += ($row->selling_price * $row->quantity_ordered) * ($row->tax/100);
            $total_qty += $row->quantity_ordered;
        }

        $res['total_amount_unfmt'] = $total_amount;
        $res['total_amount'] = $fmt->formatCurrency($total_amount, "INR");
        $res['total_qty'] = $fmt->formatCurrency($total_qty, "INR");

        return $res;
    }

    public function calculateStateSalesTotals($period="", $year="", $month="_ALL", $quarter="_ALL", $state="_ALL")
    {
        $select_period = 'period_monthly';
        if (!empty($period))
            $select_period = $period;

        if (empty($month))
            $month = date('n');
            
        if (empty($year))
            $year = date('Y');
        
        if (empty($quarter))
            $quarter = 'Q1';

        $states = State::select('id', 'name');
        if ($state!='_ALL')
            $states->where('id','=',$state);
        $states = $states->orderBy('name')
            ->get();

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        //$fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $res = array();
        
        $totals = array();
        if ($select_period=='period_monthly')
        {
            for ($i=1;$i<=12;$i++)
            {
                $totals[$i] = 0;
            }
        }
        else {
            $totals['Q1'] = 0;
            $totals['Q2'] = 0;
            $totals['Q3'] = 0;
            $totals['Q4'] = 0;
        }

        foreach ($states as $row)
        {

            if ($select_period=='period_monthly')
            {
                if ($month=="_ALL")
                {
                    $row_total = 0;

                    for ($i=1;$i<=12;$i++)
                    {
                        $dt = DateTime::createFromFormat('!m', $i);
                        $res[$row->name][$dt->format('F')] = $this->calculateMonthStateSalesTotals($i, $year, $row->id);
                        $row_total += (float)$res[$row->name][$dt->format('F')]['total_amount_unfmt'];

                        $totals[$i] += (float)$res[$row->name][$dt->format('F')]['total_amount_unfmt'];
                    }

                    $res[$row->name]['row_total'] = $row_total; //$fmt->formatCurrency($row_total, "INR");

                }
                else
                {
                    $dt = DateTime::createFromFormat('!m', $month);
                    $res[$row->name][$dt->format('F')] = $this->calculateMonthStateSalesTotals($month, $year, $row->id);
                }
            }
            else
            {
                if ($quarter=="_ALL")
                {
                    $row_total = 0;
                    $res[$row->name]['Q1'] = $this->calculateQuarterStateSalesTotals('Q1', $year, $row->id);
                    $row_total += (float)$res[$row->name]['Q1']['total_amount_unfmt'];
                    $totals['Q1'] += (float)$res[$category->name]['Q1']['total_amount_unfmt'];

                    $res[$row->name]['Q2'] = $this->calculateQuarterStateSalesTotals('Q2', $year, $row->id);
                    $row_total += (float)$res[$row->name]['Q2']['total_amount_unfmt'];
                    $totals['Q1'] += (float)$res[$category->name]['Q1']['total_amount_unfmt'];

                    $res[$row->name]['Q3'] = $this->calculateQuarterStateSalesTotals('Q3', $year, $row->id);
                    $row_total += (float)$res[$row->name]['Q3']['total_amount_unfmt'];
                    $totals['Q1'] += (float)$res[$category->name]['Q1']['total_amount_unfmt'];

                    $res[$row->name]['Q4'] = $this->calculateQuarterStateSalesTotals('Q4', $year, $row->id);
                    $row_total += (float)$res[$row->name]['Q4']['total_amount_unfmt'];
                    $totals['Q1'] += (float)$res[$category->name]['Q1']['total_amount_unfmt'];
                    
                    $res[$row->name]['row_total'] = $row_total;
                }
                else
                    $res[$row->name][$quarter] = $this->calculateQuarterStateSalesTotals($quarter, $year, $row->id);
            }
        }

        $res['column_total'] = $totals;

        return $res;
    }


    /**
     * 
     * Calculate the totals per Sale Order
     * 
     */
    public function calculateTotals()
    {
        $tax = 0;
        $rate_per_kg = 0;

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        if (is_null($this->transport_charges))
            $this->transport_charges = 0;

        $this->sub_total = 0;
        $this->tax_total = 0;
        $this->freight_charges = (float)$this->transport_charges; 
        $this->transport_total = 0; // = freight_charges + with tax
        $this->total = 0;
        $this->total_unfmt = 0;
        $this->total_advance = 0;
        $this->balance_due = 0;


        if ($this->dealer->state->freight_zone_id)
        {
            $zone = FreightZone::find($this->dealer->state->freight_zone_id);
            $rate_per_kg = $zone->rate_per_kg;
        }


        /**
         * calculate the totals
         */
        $tax = 0;
        foreach ($this->items as $item)
        {
            $this->sub_total += $item->quantity_ordered * $item->selling_price;
            $this->tax_total += ($item->quantity_ordered * $item->selling_price) * ($item->tax / 100);
            /*
                this feature left out until finalized
                with Sunlit Future 
            */
            //$this->freight_charges += ($item->quantity_ordered * $item->product->weight_calculated) * $rate_per_kg;

            if ($item->tax > $tax)
                $tax = $item->tax;
        }

        /**
         * add the Transport Charges to the totals
         */
        $this->sub_total += (float)$this->transport_charges;
        $this->transport_tax_amount = (float)($this->transport_charges * $tax / 100);
        $this->transport_total = (float)$this->transport_charges + (float)$this->transport_tax_amount;
        $this->tax_total += (float)$this->transport_charges * $tax / 100;

        $this->total = $this->sub_total + $this->tax_total;

        $this->total = round($this->total);

        foreach ($this->sale_order_payments as $payment)
        {
            $this->total_advance += $payment->amount;
        }    

        $this->balance_due = $this->total - $this->total_advance;


        /**
         * for SGST and CGST
         */
        $this->tax_total_half = $this->tax_total/2;

        $this->total_spellout = $this->expandAmount($this->total);


        /*
            eventually these fields should be formated in 
            a get...Attribute function, like the
            total_advance and balance_due columns
        */
        $this->sub_total = $fmt->formatCurrency($this->sub_total, "INR");
        $this->tax_total = $fmt->formatCurrency($this->tax_total, "INR");
        $this->transport_total = $fmt->formatCurrency($this->transport_total, "INR");
        $this->transport_tax_amount_unfmt = (float)$this->transport_tax_amount;
        $this->transport_tax_amount = $fmt->formatCurrency((float)$this->transport_tax_amount, "INR");
        $this->tax_total_half = $fmt->formatCurrency($this->tax_total_half, "INR");
        $this->total_unfmt = $this->total;
        $this->total = $fmt->formatCurrency($this->total, "INR");

        return true;

    }


    static function expandAmount($amount) {

        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::SPELLOUT);

        if (strpos($amount,'.') !== false) {

            $numwords = explode('.',$amount);

            if (intval($numwords[1]) > 0)
                $res = $fmt->format($numwords[0]).' and paise '.$fmt->format($numwords[1]).' only';
            else
                $res = $fmt->format((int)$numwords[0]).' only';
        }
        else  {
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
        if ($this->blocked_at){
            $dt = Carbon::parse($this->blocked_at);
            return $dt->toFormattedDateString(); 
        } 
        return "";
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
        if ($this->booked_at)
        {
            $dt = Carbon::parse($this->booked_at);
            return $dt->toFormattedDateString();  
        }
        return "";
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
        if ($this->dispatched_at)
        {
            $dt = Carbon::parse($this->dispatched_at);
            return $dt->toFormattedDateString(); 
        } 
        return ""; 
    }

    public function setDispatchedAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['dispatched_at'] = $dt->toDateTimeString();  
    }

    /**
     * Returns the due_at date for display Month Day, Year
     */
    public function getDisplayDueAtAttribute()
    {
        if ($this->due_at)
        {
            $dt = Carbon::parse($this->due_at);
            return $dt->toFormattedDateString(); 
        } 
        return ""; 
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
        if ($this->delivered_at)
        {
            $dt = Carbon::parse($this->delivered_at);
            return $dt->toFormattedDateString();  
        } 
        return "";
    }

    public function setDeliveredAtAttribute($value)
    {
        $dt = Carbon::parse($value);
        $this->attributes['delivered_at'] = $dt->toDateTimeString();  
    }

    public function getDisplayStatusAttribute()
    {
        switch ($this->status)
        {
            case SaleOrder::DRAFT:
                $status =  '<span class="badge badge-secondary-lighten">Draft</span>';
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
            SaleOrder::BLOCKED => 'Blocked', 
            SaleOrder::BOOKED => 'Booked',
            SaleOrder::DISPATCHED => 'Dispatched'
            //SaleOrder::DELIVERED => 'Delivered'
        ];
    }

    public function getDisplayTotalAdvanceAttribute()
    {
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, ''); 
        return $this->total_advance = $fmt->formatCurrency($this->total_advance, "INR");       
    }

    public function getDisplayBalanceDueAttribute()
    {
        $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
        $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, ''); 
        return $this->balance_due = $fmt->formatCurrency($this->balance_due, "INR");       
    }

    public function isOverdue()
    {
        if (Carbon::now()->greaterThan(Carbon::parse( $this->due_at)))
        {
            return true;
        }
        return false;
    }

    public function getOrderedDaysAgoAttribute()
    {
        return Carbon::parse( $this->ordered_at)->diffForHumans();
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
