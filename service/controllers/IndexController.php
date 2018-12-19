<?php
namespace service\controllers;

use Yii;
use yii\web\Controller;
use service\models\Common;

/**
* 首页
*/
class IndexController extends Controller
{
	public $request;
	public $app;
	public $dev = 'development';
	public $isOut = false;
	public function init () 
	{
		parent::init();
		error_reporting(0);
		$this->app = Yii::$app;
		$this->request = Yii::$app->request;
		set_error_handler([$this,'errorHandle'],E_ALL || ~E_NOTICE);
		set_exception_handler([$this,'exceptionHandle']);
		register_shutdown_function([$this, 'handleFatalError']);//注册一个会在php中止时执行的函数
	}
	public function actionIndex ()
	{
		$requestData = $this->request->post();//$this->serviceSuccess($requestData?:'');
		//$requestData = $this->imitateData();
		$commonModel = new Common();
		$commonModel = $this->setAttribuites($commonModel,$requestData);	
		$result = $commonModel->apiRun();
		$this->serviceSuccess($result?:'');
	}
	public function imitateData ()
	{
		$cmd = "Navigate/navList";
		return [
			'version'	=>	"1.0",
			'client'	=>	"web",
			'cmd'		=>	$cmd,
			'sign'		=>	md5("web|654321|".$cmd),
		];
	}
	/**
	 * 给commonModel对象属性赋值
	 * @param object $object    commonModel对象
	 * @param [type] $propertys [description]
	 */
	public function setAttribuites ($object,$propertys)
	{
		if (!($object instanceof Common))
			throw new \Exception("the object is error", Common::CONFIG_ERROR);
		foreach ($propertys as $key => $value) {
			$object->$key = $value;
		}
		return $object;		
	}
	/**
	 * 接口调用成功输出
	 * @param  string|array $data 返回数据
	 * @return [type]       [description]
	 */
	public function serviceSuccess ($data = '')
	{
		header("Content-type:application/json");
		$result = [
			"errorCode"	=>	0,
			"errorMsg"	=>	'',
			"dataresult"	=>	$data,
			"servertime"	=>	time(),
		];
		if (!$this->isOut) {
			$this->isOut = true;
			exit(json_encode($result));
		}
	}
	/**
	 * 接口调用出错
	 * @param  int $code 错误code
	 * @param  string $msg  错误内容
	 * @param  string $data 错误数据
	 * @return [type]       [description]
	 */
	public function serviceError ($code,$msg,$data = '')
	{
		header("Content-type:application/json");
		$result = [
			"errorCode"		=>	$code,
			'errorMsg'		=>	$msg,
			"dataresult"	=>	$data,
			"servertime"	=>	time(),
		];
		if (!$this->isOut) {
			$this->isOut = true;//dump($result);
			exit(json_encode($result));
		}
	}
	/**
	 * 处理非致命性错误信息
	 * @param  int $errorno      错误级别
	 * @param  string $errorstr     错误信息
	 * @param  string $errorfile    错误所在文件
	 * @param  string $errorline    错误所在行
	 * @param  string $errorcontext 错误代码附近的内容
	 * @return [type]               [description]
	 */
	public function errorHandle ($errorno,$errorstr,$errorfile,$errorline,$errorcontext)
	{
		$error = $errorstr;
		if ($this->dev == 'development')
			$error .= " in " . $errorfile . " on line " . $errorline;
		$this->serviceError($errorno,$error);
	}
	/**
	 * 处理抛出的异常信息
	 * @param  [type] $ex 抛出的异常对象
	 * @return [type]     [description]
	 */
	public function exceptionHandle ($ex)
	{
		$error = $ex->getMessage();
		if ($this->dev == 'development')
			$error .= " in " . $ex->getFile() . " on line " . $ex->getLine();
		$this->serviceError($ex->getCode()?:200,$error);
	}
	/**
	 * PHP执行终止时调用 exit或者出现致命性错误时
	 * @return [type] [description]
	 */
	public function handleFatalError ()
	{
		$error = error_get_last();
		$errorMsg = $error['message'];
		if ($this->dev == 'development')
			$errorMsg .= ' in ' . $error['file'] . ' on line ' . $error['line'];
		$this->serviceError(200,$errorMsg);
	}
}