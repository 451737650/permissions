<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','isAdmin']);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
//      $pes = Permission::pluck('name');
//      echo $pes;
//        foreach($roles as $role){
//           echo $role->permissions()->pluck('name');
//        }
       // return;
        return view('roles.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();

        return view('roles.create',compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|unique:roles|max:10',
            'permissions'=>'required'
        ]);

        $name = $request['name'];
        $role = new Role();
        $role->name = $name ;
        $permissions = $request['permissions'];
        $role->save();
        //便利选中的权限
        foreach($permissions as $permission){
            $p = Permission::where('id','=',$permission)->firstOrFail();
            // 获取新创建的角色并分配权限
            $role = Role::where('name','=',$name)->first();
            $role->givePermissionTo($p);
        }
        return redirect()->route('roles.index')
            ->with('flash_message','创建成功');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        return view('roles.edit',compact('role','permissions'));
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
        $this->validate($request,[
            'name'=>'required|max:10|unique:roles,name,'.$id,
            'permissions'=>'required',
        ]);
        $role = Role::findOrFail($id);
        $input = $request->except('permissions');
        $permissions = $request['permissions'];
        $role->fill($input)->save();
        $p_all = Permission::all();
        foreach($p_all as $p){
            $role->revokePermissionTo($p);
        }

        foreach($permissions as $permission){
            //从数据库中获取相应权限
            $p = Permission::where('id','=',$permission)->firstOrFail();
            // 分配权限到角色
            $role->givePermissionTo($p);
        }
        return redirect()->route('roles.index')->with('flash_message','修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('roles.index')->with('flash_message','删除成功');
    }
}
