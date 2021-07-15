<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use \App\Http\Requests\StoreUserRequest;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $user = Auth::user();
        if ($user->can('list users'))
            return view('users.index');
    
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
        $totalRecords = User::get()->count();

    
        // Fetch records
        if ($length < 0)
            $users = User::where('name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get(['id','name']);
        else
            $users = User::where('name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get(['id','name', 'email', 'created_at']);
               
        $arr = array();

        foreach($users as $user){
            $arr[] = array(
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "role" => $user->role
            );
        }

        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $users->count(),
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
        $user = new User();
        return view('users.form', ['user' => $user]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create($validatedData);
        if ($user){
            return redirect(route('users'))->with('success', trans('app.record_added', ['field' => 'user']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_added', ['field' => 'user']));
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
        $user = User::find($id);
        if ($user){
            return view('users.form', ['user' => $user]);
        }
        return view('users.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUserRequest $request, $id)
    {
        $role = Role::findById($request->get('role'));
        $request->merge(['role_name'=>$role->name]);
        $validatedData = $request->validated();
        $user = User::find($id);
        if ($user){
            $user->update($validatedData);
            
            $user->removeRole($user->role);
            $user->assignRole($role);

            return redirect(route('users'))->with('success', trans('app.record_edited', ['field' => 'user']));
        }
        return back()->withInputs($request->input())->with('error', trans('error.record_edited', ['field' => 'user']));
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
        if ($user->can('delete users')){
            User::destroy($id);
            return redirect(route('users'))->with('success', trans('app.record_deleted', ['field' => 'user']));
        }
        return abort(403, trans('error.unauthorized'));
    }
}

