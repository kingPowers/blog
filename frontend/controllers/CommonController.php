<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
* 前台基类
*/
class CommonController extends Controller
{
	public $app;
	public $request;
	public $layout = "public";
	protected $toView;
	public $controllerName;
	public $actionName;
	public $currentUrl;
	public function init () 
	{
		error_reporting(E_ALL || ~E_NOTICE);
		parent::init();
		$this->controllerName = $this->id;
		$requestedRoute  = Yii::$app->requestedRoute;
		$this->actionName = $requestedRoute?explode("/",$requestedRoute)[1]:Yii::$app->defaultRoute;
		$this->app = Yii::$app;
		$this->request = Yii::$app->request;
		$this->currentUrl = \Yii::$app->request->hostInfo . \Yii::$app->request->getUrl();
		$this->assign("headerNav",$this->getHeaderNav());
	}
	public function getHeaderNav ()
	{
		$params['cmd'] = "Navigate/index";
		$serviceRes = $this->service($params);//dump($serviceRes);
		$nav = ($serviceRes['errorCode'] === 0)?$serviceRes['dataresult']:[];

		if ($nav) {
			$isFocus = false;
			foreach ($nav as &$value) {
				if (false == $isFocus) {
					if (false !== stripos($value['models'],$this->controllerName)) {
						$value['class'] = 'check-li';
						$isFocus = true;
					}
				} else {
					$value['class'] = '';
				}
			}
		}

		return $nav;
	}
	public function ajaxSuccess ($msg = '',$data = [])
	{
		return ["status" => 1,'info' => $msg,"data" => $data];
	}
	public function ajaxError ($msg = '',$data = [])
	{
		return ["status" => 1,"info" => $msg,"data" => $data];
	}
	public function assign ($name,$value,$recover = true)
	{
		$view = $this->app->getView();
		if (empty($name)) return;
		if (isset($this->toView[$name])) {
			if ($recover) $this->toView[$name] = $value;
		} else {
			$view->params[$name] = $value;
		}
	}
	public function beforeAction ($action)
	{
		if (!parent::beforeAction($action)) {
		    return false;
		}
		return true;
	}
	public function service ($data)
	{
		$postParams = array_merge($data,$this->serviceParam($data));
		$url = _SERVICE_;
		return $this->curlpost($url,$postParams);
	}
	public function serviceParam ($params)
	{
		$clientKeys = ['android' => '123456',"ios" => "111111",'web' => "654321","wx" => "123654"];
		$client = 'web';
		$resParams = [
			'client'	=>	$client,
			'sign'		=>	md5("{$client}|{$clientKeys[$client]}|{$params['cmd']}"),	
			'version'	=>	'1.0',
		];
		return $resParams;
	}
	public function curlpost($url,$array)
	{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//dump($array);exit;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
        curl_setopt($ch, CURLOPT_TIMEOUT,20);   //只需要设置一个秒的数量就可以
        $data = curl_exec($ch);//dump($data);
        $info = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($data,true);
        return $data;
    }
}