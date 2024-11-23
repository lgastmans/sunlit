<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SaleOrder;
use App\Models\Supplier;
use App\Models\Tax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

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
        if ($user->can('list products')) {
            return view('products.index');
        }

        return abort(403, trans('error.unauthorized'));
    }

    public function getListForDatatables(Request $request): JsonResponse
    {
        $draw = 1;
        if ($request->has('draw')) {
            $draw = $request->get('draw');
        }

        $start = 0;
        if ($request->has('start')) {
            $start = $request->get('start');
        }

        $length = 10;
        if ($request->has('length')) {
            $length = $request->get('length');
        }

        $order_column = 'name';
        $order_dir = 'ASC';
        $order_arr = [];
        if ($request->has('order')) {

            $order_arr = $request->get('order');
            $column_arr = $request->get('columns');
            $column_index = $order_arr[0]['column'];

            if ($column_index == 0) {
                $order_column = 'categories.name';
            } elseif ($column_index == 1) {
                $order_column = 'suppliers.company';
            } elseif ($column_index == 2) {
                $order_column = 'products.part_number';
            } else {
                $order_column = $column_arr[$column_index]['data'];
            }
            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Product::all()->count();

        /*
            build the query
        */
        $query = Product::query();

        $query->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
            ->join('taxes', 'taxes.id', '=', 'products.tax_id');

        if (! empty($column_arr[0]['search']['value'])) {
            $query->where('categories.name', 'like', '%'.$column_arr[0]['search']['value'].'%');
        }

        if (! empty($column_arr[1]['search']['value'])) {
            $query->where('suppliers.company', 'like', '%'.$column_arr[1]['search']['value'].'%');
        }

        if (! empty($column_arr[2]['search']['value'])) {
            $query->where('products.part_number', 'like', '%'.$column_arr[2]['search']['value'].'%');
        }

        if (! empty($column_arr[3]['search']['value'])) {
            $query->where('products.purchase_price', 'like', '%'.$column_arr[4]['search']['value'].'%');
        }

        if (! empty($column_arr[4]['search']['value'])) {
            $query->where('taxes.name', 'like', '%'.$column_arr[5]['search']['value'].'%');
        }

        if ($request->has('search')) {
            $search = $request->get('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('products.part_number', 'like', '%'.$search.'%')
                    ->orWhere('categories.name', 'like', '%'.$search.'%')
                    ->orWhere('suppliers.company', 'like', '%'.$search.'%');
            });
        }

        $query->orderBy($order_column, $order_dir);

        $totalRecordswithFilter = $query->count();

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $products = $query->select('products.*', 'categories.name as category_name', 'taxes.name as tax_name')->get();

        $arr = [];

        foreach ($products as $record) {

            $arr[] = [
                'id' => $record->id,
                'category' => $record->category->name,
                'supplier' => $record->supplier->company,
                'tax' => $record->tax->amount,
                'code' => $record->code,
                'name' => $record->name,
                'part_number' => $record->part_number,
                'purchase_price' => $record->purchase_price,

            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordswithFilter,
            'data' => $arr,
            'error' => null,
        ];

        return response()->json($response);
    }

    public function getExportList()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $product = new Product;

        return view('products.form', ['product' => $product]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData = Arr::except($validatedData, ['display_purchase_price']);
        $product = Product::create($validatedData);
        if ($product) {

            /*
                create initial stock inventory entries
            */
            $inventory = new Inventory;
            $inventory->stock_available = 0;
            $inventory->initStock($product->id);

            return redirect(route('products'))->with('success', trans('app.record_added', ['field' => 'product']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'product']));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = Auth::user();
        if ($user->can('view products')) {
            $product = Product::with(['inventory', 'inventory.warehouse', 'movement', 'supplier'])->find($id);
            $entry_filter = InventoryMovement::getMovementFilterList();
            $purchase_order_status = PurchaseOrder::getStatusList();
            $sale_order_status = SaleOrder::getStatusList();
            if ($product) {
                return view('products.show',
                    ['product' => $product,
                        'entry_filter' => $entry_filter,
                        'purchase_order_status' => $purchase_order_status,
                        'sale_order_status' => $sale_order_status,
                    ]);
            }

            return back()->with('error', trans('error.resource_doesnt_exist', ['field' => 'product']));
        }

        return abort(403, trans('error.unauthorized'));

    }

    public function getById($id, $warehouse_id = false)
    {
        $query = Product::query();

        if ($warehouse_id) {
            $query->join('inventories', 'inventories.product_id', '=', 'products.id');
            $query->where('inventories.warehouse_id', '=', $warehouse_id);
            $product = $query->where('product_id', '=', $id)->first();
        } else {
            $product = Product::with('tax')->find($id);
        }
        if ($product) {
            return $product;
        }

        return false;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $product = Product::withCount('purchase_order_item')->find($id);
        if ($product) {
            return view('products.form', ['product' => $product]);
        }

        return view('products.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProductRequest $request, int $id)
    {
        $validatedData = $request->validated();
        $validatedData = Arr::except($validatedData, ['display_purchase_price']);
        $product = Product::whereId($id)->update($validatedData);
        if ($product) {
            return redirect(route('products'))->with('success', trans('app.record_edited', ['field' => 'product']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'product']));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        if ($user->can('delete products')) {
            /*
                check if product present in purchase orders
            */
            $count = PurchaseOrderItem::where('product_id', $id)->count();

            if ($count > 0) {
                return redirect(route('products'))->with('error', trans('error.product_has_purchase_order_item'));
            }

            Product::destroy($id);

            return redirect(route('products'))->with('success', trans('app.record_deleted', ['field' => 'product']));
        }

        return abort(403, trans('error.unauthorized'));
    }

    public function getListPerSupplier($supplier, Request $request)
    {
        $query = Product::query();
        $query->where('supplier_id', '=', $supplier);
        if ($request->has('q')) {
            $query->where('part_number', 'like', '%'.$request->get('q').'%');
        }
        $products = $query->select('products.id', 'products.part_number as text', 'products.cable_length_input', 'products.cable_length_output', 'products.kw_rating')->get();

        return ['results' => $products];
    }

    public function getListPerWarehouse($warehouse, Request $request)
    {
        $query = Product::query();
        $query->with(['inventory']);
        $query->join('inventories', 'inventories.product_id', '=', 'products.id');
        $query->where('inventories.warehouse_id', '=', $warehouse);

        if ($request->has('q')) {
            $query->where('part_number', 'like', '%'.$request->get('q').'%')
                ->orWhere('notes', 'like', '%'.$request->get('q').'%');
        }
        $products = $query->select('products.id', 'products.part_number as text', 'products.notes as notes', 'inventories.stock_available as stock_available', 'inventories.stock_booked as stock_booked', 'inventories.stock_ordered as stock_ordered')->get();

        return ['results' => $products];
    }

    /**
     * Display a listing of the resource for select2
     */
    public function getListForSelect2(Request $request): array
    {
        $query = Product::query();
        if ($request->has('q')) {
            $query->where('part_number', 'like', '%'.$request->get('q').'%')
                ->orWhere('notes', 'like', '%'.$request->get('q').'%');
        }
        $products = $query->select('products.id', 'products.part_number as text', 'products.notes as notes')->get();

        return ['results' => $products];
    }

    /*
        import products csv file from public folder
    */
    public function importCsv(Request $request)
    {
        /*
            CSV file columns are (in order):
                serial no
                notes (description)
                part number
                stock
        */

        /*
            set manually:
                Inverter
                Power Optimizer
        */
        $category = 'Inverter'; //$request->query('category');

        $file = public_path('products-import.csv');

        $dataArr = $this->csvToArray($file);
        //dd($dataArr);

        /*
            Tax ID not specified in csv file
            so default to first row of tax table
        */
        $taxes = Tax::first();

        for ($i = 0; $i < count($dataArr); $i++) {
            // $categories = Category::firstOrCreate([
            //     'name' => $dataArr[$i]['category_id']
            // ]);
            /*
                category should be passed in the route
                /products/import/{category}
            */
            $categories = Category::where('name', '=', $category)->first();
            $dataArr[$i]['category_id'] = $categories->id;

            // $suppliers = Supplier::firstOrCreate([
            //     'company' => $dataArr[$i]['supplier_id'],
            //     'address' => 'address',
            //     'city' => 'city',
            //     'zip_code' => 'zip code',
            //     'phone' => 'phone',
            //     'currency' => 'inr',
            //     'country' => 'country'
            // ]);
            /*
                set to SolarEdge - this has to be in the db
            */
            $suppliers = Supplier::where('company', '=', 'SolarEdge')->first();
            $dataArr[$i]['supplier_id'] = $suppliers->id;

            $dataArr[$i]['tax_id'] = $taxes->id;

            /*
                as the 'stock' array key is not a column in Product
                it needs to be removed so that it can be saved
                by the firstOrCreate function below
            */
            $stock = $dataArr[$i]['stock'];
            unset($dataArr[$i]['stock']);

            /*
                save the product
            */
            $product = Product::firstOrCreate($dataArr[$i]);

            /*
                register initial stock
            */
            $inventory = new Inventory;
            $inventory->stock_available = $stock;
            $warehouse_id = $inventory->initStock($product->id);

            /*
                inventory movement as RECEIVED
            */
            $data = [
                'warehouse_id' => $warehouse_id,
                'product_id' => $product->id,
                'purchase_order_id' => null,
                'purchase_order_invoice_id' => null,
                'sales_order_id' => null,
                'quantity' => $stock,
                'user_id' => Auth::user()->id,
                'movement_type' => InventoryMovement::RECEIVED,
                'price' => 0,
            ];
            $movement = new InventoryMovement;
            $movement->updateMovement($data);
        }

        return true;
    }

    /*
        convert the csv file to array
    */
    public function csvToArray($filename = '', $delimiter = "\t")
    {
        if (! file_exists($filename) || ! is_readable($filename)) {
            return false;
        }

        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            $i = 0;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {

                if (strpos($row[0], 'UI') !== false) {
                    continue;
                } else {

                    $data[$i]['notes'] = $row[1];
                    $data[$i]['part_number'] = ($row[2] != '') ? $row[2] : 'no-part-number___'.time();
                    $data[$i]['stock'] = intval($row[3]);

                    /*
                    $data[$i]['code'] = $row[1];
                    $data[$i]['part_number'] = ($row[1] != "") ? $row[1] : "no-part-number___".time();
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

                    if ((empty($row[7])) || (is_null($row[7])))
                        $data[$i]['cable_length_input'] = 0;
                    else
                        $data[$i]['cable_length_input'] = $row[7];

                    if ((empty($row[8])) || (is_null($row[8])))
                        $data[$i]['cable_length_output'] = 0;
                    else
                        $data[$i]['cable_length_output'] = $row[8];

                    $data[$i]['kw_rating'] = $row[9];

                    if ((empty($row[10])) || (is_null($row[10])))
                        $data[$i]['weight_actual'] = 0;
                    else
                        $data[$i]['weight_actual'] = $row[10];

                    if ((empty($row[11])) || (is_null($row[11])))
                        $data[$i]['weight_volume'] = 0;
                    else
                        $data[$i]['weight_volume'] = $row[11];

                    if ((empty($row[12])) || (is_null($row[12])))
                        $data[$i]['weight_calculated'] = 0;
                    else
                        $data[$i]['weight_calculated'] = $row[12];

                    if ((empty($row[13])) || (is_null($row[13])))
                        $data[$i]['warranty'] = 0;
                    else
                        $data[$i]['warranty'] = $row[13];

                    $data[$i]['notes'] = $row[1];
                    $data[$i]['stock'] = intval($row[3]);
                    // as tax is not given in the csv file
                    // the tax_id is initialized in the importCSV function
                    $data[$i]['tax_id'] = 0;
                    $data[$i]['purchase_price'] = 0;
                    */

                    $i++;
                }
            }
            fclose($handle);
        }

        return $data;
    }
}
