<?php
namespace service\models\version1;

use service\models\Common;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveQuery;
use Yii;
use yii\data\Pagination;

/**
* 接口请求处理基类
*/
class Base extends ActiveRecord
{
	const API_MODEL_ERROR = '500';
	const API_NOMARL_ERROR = '501';
	const ENABLE_STATUS = 1;const ENABLE_STATUS_NAME = "启用";
	const DISABLE_STATUS = 2;const DISABLE_STATUS_NAME = "禁用";
	const REDIS_LIFE = 15;
	public $fields;
	public $file;
	public $commonModel;
	protected $finalSql;
	protected $joinTables;
	public $requestData;
	protected $lastDb;
	public $where;
	public $whereFilter;
	public $dbWhere = [];
	public $imageDomain;
	public $pageNum = 10;
	public $handleValue = true;
	public function apiInit($commonModel,$config = [])
	{
		if (!($commonModel instanceof Common))
			$this->apiErr("接口初始化失败",self::API_MODEL_ERROR);
		$this->commonModel = clone $commonModel;
		$this->requestData = $commonModel->requestData;
	}
	public function apiErr ($msg,$code = '')
	{
		throw new \Exception($msg, $code?:self::API_NOMARL_ERROR);
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
				if (is_array($value)) {
					$tmpWhere[2] = $value;
				} else {
					$tmpWhere[2] = str_replace('_value_',$value,$tmpWhere[2]);
				}
				$dbWhere[] = $tmpWhere;
			}	
		}
		$this->dbWhere = array_merge($this->dbWhere,$dbWhere);
		foreach ($this->dbWhere as $value) {
			$db->andFilterWhere($value);
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
		if (empty($db))$db = static::find();
		if (!($db instanceof \yii\db\ActiveQuery)) $this->apiErr("非正常查询");
		$this->getJoinDb($db);
		$this->setDbWhere($db,$this->where);
		if ($returnArr) $db->asArray();
		if ($params['order']) $db->orderBy($params['order']);
		if ($params['isPage']) {
			$pageDb = clone $db;
			$count = $pageDb->count();
			$pagination = new Pagination(['totalCount' => $count,'pageSize' => $this->pageNum]);
			$db->offset($pagination->offset)->limit($pagination->limit);
		}
		$db->select($this->fields);
		$list = $db->all();

		foreach ($list as &$value) {
			$value = $this->handleValue($value);
		}

		$this->lastDb = clone $db;
		$this->afterQuery();
		return $list;
	}
	public function afterQuery()
	{
		$this->where = [];
		$this->dbWhere = [];
		$this->fields = [];
	}
	/**
	 * 通过id获取内容
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-21T16:02:38+0800
	 * @param    [type]                   $id    [description]
	 * @param    boolean                  $cache [description]
	 * @return   [type]                          [description]
	 */
	public function getByid($id,$cache = true)
	{
		if (!$id) return [];

		if ($cache) {
			static $data;
			if ($data[$id]) return $data[$id];
		}
		$dataObj = static::findOne($id);	
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
		$return = [];
		foreach ($list as $value) {
			$return[$value['id']] = $value;
		}
		return (array)$return;
	}
	/**
	 * 处理列表子数据
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-22T11:34:24+0800
	 * @param    [type]                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function handleValue($value)
	{
		if (false == $this->handleValue) return $value;
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
	public function getLastSql()
	{
		if (!$this->finalSql && ($this->lastDb))
			$this->finalSql = $this->lastDb->createCommand()->getRawSql();
		return $this->finalSql;
	}
	/**
	 * 获取redis缓存
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-22T11:23:08+0800
	 * @param    [type]                   $key              [缓存键值]
	 * @param    [type]                   $nullVallCallBack [回调函数]
	 * @param    integer                  $lifetime         [缓存有效时间]
	 * @return   [type]                                     [description]
	 */
	public function redisGet ($key,$nullVallCallBack = null,$lifetime = 15)
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
	public function redsiSet ($key,$value,$lifetime = 15)
	{
		if (!$key) return false;

		\Yii::$app->cache->set($key,$value,$lifetime);
	}
}