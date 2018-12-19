<?php
namespace backend\models;

/**
* 标签管理
*/
class Label extends Common
{
	const HOT_STATUS = 2;const HOT_STATUS_NAME = "热门";
	const UNHOT_STATUS = 1;const UNHOT_STATUS_NAME = "非热门";
	public $whereFilter = [
		'status'	=>	['=','status','_value_'],
		'id'		=>	['=','id','_value_'],
		'pid'		=>	['=','pid','_value_'],
		'name'		=>	['like','name',"%_value_%",false],
		'hot'	=>	['=',"is_hot","_value_"],
	];
	public static function tableName ()
	{
		return "{{label}}";
	}
	public function attributeLabels ()
	{
		return [
			'name'	=>	"标签名",
			'status'=>	'状态',
			'is_hot'=>	"热门状态",
			'intro'	=>	'标签介绍'
		];
	}
	public function rules ()
	{
		return [
			[['name','status','is_hot','intro'],'required','on' => [self::SCENARIO_ADD,self::SCENARIO_EDIT],'message' => "请填写或选择{attribute}"],	
		];
	}
	public function scenarios ()
	{
		return [
			self::SCENARIO_ADD => ['name','status','is_hot','pid','level','intro'],
			self::SCENARIO_EDIT	=>	['name','status','is_hot','pid','level','intro']
		];
	}
	public function add ($data)
	{
		if (false == $this->checkMyValidate($data,self::SCENARIO_ADD))return false;
		if (false == $this->save(false))return $this->error("标签添加失败");
		return $this->id;
	}
	/**
	 * 编辑修改标签
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-21T11:49:15+0800
	 * @param    [type]                   $labelid [description]
	 * @param    [type]                   $data    [description]
	 * @return   [type]                            [description]
	 */
	public function edit ($labelid,$data)
	{
		if (empty($labelid) || (false == ($labelModel = self::findOne($labelid))))return $this->error("labelid错误");
		if (false == $this->checkMyValidate($data,self::SCENARIO_EDIT))return false;
		$labelModel->scenario = self::SCENARIO_EDIT;
		$labelModel->attributes = $data;
		if (false == $labelModel->save(false))return $this->error("标签修改失败");
		return true;
	}
	public function checkMyValidate(&$data,$scenario = null)
	{
		if (!$data['pid']) {
			$data['level'] = 0;
		} else {
			$p_label = $this->getByid($data['pid']);
			$data['level'] = $p_label['level'] . $data['pid'];
		}
		if (substr($data['level'], 0, 1) != '-') {
			$data['level'] = '-' . $data['level'];
		}

		if (substr($data['level'], -1, 1) != '-') {
			$data['level'] = $data['level'] . '-';
		}
		return parent::checkMyValidate($data,$scenario);
	}
	/**
	 * 获取分类号的标签
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-21T11:49:54+0800
	 * @return   [type]                   [description]
	 */
	// public function getClassifyLabelList()
	// {
	// 	$this->where['status'] = self::ENABLE_STATUS;
	// 	$list = $this->allList();
	// 	$list = $this->classifyLabel($list);
	// 	return $list;
	// }
	/**
	 * 标签按梯次 归类
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-21T15:23:52+0800
	 * @param    [type]                   $data  [description]
	 * @param    integer                  $pid   [description]
	 * @param    integer                  $depth [description]
	 * @return   [type]                          [description]
	 */
	public static function classifyLabel($data,$pid = 0,$depth = 1)
	{
		if (empty($data)) return [];
		$return = [];
		foreach ($data as $key => $value) {
			$value['level'] = trim($value['level'],'-');
			if ($value['pid'] == $pid) {
				$value['name'] = str_repeat('----',count(explode('-', $value['level']))-1) . $value['name'] ;
				$return[] = $value;
				unset($data[$key]);
				if ($data && $child = self::classifyLabel($data,$value['id'],$depth++))
					$return = array_merge($return,$child);
			}
		}
		return $return;
	}
	public static function selectTree ($condition = '1=1')
	{
		$query = self::find()->where($condition)->orderBy('id');
		$data = $query->asArray()->all();//dump($data);
		$list = self::classifyLabel($data);//dump($list);
		$return = [];
		foreach ($list as $key => $value) {
			$return[$value['id']] = $value['name'];
		}
		return $return;
	}
	/**
	 * 获取是否热门状态
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-21T11:49:00+0800
	 * @param    string                   $status [description]
	 * @return   [type]                           [description]
	 */
	public static function getHotStatus ($status = '')
	{
		$statusArr = [
			self::HOT_STATUS 	=>	self::HOT_STATUS_NAME,
			self::UNHOT_STATUS 	=>	self::UNHOT_STATUS_NAME,
		];
		return $status?$statusArr[$status]:$statusArr;
	}
	// public static function labelIdNames ($labelid = '',$status = self::ENABLE_STATUS)
	// {
	// 	$labelModel = new Label();
	// 	if ($status) $labelModel->where['status'] = $status; 
	// 	$labelList = $labelModel->allList();
	// 	$labelList = self::classifyLabel($labelList);

	// 	$return = [];
	// 	foreach ($labelList as $value) {
	// 		$return[$value['id']] = $value['name'];
	// 	}
	// 	return $labelid?$return[$labelid]:$return;
	// }
	public function handleValue($labelInfo)
	{
		if (!$labelInfo) return [];
		$labelInfo['level'] = trim($labelInfo['level'],'-');
		return $labelInfo;
	}
	/**
	 * 获取某个标签以及此标签下的所有子标签
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-29T10:29:09+0800
	 * @param    [type]                   $labelid [description]
	 * @return   [type]                            [description]
	 */
	// public function getLabelTree ($labelid,$condition = '1=1')
	// {
	// 	if (!$labelid || !$labelInfo = $this->getByid($labelid)) return [];

	// 	$this->where = [
	// 		'status' => self::ENABLE_STATUS,
	// 	];
	// 	$this->addDbWhere(['like','level',$labelInfo['level'] . $labelInfo['id'] . '-' . '%',false]);
	// 	$this->fields = ['id','name'];
	// 	$list = $this->allList();
	// 	$pTree = [['id' => $labelInfo['id'],'name' => $labelInfo['name']]];
	// 	$return = $list?array_merge($pTree,$list):$pTree;
	// 	return $return;
	// }
	public static function labelTree ($label_id,$condition = '1=1')
	{
		if (!$label_id || !($labelInfo = static::getInfoById($label_id))) return [];
		$query = static::find();
		$query->select(['id','name']);
		$query->where(['and',['like','level',$labelInfo['level'] . $labelInfo['id'] . '-' . '%',false]])->andWhere($condition);
		$list = $query->asArray()->all();
		$pTree = [['id' => $labelInfo['id'],'name' => $labelInfo['name']]];
		$return = $list?array_merge($pTree,$list):$pTree;
		return $return;
	}
}