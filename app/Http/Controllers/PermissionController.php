<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = Permission::create(['name' => 'list tranport zones']);
        $permission->assignRole('user');
        $permission->assignRole('staff');
        $permission->assignRole('admin');
        $permission->assignRole('super-admin');

        $permission = Permission::create(['name' => 'view tranport zones']);
        $permission->assignRole('user');
        $permission->assignRole('staff');
        $permission->assignRole('admin');
        $permission->assignRole('super-admin');

        $permission = Permission::create(['name' => 'edit tranport zones']);
        $permission->assignRole('staff');
        $permission->assignRole('admin');
        $permission->assignRole('super-admin');

        $permission = Permission::create(['name' => 'delete tranport zones']);
        $permission->assignRole('admin');
        $permission->assignRole('super-admin');
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
    }
}
