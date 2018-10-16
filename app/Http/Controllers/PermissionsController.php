<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Seesion;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','isAdmin']);
    }

    /**
     *显示权限列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();
        return view('permissions.index',compact('permissions'));
    }

    /**
     *添加权限页面
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::get();

        return view('permissions.create',compact('roles'));
    }

    /**
     *保存创建权限
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name'=>'required|max:40',
        ]);

        $name = $request['name'];
        $permission = new Permission();
        $permission->name = $name;

        $roles = $request['roles'];

        $permission->save();

        if (!empty($request['roles'])) { // 如果选择了角色
            foreach ($roles as $role) {
                $r = Role::where('id',$role)->firstOrFail(); // 将输入角色和数据库记录进行匹配
                $permission = Permission::where('name', $name)->first(); // 将输入权限与数据库记录进行匹配

                $r->givePermissionTo($permission);
            }
        }

        return redirect()->route('permissions.index')
            ->with('flash_message',
             'Permission'. $permission->name.' added!');

    }

    /**
     * 显示选定权限
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('permissions');

    }

    /**
     * 显示编辑权限表单
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        return view('permissions.edit',compact('permission'));
    }

    /**
     * 权限修改
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        //验证
        $this->validate($request,[
           'name'=>'required|max:40'
        ]);
        $input = $request->all();
        $permission->fill($input)->save();

        return redirect()->route('permissions.index')
            ->with('flash_message',$permission->name.'修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        //保护特定权限
        if($permission->name == "Administer roles & permissions"){
            return redirect()->route('permissions.index')->
            with('flash_message','此权限无法删除');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('flash_message','权限'.$permission->name.'已删除');

    }
}
