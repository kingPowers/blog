<?php
namespace backend\models;

use yii\web\UploadedFile;
/**
* 前台首页banner
*/
class Banner extends Common
{
	const SCENARIO_CHECKIMG = 'checkimg';
	static $imageDomain = _UPLOAD_ . "/images/banner/";
	static $savePath = UPLOAD_PATH . "images" . DS ."banner" . DS;
	public $whereFilter = [
		"status"	=>	['=','status',"_value_"],
		"id"		=>	['=','id',"_value_"],
	];
	public $bannerImg;
	public static function tableName ()
	{
		return "{{banner}}";
	}
	public function attributeLabels ()
	{
		return [
			'image'	=>	"图片",
			"title"	=>	"标题",
			'url'	=>	'链接',
			'status'=>	'状态',
		];
	}
	public function rules ()
	{
		return [
			[['bannerImg'],'file','skipOnEmpty' => false,'extensions' => "jpg,gif,png,jpeg",'on'=>[self::SCENARIO_CHECKIMG], 'mimeTypes' => 'image/jpeg, image/png',"message" => "上传图片格式错误"],
			[['title','url','status'],"required",'on' => [self::SCENARIO_ADD,self::SCENARIO_EDIT],"message" => "请填写或选择{attribute}"],
			[['image'],'required',"on" => [self::SCENARIO_ADD],"message" => "请上传banner图片"],
		];
	}
	public function scenarios ()
	{
		return [
			self::SCENARIO_ADD	=>	['image','title','url','status'],
			self::SCENARIO_EDIT	=>	['image','title','url','status'],
			self::SCENARIO_CHECKIMG	=>	['bannerImg'],
		];
	}
	public function add ($data)
	{
		if (false == $this->checkMyValidate($data,self::SCENARIO_ADD))return false;
		if (false == $this->save(false))return $this->error("banner添加失败");
		return $this->id;
	}
	public function edit($bannerid,$data)
	{
		if (empty($bannerid) || (false == ($bannerModel = self::findOne($bannerid)))) return $this->error("bannerid错误");
		if (false == $this->checkMyValidate($data,self::SCENARIO_EDIT)) return false;

		if ($data['image']) 
			unlink($this->savePath . $bannerModel->image);
		$bannerModel->scenario = self::SCENARIO_EDIT;
		$bannerModel->attributes = $data;
		if (false == $bannerModel->save(false)) return $this->error("保存修改失败");
		return true; 
	}
	public function checkimg ($imgObj)
	{
		if (!($imgObj instanceof UploadedFile) || $imgObj->error != 0) return $this->error("文件上传出错");
		$data['bannerImg'] = $imgObj;
		if (false == $this->checkMyValidate($data,self::SCENARIO_CHECKIMG)) return false;
		return true;
	}
}