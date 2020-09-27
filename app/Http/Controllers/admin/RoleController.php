<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Permission;
use App\Admin\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::OrderBy('id' , 'ASC')->paginate(25);
        return view('admin.roles.all' ,  compact('roles'))->with('title' , 'Admin User Groups');
    }

    public function create()
    {
        $permissionList = Permission::pluck('label' , 'id');
        return view('admin.roles.create' , compact('permissionList'));
    }

    public function store(Request $request)
    {
        $this->validate($request , [
           'permission_id' => 'required',
            'name' => 'required',
            'label' => 'required'
        ]);

        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permission_id'));

        return redirect(route('roles.index'));
    }

    public function show(Role $role)
    {
        //
    }

    public function edit(Role $role)
    {
        $permissionList = Permission::pluck('label' , 'id');
        if( count($role->permissions)) {
            $selectedCategory = $role->permissions->pluck('id');
        } else {
            $selectedCategory = '';
        }
        return view('admin.roles.edit' , compact('role' , 'permissionList' , 'selectedCategory'));
    }

    public function update(Request $request, Role $role)
    {
        $this->validate($request , [
            'permission_id' => 'required',
            'name' => 'required',
            'label' => 'required'
        ]);

        $role->update($request->all());
        $role->permissions()->sync($request->input('permission_id'));

        return redirect(route('roles.index'));
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect(route('roles.index'));
    }
}
