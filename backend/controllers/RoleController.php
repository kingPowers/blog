<?php
namespace backend\controllers;
/**
* 角色管理
*/
class RoleController extends CommonController
{
	public $roleTitle = [
		'title'	=>	'角色名',
		'p_role'	=>	'上级角色',
		'status_name'	=>	'状态',
		'add_user'	=>	'添加人员',
		'authority'	=>	'权限',
		'timeadd'	=>	'添加时间',
		'lasttime'	=>	'修改时间'
	];
	
}