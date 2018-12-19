<?php
namespace backend\models;
/**
* 后台角色管理
*/
class Role extends Common
{
	public function tableName ()
	{
		return '{{auth_group}}';
	}
	public function attributeLabels ()
	{
		return [
			'title'	=>	'角色名称',
			'pid'	=>	'父级角色',
			'status'=>	'状态',
			'rules'	=>	'权限'
		];
	}
	public function rules ()
	{
		return [
			[['title'],'required','on'	=>	[self::SCENARIO_ADD,self::SCENARIO_EDIT],'message' => "请选择或填写{attribute}"],
		];
	}
	public function scenarios ()
	{
		return [
			self::SCENARIO_ADD	=>	['title','pid','status','rules','add_uid'],
			self::SCENARIO_EDIT	=>	['title','pid','status','rules','history']
		];
	}
	public function add ($data)
	{
		if (false == $this->checkMyValidate($data,self::SCENARIO_ADD)) return false;
		if (false == $this->save(false)) return $this->error('添加角色失败');
		return $this->id;
	}
	public function edit ($roleId,$data)
	{
		if (!$roleId || !$roleModel = self::findOne($roleId)) return $this->error('角色不存在');

		if (false == $roleModel->checkMyValidate($data,self::SCENARIO_EDIT)) return $this->error($roleModel->getError());

		if (false == $roleModel->save(false)) return $this->error('修改角色信息失败');
	}
	public function checkMyValidate (&$data,$scenario = null)
	{
		$data['add_uid'] = User::getUserInfo('id');
		if ($scenario != self::SCENARIO_ADD) {
			$data = $this->addHistory($data);
		}
		return parent::checkMyValidate($data,$scenario);
	}
	/**
	 * 添加历史记录
	 * @DateTime 2018-08-03T17:52:01+0800
	 * @param    [type]                   $data 修改的数据
	 */
	public function addHistory ($data)
	{
		if (!$data) return $data;

		$add = false;
		$checkFields = ['rules','title','pid'];

		foreach ($checkFields as $field) {
			if (!$add && isset($data[$field]) && $data[$field] != $this->$field) {
				$add = true;
				break;
			}
		}
		if (!$add) return $data;
		
		$history = json_decode($this->history,true)?:[];
		$data['history'] = json_encode(array_slice(array_merge($history,[$this->attributes]),0,50));
		return $data;
	}
}	