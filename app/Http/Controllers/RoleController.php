<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
        /**
     * Display a listing of the resource for select2
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function getListForSelect2(Request $request)
    {
        $allRoles = Role::all();
        $roles = [];
        foreach($allRoles as $role){
            $roles[] = array('id' => $role->id, 'text'=> ucfirst($role->name));
        }
      
        return ['results' => Arr::sort($roles)];
    }

}
