<?php
namespace App\Http\Controllers;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class TsController extends User
{
	public function index()
    {
    	//在表中添加数据
    	//$role = Role::create(['name'=>'writer']);
    	//$permission = Permission::create(['name'=>'edit articles']);
    	
    	//通过调用用户实例上的动态属性 permissions 获取用户所有权限：
    	$user = New User();
    	$permission = $user->permissions;
    	echo $permission;
    	return view('ts');
    }
}
?>