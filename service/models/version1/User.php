<?php
namespace service\models\version1;

/**
* 后台用户管理
*/
class User extends Base
{
	public static function tableName () 
	{
		return "{{m_user}}";
	}

}