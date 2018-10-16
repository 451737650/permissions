<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Session;
// 引入 laravel-permission 模型
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','isAdmin']); // isAdmin 中间件让具备指定权限的用户才能访问该资源
    }
    /**
     * 显示用户
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('users.index',compact('users'));
    }

    /**
     * 用户角色表单
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 获取所有角色并将其传递到视图
        $roles = Role::get();
        return view('users.create',['roles'=>$roles]);

    }

    /**
     * 保存新增用户
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // 验证 name、email 和 password 字段
        $this->validate($request, [
            'name'=>'required|max:120',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6|confirmed'
        ]);

        $user = User::create($request->only('email', 'name', 'password')); // email、name、password 字段

        $roles = $request['roles'];
        // 检查是否某个角色被选中
        if (isset($roles)) {
            foreach ($roles as $role) {
                $role_r = Role::where('id', '=', $role)->firstOrFail();
                $user->assignRole($role_r);
            }
        }
        // 重定向到 users.index 视图并显示消息
        return redirect()->route('users.index')
            ->with('flash_message','用户添加成功');
    }

    /**
     * 查看用户
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('users');
    }

    /**
     * 显示编辑用户角色表单
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::get();
        return view('users.edit',compact('user','roles'));
    }

    /**
     * 更新用户信息
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $user = User::findOrFail($id); // 通过id获取给定角色

        // 验证 name, email 和 password 字段
        $this->validate($request, [
            'name'=>'required|max:120',
            'email'=>'required|email|unique:users,email,'.$id,
            'password'=>'required|min:6|confirmed'
        ]);
        $input = $request->only(['name', 'email', 'password']); // 获取 name, email 和 password 字段
        $roles = $request['roles']; // 获取所有角色
        $user->fill($input)->save();

        if (isset($roles)) {
            $user->roles()->sync($roles);  // 如果有角色选中与用户关联则更新用户角色
        } else {
            $user->roles()->detach(); // 如果没有选择任何与用户关联的角色则将之前关联角色解除
        }
        return redirect()->route('users.index')
            ->with('flash_message',
                '用户修改成功');
    }

    /**
     * 删除用户
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')
            ->with('flash_message','删除成功');
    }
}
