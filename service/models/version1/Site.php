<?php
namespace service\models\version1;

/**
* 
*/
class Site extends Base
{
	const RKEY_PRE = 'services:site';
	const REDIS_LIFE = 6;
	public $whereFilter = [
		'status' => ['=','status',"_value_"],
		'is_top' => ['=','is_top',"_value_"],
	];
	public function init ()
	{
		$this->imageDomain = _UPLOAD_ . "/images/site/";
	}
	public static function tableName ()
	{
		return "{{site}}";
	}
	/**
	 * 获取站点公告
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-22T14:47:25+0800
	 * @return   [type]                   [description]
	 */
	public function siteNotice()
	{
		$rkey = self::RKEY_PRE . __METHOD__ .serialize(func_get_args());
		$data = $this->redisGet($rkey,function () {
			$info = [
				'title'	=>	'Please Focus On',
				'content'	=>	'This is Description about site-notice',
			];
			return $info;
		});

		return $data;
	}
	public function friendlyLink()
	{
		$links = [
			[
				'url'	=>	'https://www.baidu.com/',
				'name'	=>	"百度首页",	
			],
			[
				'url'	=>	'https://www.baidu.com',
				'name'	=>	"开源中国",	
			],
			[
				'url'	=>	'https://www.baidu.com',
				'name'	=>	"CSDN",	
			],
			[
				'url'	=>	'https://www.baidu.com',
				'name'	=>	"知乎",	
			],
			[
				'url'	=>	'https://www.baidu.com',
				'name'	=>	"新浪微博",	
			],
			[
				'url'	=>	'https://www.yiichina.com/',
				'name'	=>	'YII学习'
			],
		];

		return $links;
	}
}