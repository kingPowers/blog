<?php
namespace service\models\version1;

/**
* 
*/
class Article extends Base
{
	const RKEY_PRE = 'services:article';
	const REDIS_LIFE = 6;
	const TOP_STATUS = 2;const UNTOP_STATUS = 1;
	const NEW_STATUS = 2;const UNNEW_STATUS = 2;
	public $whereFilter = [
		'status' => ['=','status',"_value_"],
		'is_top' => ['=','is_top',"_value_"],
		'idgt'	=>	['>','id','_value_'],
		'idlt'	=>	['<','id','_value_'],
		'id'	=>	['=','id','_value_'],
		'labels'	=>	['in','major_label','_value_']
	];
	public function init ()
	{
		$this->imageDomain = _UPLOAD_ . "/images/article/";
	}
	public static function tableName ()
	{
		return "{{article}}";
	}
	/**
	 * 置顶文章
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-22T14:32:06+0800
	 * @return   [type]                   [description]
	 */
	public function topArticle()
	{
		$rkey = self::RKEY_PRE . __METHOD__ . serialize(func_get_args());
		$data = $this->redisGet($rkey,function () {
			$this->where['is_top'] = self::TOP_STATUS;
			$this->where['status'] = self::ENABLE_STATUS;
			$this->fields = ['id','title','introduction','image'];
			$list = $this->allList();
			return $list?array_slice($list,0,1):[];
		},self::REDIS_LIFE);

		return $data;
	}
	/**
	 * 随机获取最新文章
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-22T15:15:29+0800
	 * @param    integer                  $limit [获取个数]
	 * @return   [type]                          [description]
	 */
	public function newArticle($limit = 10)
	{
		$rkey = self::RKEY_PRE . __METHOD__ .serialize($this->requestData);
		$data = $this->redisGet($rkey,function () use ($limit) {
			$tableName = 'article';
			$where = [];
			$where[] = 'status=' . self::ENABLE_STATUS;
			$where[] = 'is_new=' . self::NEW_STATUS;
			$where[] = 'id >= ((SELECT MAX(id) FROM ' . $tableName . ')-(SELECT MIN(id) FROM ' . $tableName . ')) * RAND() + (SELECT MIN(id) FROM ' . $tableName . ')';
			$fields = 'title,id';
			$sql = 'SELECT ' . $fields . ' FROM  ' . $tableName . ' WHERE ' . implode(' AND ',$where) . ' LIMIT ' . $limit;
			//随机获取10条
			$command = \Yii::$app->db->createCommand($sql);
			$res = $command->queryAll();
			return $res?:[];
		},self::REDIS_LIFE);
		return $data;
	}
	/**
	 * 文章列表接口
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-25T14:18:03+0800
	 * @return   [type]                   [description]
	 */
	public function listApi ()
	{
		$rkey = self::RKEY_PRE . __METHOD__ . serialize($this->requestData);
		$data = $this->redisGet($rkey,function () {
			$requestData = $this->requestData;
			$this->where = $this->getWhere($requestData);
			$params['order'] = $requestData['order']?:'id desc';
			$params['isPage'] = 1;
			$params['page'] = $requestData['page']?:1;
			$this->fields = ['id','title','image','introduction','author','views','timeadd','labels','major_label'];
			$this->where['status'] = self::ENABLE_STATUS;
			$list = $this->allList(self::find(),$params);
			return $list?:[];
		},self::REDIS_LIFE);

		return $data;		
	}
	public function getWhere ($where)
	{
		if (empty($where)) return [];

		$return = [];
		$labelModel = new Label();
		if ($where['label']) {
			$labelTree = $labelModel->getLabelTree($where['label']);
			$return['labels'] = array_column($labelTree, 'id');
		}
		return $return;
	}
	/**
	 * 文章详情接口
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-25T14:18:33+0800
	 * @return   [type]                   [description]
	 */
	public function detailApi ()
	{
		$articleId  = $this->requestData['article_id'];

		if (!$articleId)
			$this->apiErr('参数错误');

		$rkey = self::RKEY_PRE . __METHOD__ .serialize($this->requestData);

		$data = $this->redisGet($rkey,function () use ($articleId) {
			$this->where['id'] = $articleId;
			$articleInfo = $this->allList()[0];

			if (!$articleInfo) 
				$this->apiErr('文章不存在');

			if ($articleInfo['status'] != static::ENABLE_STATUS)
				$this->apiErr('文章尚未开放');
			//$articleInfo  = $this->handleValue($articleInfo);
			$articleInfo['pre_article'] = $this->preArticel($articleInfo);
			$articleInfo['next_article'] = $this->nextArticle($articleInfo);
			return $articleInfo?:[];
		},self::REDIS_LIFE);
	
		return $data;
	}
	/**
	 * 获取某一篇文章的上一篇文章
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-26T10:01:56+0800
	 * @param    [type]                   $articleInfo [文章内容]
	 * @return   [type]                                [description]
	 */
	public function preArticel($articleInfo)
	{
		if (empty($articleInfo))return [];

		$this->where = [
			'status' => self::ENABLE_STATUS,
			'idgt'	=>	$articleInfo['id'],
		];

		$this->fields = ['id','title'];
		$this->pageNum = 1;
		$this->handleValue = false;
		$data = $this->allList(null,['isPage' => 1,'page' => 1,'order' => 'id DESC'])[0];
		return $data?:[];
	}
	/**
	 * 获取某一篇文章的上一篇文章
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-26T10:12:45+0800
	 * @param    [type]                   $articleInfo [文章内容]
	 * @return   [type]                                [description]
	 */
	public function nextArticle($articleInfo)
	{
		if (empty($articleInfo))return [];

		$this->where = [
			'status' => self::ENABLE_STATUS,
			'idlt'	=>	$articleInfo['id'],
		];

		$this->fields = ['id','title'];
		$this->pageNum = 1;
		$this->handleValue = false;
		$data = $this->allList(null,['isPage' => 1,'page' => 1,'order' => 'id DESC'])[0];
		return $data?:[];
	}
	public function handleValue($value)
	{
		if (false == $this->handleValue) return $value;

		if ($value['image']) $value['image'] = $this->imageDomain . $value['image'];

		$value['label'] = $this->articleLabels($value);
		$author = User::findOne($value['author']);
		$value['author'] = $author?$author->names:'匿名';
		$value['views'] = rand(99,9999);
		$value['comments'] = rand(0,100);
		return $value;
	}
	public function articleLabels ($articleInfo)
	{
		if (!$articleInfo || (!$articleInfo['major_label'] && !$articleInfo['labels']))
			return [];

		$labels = array_unique(array_merge([$articleInfo['major_label']],explode(',', $articleInfo['labels'])));
		$labelModel = new Label();
		$labelList = $labelModel->getByIds(implode(',', $labels));

		$return = [];
		foreach ($labels as $value) {
			if ($value['name'])
				$return[] = [
					'id' => $labelList[$value]['id'],
					'name' => $labelList[$value]['name']
				];
		}

		return $return;
	}
}