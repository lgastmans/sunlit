<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\UserInvited;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            switch($column_index){
                case "3":
                    $order_column = "users.deleted_at";
                    break;
                default:
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
        $totalRecords = User::get()->count();

        // Fetch records
        if ($length < 0)
            $users = User::select('id', 'name', 'email', 'deleted_at')
                ->withTrashed()->where('name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->get();
        else
            $users = User::select('id', 'name', 'email', 'deleted_at')
                ->withTrashed()->where('name', 'like', '%'.$search.'%')
                ->orderBy($order_column, $order_dir)
                ->skip($start)
                ->take($length)
                ->get();
               
        $arr = array();
        foreach($users as $user){

            $user_role = '';
            try {
            if ($user->role !== null)
                $user_role = $user->role;
            } catch(\Exception $e) {
                $user_role = 'Not defined'; //$e->getMessage();
            }            

            $arr[] = array(
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "role" => (isset($user_role) ? $user_role : ''),
                "status" => ($user->deleted_at) ? 'disabled' : 'enabled'
            );
        }

        $response = array(
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $users->count(),
            "data" => $arr,
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
        $user = Auth::user();
        if ($user->hasRole('super-admin'))
        {
            $user = new User();
            return view('users.form', ['user' => $user]);
        }
        return abort(403, trans('error.unauthorized'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = new User();
        $validatedData = $request->validated();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($request->email.$request->name.\Carbon\Carbon::now()->timestamp);
        $user->invite_token = Str::random(40);
        $user->save();
        $user->syncRoles($validatedData['role']);

        // Send the email 
        Mail::to($user->email)->send(new UserInvited($user));
         
        return redirect(route('users'))->with('success', trans('app.record_added', ['field' => 'user']));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = User::find(Auth::user()->id);
        return view('users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::withTrashed()->find($id);
        if ($user){
            return view('users.form', ['user' => $user]);
        }
        return view('users.index');
    }

    public function editProfile(){
        $user = User::find(Auth::user()->id);
        if ($user){
            return view('users.profile-edit', ['user' => $user]);
        }
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
        $validatedData = $request->validated();
        $user = User::find($id);
        if ($user){
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->password)
                $user->password = Hash::make($request->password);
    
            $user->update();
            $user->syncRoles($validatedData['role']);
            if ($id != Auth::user()->id){
                return redirect(route('users'))->with('success', trans('app.record_edited', ['field' => 'user']));
            }
            return redirect(route('profile'))->with('success', trans('app.record_edited', ['field' => 'user']));
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

    /**
     * Enable the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function enable($id)
    {
        $user = Auth::user();
        if ($user->can('delete users')){
            $u = User::withTrashed()->find($id);
            if ($u){
                $u->restore();
                return redirect(route('users'))->with('success', trans('app.record_enabled', ['field' => 'user']));
            }   
        }
        return abort(403, trans('error.unauthorized'));
    }


        /**
     * Update the password of the specified resource
     * 
     * @param  string $email
     * @param  string $invite_token
     * @return \Illuminate\Http\Response
     */
    public function registration($email, $invite_token)
    {
        return view('auth.update-password-registration', compact('email','invite_token'));
    }

    /**
     * Update the password of the specified resource
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registrationPassword(Request $request)
    {
        $user = User::where('email', 'like', $request->email)->where('invite_token', 'like', $request->invite_token)->first();
        if ($user){
            $user->password = Hash::make($request->password);
            $user->save();
            $user->markEmailAsVerified();
        }
        return redirect()->route('login');
    }
}

