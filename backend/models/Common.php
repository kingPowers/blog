<?php
namespace backend\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveQuery;
use Yii;
use yii\data\Pagination;
/**
* 
*/
class Common extends ActiveRecord
{
	const SCENARIO_ADD = 'add';
	const SCENARIO_EDIT  ='edit';
	const SCENARIO_DEFAULT = 'add';
	const ENABLE_STATUS = 1;const ENABLE_STATUS_NAME = "启用";
	const DISABLE_STATUS = 2;const DISABLE_STATUS_NAME = "禁用";
	public static $status = [
		self::ENABLE_STATUS => self::ENABLE_STATUS_NAME,
		self::DISABLE_STATUS => self::DISABLE_STATUS_NAME
	];
	public $error = '';
	public $app;
	public $request;
	protected $finalSql;
	protected $joinTables;
	protected $lastDb;
	protected $fields = '';
	public $where;
	public $whereFilter;
	public $dbWhere = [];
	public $orWhere = [];
	public $page;
	public $pageNum = 10;
	public $backend_err_msg;
	public function init ()
	{
		$this->app = Yii::$app;
		$this->request  = Yii::$app->request;
	}
	/**
	 * 获取错误信息
	 * @param  boolean $single 是否获取第一条错误信息
	 * @return [type]          [description]
	 */
	public function getError ($single = true)
	{
		$errors = $this->getErrors();
		if (false == $single) return $errors;
		$error = $this->firstArrValue($errors);
		if (empty($error)) $error = $this->error;
		return $error;
	}
	public function firstArrValue ($arr) 
	{
		if (!is_array($arr)) return $arr;
		return $this->firstArrValue(current($arr));

	}
	public function backendError ($message)
	{
		$this->backend_err_msg = $message;
		$this->addError('backend_err_msg',$message);
	}
	/**
	 * 自定义验证规则
	 * 	子类用法
	 * 		public function checkMyValidate (&$data,$scenario = null) {
	 * 			*	
	 * 			子类验证内容
	 * 			*
	 * 			return parent::checkMyValidate($data,$scenario);
	 * 		}
	 * @param  [type] &$data    验证信息
	 * @param  [type] $scenario 验证场景
	 * @return [type]           [description]
	 */
	public function checkMyValidate (&$data,$scenario = null)
	{
		if ($scenario)$this->scenario = $scenario;
		$this->attributes = $data;
		return $this->validate();
	}
	/**
	 * 设置自动添加或者更新时间
	 * 若覆盖此方法并返回空数组即可
	 * @return [type] [description]
	 */
	public function behaviors ()
	{
		return [
			[
			'class'	=>	TimestampBehavior::className(),
			'createdAtAttribute'	=>	'timeadd',
			'updatedAtAttribute'	=>	'lasttime',
			'value'					=>	new Expression('NOW()'),
			]
		];
	}
	public function addWhereFilter ($filter) 
	{
		$this->whereFilter = array_merge($this->addWhereFilter(),$filter);
	}
	public function addDbWhere ($where)
	{
		$this->dbWhere[] = $where;
	}
	/**
	 * 获取全部列表筛选数据表所需的where数组
	 * @param  array $where 筛选where 类的$this->where 属性
	 * @return array       
	 */
	public function setDbWhere ($db,$where)
	{
		if (empty($where) && empty($this->dbWhere)) return $db;
		$dbWhere = [];

		foreach ($where as $name => $value) {
			if (in_array($name,array_keys($this->whereFilter))) {
				$tmpWhere = $this->whereFilter[$name];
				$tmpWhere[2] = str_replace('_value_',$value,$tmpWhere[2]);
				$dbWhere[] = $tmpWhere;
			}	
		}

		$this->dbWhere = array_merge($this->dbWhere,$dbWhere);

		foreach ($this->dbWhere as $value) {
			$db->andFilterWhere($value);
		}

		if ($this->orWhere) {
			$db->orFilterWhere($this->orWhere);
		}
	}
	public function getJoinDb ($db)
	{
		if (empty($this->joinTables))return $db;
		foreach ($this->joinTables as $value) {
			$db->join($value['type'],$value['name'],$value['where']);
		}
	}
	public function getFields ()
	{
		return $this->fields;
	}
	public function allList ($db = '',$params = [],$returnArr = true)
	{
		$args = func_get_args();
		unset($args[0]);
		$args[] = $this->where;
		$args[] = $this->joinTables;
		$args[] = $this->fields;//dump(get_called_class());
		//$data = $this->getByCache()
		if (empty($db))$db = self::find();
		$this->getJoinDb($db);
		$this->setDbWhere($db,$this->where);
		if ($returnArr) $db->asArray();
		if (!empty($this->fields)) $db->select(explode(",",$this->fields));
		if (isset($params['order'])) $db->orderBy($params['order']);
		if (isset($params['isPage'])) {
			$pageDb = clone $db;
			$count = $pageDb->count();
			$this->page = $pagination = new Pagination(['totalCount' => $count,'pageSize' => $this->pageNum]);
			$db->offset($pagination->offset)->limit($pagination->limit);
		}
		$list = $db->all();
		foreach ($list as &$value) {
			if ($statusName = self::getStatus()) 
				$value['statusName'] = $statusName[$value['status']];
			$value = $this->handleValue($value);
		}
		$this->lastDb = clone $db;
		return $list;
	}
	/**
	 * 通过id获取内容
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-21T16:02:38+0800
	 * @param    [type]                   $id    [description]
	 * @param    boolean                  $cache [description]
	 * @return   [type]                          [description]
	 */
	public function getByid($id,$cache = true,$array = true)
	{
		if (!$id) return [];

		if ($cache) {
			static $data;
			if ($data[$id]) return $data[$id];
		}
		$dataObj = self::findOne($id);	
		$data[$id] = $dataObj?$dataObj->attributes:[];
		return $data[$id];
	}
	/**
	 * 通过id字符串获取信息
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-21T16:19:36+0800
	 * @param    [type]                   $ids [description]
	 * @return   [type]                        [description]
	 */
	public function getByIds($ids)
	{
		if (empty($ids))return [];
		$this->addDbWhere(['in','id',explode(',', $ids)]);
		$list = $this->allList();

		return (array)$list;
	}
	/**
	 * 获取查询sql语句
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-21T11:41:01+0800
	 * @return   [type]                   [description]
	 */
	public function getLastSql()
	{
		if (!$this->finalSql && ($this->lastDb))
			$this->finalSql = $this->lastDb->createCommand()->getRawSql();
		return $this->finalSql;
	}
	public function handleValue ($value)
	{
		return $value;
	}
	/**
	 * 获取状态数组或者状态名 子类可覆盖
	 * @param  string || int $status 状态
	 * @return [type]         [description]
	 */
	static function getStatus($status = '')
	{
		$arr = [
			static::ENABLE_STATUS => static::ENABLE_STATUS_NAME,
			static::DISABLE_STATUS =>	static::DISABLE_STATUS_NAME,
		];
		return $status?$arr[$status]:$arr;
	}
	public function error ($msg)
	{
		$this->error = $msg;
		return false;
	}
	public function getByCache ($key,$nullVallCallBack = null,$lifetime = 15)
	{
        if (CACHE_ENABLE === false)
            return call_user_func($nullVallCallBack);

        $value = \Yii::$app->cache->get($key);

        if (false === $value) {
        	if ($nullVallCallBack === null) {
        		$value = null;
        	} else {
        		$value = call_user_func($nullVallCallBack);
        		if (!isset($value)) $value = null;

        		\Yii::$app->cache->set($key,$value,$lifetime);
        	}
        }
        return $value;
	}
	public static function getInfoById ($id,$isArray = true,$condition = '1=1')
	{
		if (!$id) return [];
		$query = self::find()->where(['id' => $id])->andWhere($condition);
		if ($isArray) $query->asArray();
		return $query->limit(1)->one();
	}
	public static function getInfoByIds ($ids,$isArray = true,$indexBy = '',$condition = '1=1')
	{
		if (!$ids) return [];
		if (is_string($ids)) $ids = implode(',',$ids);
		$query = self::find()->where(['id' => $ids])->andWhere($condition);
		if ($indexBy) $query->indexBy($indexBy);
		if ($isArray) $query->asArray();
		return $query->all();
	}
	public static function buildCaseSql ($field,$case,$alias)
	{
		if (!$field || !$case || !$alias) return '';
		$sql = ' CASE `'.$field . '`';
		foreach ($case as $key => $value) {
		 	$sql .= ' WHEN ' . $key . " THEN '" . $value . "'";
		}
		$sql .= ' END AS `' . $alias . '`';
		return $sql; 
	}
}