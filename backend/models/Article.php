<?php
namespace backend\models;

/**
* 标签管理
*/
class Article extends Common
{
	const TOP_STATUS = 2;const TOP_STATUS_NAME = "置顶";
	const UNTOP_STATUS = 1;const UNTOP_STATUS_NAME = "非置顶";
	const NEW_STATUS = 2;const NEW_STATUS_NAME = "最新";
	const UNNEW_STATUS = 1;const UNNEW_STATUS_NAME = "非最新";
	const SCENARIO_SETTOP = 'settop';
	static $top_status = [
		self::UNTOP_STATUS => '非置顶'
		,self::TOP_STATUS => '置顶'
	];
	static $new_status = [
		self::UNNEW_STATUS => '非最新'
		,self::NEW_STATUS => '最新'
	];
	public $joinTables = [
		['name' => 'm_user as u','where' => "u.id=a.author",'type' => 'LEFT JOIN'],
	];
	public $fields = "a.*,u.names";
	public $labelList = [];
	public $whereFilter = [
		'status'	=>	['=','a.status','_value_'],
		'id'		=>	['=','a.id','_value_'],
		'names'		=>	['like','u.names','%_value_%',false],
		'newStatus'	=>	['=','a.is_new','_value_'],
		//'label'		=>	['like','a.labels','%_value_%',false],
		'title'		=>	['like','a.title','%_value_%',false],	
	];
	public static function tableName ()
	{
		return "{{article}}";
	}
	public function attributeLabels ()
	{
		return [
			'status'	=>	'状态',
			'title'		=>	'标题',
			'major_label'=>	'主标签',
			'labels'	=>	'副标签',
			'is_new'	=>	'是否最新',
			'introduction' => '简介',
		];
	}
	public function rules ()
	{
		return [
			[['status','title','is_new','major_label'],'required','on' => [self::SCENARIO_ADD,self::SCENARIO_EDIT],'message' => "请填写或选择{attribute}"],	
			//[['major_label'],'match','pattern' => '/[1-9]+/','on' => [self::SCENARIO_ADD,self::SCENARIO_EDIT],'message' => "请选择{attribute}"],
			['major_label','compare','compareValue' => 0,'operator' => '>'],
			['status','in','range' => array_keys(self::$status)],
		];
	}
	public function scenarios ()
	{
		return [
			self::SCENARIO_ADD => ['title','labels','autor','status','is_new','introduction','major_label'],
			self::SCENARIO_EDIT	=>	['content','title','labels','status','is_new','is_top','introduction','major_label'],
			self::SCENARIO_SETTOP => ['is_top'],
		];
	}
	public function beforeEdit ()
	{
		if (empty($this->author)) $this->author = \Yii::$app->user->identity->id;
		if (is_array($this->labels)) $this->addError('title',json_encode($this->labels));
	}
	public function add ($data)
	{
		if (false == $this->checkMyValidate($data,self::SCENARIO_ADD))return false;
		if (empty($this->author)) $this->author = User::getUserInfo("id");
		if (false == $this->save(false))return $this->error("文章添加失败");
		return $this->id;
	}
	public function edit ($articleid,$data,$scenario = self::SCENARIO_EDIT)
	{
		if (empty($articleid) || (false == ($articleModel = $this->getArticleById($articleid))))return $this->error("labelid错误");
		if (false == $this->checkMyValidate($data,$scenario))return false;
		$articleModel->scenario = $scenario;
		$articleModel->attributes = $data;
		if (false == $articleModel->save(false))return $this->error("文章修改失败");
		return true;
	}
	/**
	 * 获取标签列表
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-21T18:41:19+0800
	 * @param    [type]                   $params [description]
	 * @return   [type]                           [description]
	 */
	public function articleList ($params) 
	{
		$labelModel = new Label();
		$this->labelList = $labelModel->allList();
		if ($this->where['label']) {
		 	$labelTree = $labelModel->getLabelTree($this->where['label']);
			$this->addDbWhere(['in','a.major_label',array_column($labelTree, 'id')]);
		}

		$db = self::find()->from(self::tableName()." a");
		$list = $this->allList($db,$params);
		return $list;
	}
	/**
	 * 处理列表子数据
	 * @param  [type] $value 列表子数据
	 * @return [type]        [description]
	 */
	public function handleValue ($value)
	{
		$value['newStatus'] = self::getNewStatus($value['is_new']);
		$value['topStatus'] = self::getTopStatus($value['is_top']);
		$value['labelList'] = '';
		$labels = explode(',', $value['labels']);

		foreach ($this->labelList as $label) {

			if (in_array($label['id'],$labels)) 
				$value['labelList'] .= $label['name'] . ',';

			if ($label['id'] == $value['major_label'])
				$value['major_label_name'] = $label['name'];
		}
		
		$value['labelList'] = trim($value['labelList'],',');
		return $value;
	}
	public function checkMyValidate (&$data,$scenario = null)
	{
		return parent::checkMyValidate($data,$scenario);
	}
	/**
	 * 文章置顶
	 * @param [type] $articleid 文章id 
	 */
	public function setTopStatus ($articleid)
	{
		if (empty($articleid) || !$articleInfo = $this->getByid($articleid)) return $this->error("文章id错误");
		$topStatus = ($articleInfo['is_top'] == self::TOP_STATUS)?self::UNTOP_STATUS:self::TOP_STATUS;
		$trans = self::getDb()->beginTransaction();
		try {
			// if (self::findOne(['is_top' => self::TOP_STATUS]))
			// 	if (false == self::updateAll(['is_top' => self::UNTOP_STATUS],['is_top' => self::TOP_STATUS]))
			// 		throw new \Exception("更新错误");
			if (false == $this->edit($articleid,['is_top' => $topStatus],self::SCENARIO_SETTOP))
				throw new \Exception($this->getError());	
			$trans->commit();
			return true;
		} catch (\Exception $ex) {
			$trans->rollBack();
			return $this->error($ex->getMessage());
		}
	}
	/**
	 * 文章启/禁用
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-27T17:51:25+0800
	 * @param    [type]                   $articleid [文章id]
	 * @return   [type]                              [description]
	 */
	public function changeStatus ($articleid)
	{
		if (!$articleid || !$articleInfo = $this->getArticleById($articleid))
			return $this->error('文章id错误');

		$status = ($articleInfo->attributes['status'] == static::ENABLE_STATUS)?static::DISABLE_STATUS:static::ENABLE_STATUS;

		return $this->edit($articleid,['status' => $status]);

	}
	public function getArticleById ($articleid,$cache = true)
	{
		if (!$articleid) return [];

		if ($cache) {
			static $article;
			if ($article[$articleid]) return $article[$articleid];
		}
		$article[$articleid] = self::findOne($articleid);			

		return $article[$articleid];
	}
	public static function getTopStatus ($status = '')
	{
		$statusArr = [
			self::TOP_STATUS 	=>	self::TOP_STATUS_NAME,
			self::UNTOP_STATUS 	=>	self::UNTOP_STATUS_NAME,
		];
		return $status?$statusArr[$status]:$statusArr;
	}
	public static function getNewStatus ($status = '')
	{
		$statusArr = [
			self::NEW_STATUS 	=>	self::NEW_STATUS_NAME,
			self::UNNEW_STATUS 	=>	self::UNNEW_STATUS_NAME,
		];
		return $status?$statusArr[$status]:$statusArr;
	}
	public function getUserInfo ()
	{
		return  $this->hasOne(UserAdmin::className(), ['id' => 'author']);
	}
	public function getViceLabel ()
	{
		$labelids = explode(',', trim($this->labels,','));
		$labelInfos = Label::getInfoByIds($labelids);
		return $labelInfos;
	}
}