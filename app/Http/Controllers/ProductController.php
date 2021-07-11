<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('products.index');
    }


    public function getProducts(Request $request)
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
            if ($column_index==1)
                $order_column = "categories.name";
            elseif ($column_index==2)
                $order_column = "suppliers.company";
            else
                $order_column = $column_arr[$column_index]['data'];
            $order_dir = $order_arr[0]['dir'];
        }

//dd($order_column);



        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Product::all()->count();
        $totalRecordswithFilter = Product::where('products.code', 'like', '%'.$search.'%')
            ->orWhere('products.name', 'like', '%'.$search.'%')
            ->get()
            ->count();

        // Fetch records
        if ($length < 0)
            $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
                ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
                ->join('taxes', 'taxes.id', '=', 'products.tax_id')
                ->where('products.code', 'like', '%'.$search.'%')
                ->orWhere('products.name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get(['products.*', 'categories.name as category_name', 'taxes.name as tax_name']);
        else
            $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
                ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
                ->join('taxes', 'taxes.id', '=', 'products.tax_id')
                ->where('products.code', 'like', '%'.$search.'%')
                ->orWhere('products.name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get(['products.*', 'categories.name as category_name', 'taxes.name as tax_name']);

        $arr = array();

        foreach($products as $record)
        {

            $arr[] = array(
                "id" => $record->id,
                "category" => $record->category->name,
                "supplier" => $record->supplier->company,
                "tax" => $record->tax->display_amount,
                "code" => $record->code,
                "name" => $record->name,
                "model" => $record->model,
                "cable_length" => $record->cable_length,
                "kw_rating" => $record->kw_rating,
                "part_number" => $record->part_number,
                "notes" => $record->notes

            );
        }

        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $arr,
            'error' => null
        );

        
        echo json_encode($response);

        exit;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        //return view('products.show',compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
dd($product);

        // if ($product){
        //     return view('products.form', ['product' => $product]);
        // }
        // return view('products.index');
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
        //
        // $request->validate([
        //     'title' => 'required',
        //     'description' => 'required',
        // ]);

        // $product->update($request->all());

        // return redirect()
        //     ->route('products.index')
        //     ->with('success','Product updated successfully');        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        // $product->delete();

        // return redirect()->route('products.index')
        //     ->with('success','Product deleted successfully');
    }
}
