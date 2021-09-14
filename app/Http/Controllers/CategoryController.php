<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use \App\Http\Requests\StoreCategoryRequest;
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
        if ($user->can('list categories'))
            return view('categories.index');
    
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
        if ($length < 0)
            $categories = Category::where('name', 'like', '%'.$search.'%')
                ->select('id', 'name')
                ->orderBy($order_column, $order_dir)
                ->get(['id','name']);
        else
            $categories = Category::where('name', 'like', '%'.$search.'%')
                ->select('id', 'name')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();
               

        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $categories,
            'error' => null
        );

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = new Category();
        return view('categories.form', ['category' => $category]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $category = Category::create($validatedData);
        if ($category){
            return redirect(route('categories'))->with('success', trans('app.record_added', ['field' => 'category']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'category']));
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        if ($category){
            return view('categories.form', ['category' => $category]);
        }
        return view('categories.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreCategoryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCategoryRequest $request, $id)
    {
        $validatedData = $request->validated();
        $category = Category::whereId($id)->update($validatedData);
        if ($category){
            return redirect(route('categories'))->with('success', trans('app.record_edited', ['field' => 'category']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'category']));
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
        if ($user->can('delete categories')){
            Category::destroy($id);
            return redirect(route('categories'))->with('success', trans('app.record_deleted', ['field' => 'category']));
        }
        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Display a listing of the resource for select2
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function getListForSelect2(Request $request)
    {
        $query = Category::query();
        if ($request->has('q')){
            $query->where('name', 'like', $request->get('q').'%');
        }
        $categories = $query->select('id', 'name as text')->get();
        return ['results' => $categories];
    }     
}
