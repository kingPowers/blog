<?php
namespace backend\models;

/**
* 菜单管理
*/
class Menu extends Common
{
	const ENABLE_STATUS = 1;const ENABLE_STATUS_NAME = "启用";
	const DISABLE_STATUS = 2;const DISABLE_STATUS_NAME = "禁用";
	const MENU_TYPE = 1;const MENU_TYPE_NAME = "菜单";
	const FUNC_TYPE = 2;const FUNC_TYPE_NAME = "功能";
	public $action;
	public $module;
	public $whereFilter = [
		'id'		=>	['=',"id","_value_"],
		'status'	=>	['=','status',"_value_"],
		'type'		=>	['=','type','_value_'],
		'pid'		=>	['=','pid',"_value_"],
	];
	public static function tableName ()
	{
		return "{{auth_rule}}";
	}
	public function attributeLabels ()
	{
		return ['title'=>"名称",'module' => "Model",'pid' => "上级导航",'status' => '状态','type' => "类型"];
	}
	public function rules ()
	{
		return [
			[['title','module','pid','type','status'],'required','on' => [self::SCENARIO_ADD,self::SCENARIO_EDIT],"message" => "{attribute}不能为空"],
			[['status','type'],'checkAttributes',"on" => [self::SCENARIO_ADD,self::SCENARIO_EDIT]],
		];
	}
	public function checkAttributes($attribute,$params)
	{
		if ($attribute == 'status' && (!self::getStatus($this->status))) {
			$this->addError($attribute,"菜单状态错误");
		} elseif ($attribute == 'type' && (!self::getType($this->type))) {
			$this->addError($attribute,"菜单类型错误");
		}
		
	}
	public function scenarios ()
	{
		return [
			self::SCENARIO_ADD =>	['title','name','pid','type','status','module','action'],
			self::SCENARIO_EDIT =>	['title','name','pid','type','sort','status','module','action'],
		];
	}
	/**
	 * 添加菜单
	 * @param array $data 菜单信息
	 */
	public function add($data)
	{
		if (false == $this->checkMyValidate($data,self::SCENARIO_ADD)) return false;
		$this->name = $this->action?($this->module . "-" . $this->action):$this->module;
		if ($menuModel = self::findOne(['name' => $this->name])) return $this->error("该菜单已经创建完成，禁止重复创建");
		if (false == $this->save()) return $this->error("菜单添加失败");
		return $this->id;
	}
	/**
	 * 编辑菜单信息
	 * @param  string | int $menuid 菜单id
	 * @param  [type] $data   编辑信息
	 * @return [type]         [description]
	 */
	public function edit ($menuid,$data)
	{
		if (empty($menuid) || (false == ($menuModel = self::findOne($menuid)))) return $this->error("菜单id错误");
		if (false == $this->checkMyValidate($data,self::SCENARIO_EDIT)) return false;
		$data['name'] = $this->action?($this->module . "-" . $this->action):$this->module;
		$menuModel->scenario = self::SCENARIO_EDIT;//需要设置场景，否则不能执行块赋值
		$menuModel->attributes = $data;
		if (false == $menuModel->save(false))return $this->error("菜单修改失败");
		return true;
	}
	public function checkMyValidate (&$data,$scenario = NULL)
	{
		if ($data['pid'] && empty($data['action'])) 
			return $this->error("请填写action");
		return parent::checkMyValidate($data,$scenario);
	}
	/**
	 * 获取系统可用菜单
	 * @return [type] [description]
	 */
	public function getIndexMenu ()
	{
		$this->where = ['status' => self::ENABLE_STATUS,'type' => self::MENU_TYPE];
		$params['order'] = "pid ASC,sort ASC";
		$list = $this->allList('',$params);
		$return = $this->classifyMenu ($list);
		foreach ($return as $key => $value) {
			if (empty($value['child']))unset($return[$key]);
		}
		return $return;
	}
	public function menuList ()
	{
		$allList = $this->allList();
		return $this->classifyMenu($allList);
	}
	/**
	 * 给菜单列表归类分组
	 * @param  array $list 菜单列表
	 * @return array       [description]
	 */
	public function classifyMenu ($list)
	{
		if (empty($list))return [];

		$return = [];

		foreach ($list as $var) {
			$var['name'] = strtolower($var['name']);

            if ($var['pid'] == 0) {
                $return[$var['id']] = $var;
            } else {
                $return[$var['pid']]['child'][] = $var;
            }

        }

        return $return;
	}
	/**
	 * 获取菜单类型数组或者类型名
	 * @param  string || int $type 类型
	 * @return [type]         [description]
	 */
	static function getType($type = '')
	{
		$arr = [
			self::MENU_TYPE => self::MENU_TYPE_NAME,
			self::FUNC_TYPE =>	self::FUNC_TYPE_NAME,
		];
		return $type?$arr[$type]:$arr;
	}
}