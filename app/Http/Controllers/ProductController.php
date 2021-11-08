<?php

namespace App\Http\Controllers;


use \NumberFormatter;

use App\Models\Dealer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\SaleOrder;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Tax;
use App\Models\Supplier;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Exports\ProductsExport;

use App\Models\InventoryMovement;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use \App\Http\Requests\StoreProductRequest;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list products'))
            return view('products.index');
    
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

        $order_column = 'name';
        $order_dir = 'ASC';
        $order_arr = array();
        if ($request->has('order')) {

            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];

            if ($column_index==0)
                $order_column = "categories.name";
            elseif ($column_index==1)
                $order_column = "suppliers.company";
            elseif ($column_index==2)
                $order_column = "taxes.name";
            else
                $order_column = $column_arr[$column_index]['data'];
            $order_dir = $order_arr[0]['dir'];
        }


        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Product::all()->count();
        
        // $totalRecordswithFilter = Product::join('categories', 'categories.id', '=', 'products.category_id')
        //     ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
        //     ->join('taxes', 'taxes.id', '=', 'products.tax_id')
        //     ->where('products.code', 'like', '%'.$search.'%')
        //     ->orWhere('products.name', 'like', '%'.$search.'%')
        //     ->get()
        //     ->count();

        /*
            build the query
        */
        $query = Product::query();

        $query->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
            ->join('taxes', 'taxes.id', '=', 'products.tax_id');


        if (!empty($column_arr[0]['search']['value'])){
            $query->where('categories.name', 'like', '%'.$column_arr[0]['search']['value'].'%');
        }
       
        if (!empty($column_arr[1]['search']['value'])) {
            $query->where('suppliers.company', 'like', '%'.$column_arr[1]['search']['value'].'%');
        }

        if (!empty($column_arr[2]['search']['value'])) {
            $query->where('products.part_number', 'like', '%'.$column_arr[2]['search']['value'].'%');
        }

        if (!empty($column_arr[3]['search']['value'])) {
            $query->where('products.purchase_price', 'like', '%'.$column_arr[4]['search']['value'].'%');
        }

        if (!empty($column_arr[4]['search']['value'])) {
            $query->where('taxes.name', 'like', '%'.$column_arr[5]['search']['value'].'%');
        }

        if ($request->has('search')){
            $search = $request->get('search')['value'];
            $query->where( function ($q) use ($search){
                $q->where('products.code', 'like', '%'.$search.'%')
                    ->orWhere('products.name', 'like', '%'.$search.'%')
                    ->orWhere('categories.name', 'like', '%'.$search.'%')
                    ->orWhere('suppliers.company', 'like', '%'.$search.'%');
            });    
        }

        $query->orderBy($order_column, $order_dir);

        $totalRecordswithFilter = $query->count();

        if ($length > 0)
            $query->skip($start)->take($length);

        $products = $query->select('products.*', 'categories.name as category_name', 'taxes.name as tax_name')->get();

        $arr = array();

        foreach($products as $record)
        {

            $arr[] = array(
                "id" => $record->id,
                "category" => $record->category->name,
                "supplier" => $record->supplier->company,
                "tax" => $record->tax->amount,
                "code" => $record->code,
                "name" => $record->name,
                "part_number" => $record->part_number,
                "purchase_price" => $record->purchase_price

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


    public function getExportList()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Product();
        return view('products.form', ['product' => $product]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData = Arr::except($validatedData, array('display_purchase_price'));
        $product = Product::create($validatedData);
        if ($product){

            /*
                create initial stock inventory entries
            */
            $inventory = new Inventory();
            $inventory->initStock($product->id);            

            return redirect(route('products'))->with('success', trans('app.record_added', ['field' => 'product']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'product']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        if ($user->can('view products')){
            $product = Product::with(['inventory', 'inventory.warehouse', 'movement', 'supplier'])->find($id);
            $entry_filter = InventoryMovement::getMovementFilterList();
            $purchase_order_status = PurchaseOrder::getStatusList();
            $sale_order_status = SaleOrder::getStatusList();
            if ($product)
                return view('products.show',
                ['product'=>$product, 
                'entry_filter' => $entry_filter, 
                'purchase_order_status' => $purchase_order_status,
                'sale_order_status' => $sale_order_status,
            ]);

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'product']));
        }
        return abort(403, trans('error.unauthorized'));

    }

    public function getById($id, $warehouse_id = false)
    {
        $query = Product::query();

        if ($warehouse_id){
            $query->join('inventories', 'inventories.product_id', '=', 'products.id');
            $query->where('inventories.warehouse_id', '=', $warehouse_id);
            $product = $query->where('product_id', '=', $id)->first();
        }
        else{
            $product = Product::with('tax')->find($id);
        }
        if ($product)
            return $product;

        return false;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::withCount('purchase_order_item')->find($id);
        if ($product){
            return view('products.form', ['product' => $product]);
        }
        return view('products.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProductRequest $request, $id)
    {
        $validatedData = $request->validated();
        $validatedData = Arr::except($validatedData, array('display_purchase_price'));
        $product = Product::whereId($id)->update($validatedData);
        if ($product){
            return redirect(route('products'))->with('success', trans('app.record_edited', ['field' => 'product']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'product']));
  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->can('delete products')){
            /*
                check if product present in purchase orders
            */
            $count = PurchaseOrderItem::where('product_id', $id)->count();

            if ($count > 0)
                return redirect(route('products'))->with('error', trans('error.product_has_purchase_order_item'));

            Product::destroy($id);
            return redirect(route('products'))->with('success', trans('app.record_deleted', ['field' => 'product']));
        }
        return abort(403, trans('error.unauthorized'));
    }


    public function getListPerSupplier($supplier, Request $request)
    {
        $query = Product::query();
        $query->where('supplier_id', '=', $supplier);
        if ($request->has('q')){
            $query->where('code', 'like', '%'.$request->get('q').'%');
        }
        $products = $query->select('products.id', 'products.code as text')->get();
        return ['results' => $products];  
    }

    public function getListPerWarehouse($warehouse, Request $request)
    {
        $query = Product::query();
        $query->with(['inventory']);
        $query->join('inventories', 'inventories.product_id', '=', 'products.id');
        $query->where('inventories.warehouse_id', '=', $warehouse);

        if ($request->has('q')){
            $query->where('code', 'like', '%'.$request->get('q').'%');
        }
        $products = $query->select('products.id', 'products.code as text')->get();
        return ['results' => $products];  
    }


    /**
    * Display a listing of the resource for select2
    *
    * @return json
    */
    public function getListForSelect2(Request $request)
    {
        $query = Product::query();
        if ($request->has('q')){
            $query->where('code', 'like', '%'.$request->get('q').'%');
        }
        $products = $query->select('products.id', 'products.code as text')->get();
        return ['results' => $products];
    }

    /*
        import products csv file from public folder
    */
    public function importCsv()
    {
        /*
            File columns are (in order):

            Unique Code 
            Part Number 
            Description 
            Category    
            Supplier    
            Model   
            Min Qty
            "Cable Length (input in m)"
            "Cable Length (output in m)"
            KW Rating
            Act Wt(kg)
            Vol Wt(kg)
            Cal Wt
            Std Warranty(yrs)
            Notes
        */

        $file = public_path('products-import.csv');

        $dataArr = $this->csvToArray($file);
        //dd($dataArr);

        /*
            Tax ID not specified in csv file
            so default to first row of tax table
        */
        $taxes = Tax::first();

        for ($i = 0; $i < count($dataArr); $i ++)
        {
            $categories = Category::firstOrCreate([
                'name' => $dataArr[$i]['category_id']
            ]);
            $dataArr[$i]['category_id'] = $categories->id;

            $suppliers = Supplier::firstOrCreate([
                'company' => $dataArr[$i]['supplier_id'],
                'address' => 'address',
                'city' => 'city',
                'zip_code' => 'zip code',
                'phone' => 'phone',
                'currency' => 'inr',
                'country' => 'country'
            ]);
            $dataArr[$i]['supplier_id'] = $suppliers->id;

            $dataArr[$i]['tax_id'] = $taxes->id;

            $product = Product::firstOrCreate($dataArr[$i]);

            $inventory = new Inventory();
            $inventory->initStock($product->id); 
        }

        return true;    
    }

    /*
        convert the csv file to array
    */
    function csvToArray($filename = '', $delimiter = "\t")
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            $i=0;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {

                if (strpos($row[0], 'UI') !== false)
                    continue;
                else {
                    $data[$i]['code'] = $row[1];
                    $data[$i]['part_number'] = $row[1];
                    $data[$i]['name'] = $row[1];
                    // the column "category_id" is set to its corresponding
                    // id in the importCSV function
                    $data[$i]['category_id'] = $row[3];
                    // ditto column "supplier_id"
                    $data[$i]['supplier_id'] = $row[4];
                    $data[$i]['model'] = $row[5];
                    if ((empty($row[6])) || (is_null($row[6])))
                        $data[$i]['minimum_quantity'] = 0;
                    else
                        $data[$i]['minimum_quantity'] = $row[6];
                    $data[$i]['cable_length_input'] = $row[7];
                    $data[$i]['cable_length_output'] = $row[8];
                    $data[$i]['kw_rating'] = $row[9];
                    $data[$i]['weight_actual'] = $row[10];
                    $data[$i]['weight_volume'] = $row[11];
                    $data[$i]['weight_calculated'] = $row[12];
                    $data[$i]['warranty'] = $row[13];
                    $data[$i]['notes'] = $row[2];
                    // as tax is not given in the csv file
                    // the tax_id is initialized in the importCSV function
                    $data[$i]['tax_id'] = 0;
                    $data[$i]['purchase_price'] = 0;

                    $i++;
                }
            }
            fclose($handle);
        }

        return $data;
    }

}
