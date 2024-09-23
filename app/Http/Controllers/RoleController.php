<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource for select2
     *
     * @return json
     */
    public function getListForSelect2(Request $request): json
    {
        $allRoles = Role::all();
        $roles = [];
        foreach ($allRoles as $role) {
            $roles[] = ['id' => $role->id, 'text' => ucfirst($role->name)];
        }

        return ['results' => Arr::sort($roles)];
    }
}
