<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrderInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('list categories')) {
            return view('categories.index');
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
            $order_column = $column_arr[$column_index]['data'];
            $order_dir = $order_arr[0]['dir'];
        }

        $search = '';
        if ($request->has('search')) {
            $search_arr = $request->get('search');
            $search = $search_arr['value'];
        }

        // Total records
        $totalRecords = Category::count();
        $totalRecordswithFilter = Category::where('name', 'like', '%'.$search.'%')->count();

        // Fetch records
        if ($length < 0) {
            $categories = Category::where('name', 'like', '%'.$search.'%')
                ->select('id', 'name', 'hsn_code', 'customs_duty', 'social_welfare_surcharge', 'igst')
                ->orderBy($order_column, $order_dir)
                ->get(['id', 'name']);
        } else {
            $categories = Category::where('name', 'like', '%'.$search.'%')
                ->select('id', 'name', 'hsn_code', 'customs_duty', 'social_welfare_surcharge', 'igst')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordswithFilter,
            'data' => $categories,
            'error' => null,
        ];

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $category = new Category;

        return view('categories.form', ['category' => $category]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $category = Category::create($validatedData);
        if ($category) {
            return redirect(route('categories'))->with('success', trans('app.record_added', ['field' => 'category']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'category']));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $category = Category::find($id);
        if ($category) {
            return view('categories.form', ['category' => $category]);
        }

        return view('categories.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCategoryRequest $request, int $id)
    {
        $validatedData = $request->validated();
        $category = Category::whereId($id)->update($validatedData);
        if ($category) {
            return redirect(route('categories'))->with('success', trans('app.record_edited', ['field' => 'category']));
        }

        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'category']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        if ($user->can('delete categories')) {
            /*
                check if tax present in products
            */
            $count = Product::where('category_id', $id)->count();

            if ($count > 0) {
                return redirect(route('categories'))->with('error', trans('error.category_has_product'));
            }

            Category::destroy($id);

            return redirect(route('categories'))->with('success', trans('app.record_deleted', ['field' => 'category']));
        }

        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Display a listing of the resource for select2
     */
    public function getListForSelect2(Request $request): json
    {
        $query = Category::query();
        if ($request->has('q')) {
            $query->where('name', 'like', $request->get('q').'%');
        }
        $categories = $query->select('id', 'name as text')->get();

        return ['results' => $categories];
    }

    public function getCategory(Request $request)
    {
        if ($request->has('po_item_id')) {

            $po_item_id = $request->get('po_item_id');

            $po_item = PurchaseOrderInvoiceItem::find($po_item_id);

            $product = Product::find($po_item->product_id);

            $category = Category::find($product->category_id);

            if ($category) {
                $charges = [
                    'customs_duty' => $category->customs_duty,
                    'social_welfare_surcharge' => $category->social_welfare_surcharge,
                    'igst' => $category->igst,
                ];
                $po_item->charges = $charges;
                $po_item->update();
            }

            $po_item->updateInvoiceItemCharges($po_item_id);

            return $category;

        }

        return false;
    }
}
