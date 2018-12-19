<?php
namespace common\components;

use yii\web\Controller;
use yii\web\Response;
use Yii;
/**
* 
*/
class BaseController extends Controller
{
	public function init ()
	{	
		if ($this->request->isOptions)
			exit(json_encode(['code' => 0]));
		parent::init();
		if(isset($_SERVER['HTTP_TOKEN']) && $_SERVER['HTTP_TOKEN']){
            $token = trim($_SERVER['HTTP_TOKEN']);
            $this->setSession($token);
        }
	}
	protected function setSession ($session_id = '')
	{
		if ($session_id) Yii::$app->session->setId($session_id);
		Yii::$app->session->open();
		// if($session_id && ($session_values = \Yii::$app->session->readSession($session_id))){
  //           @session_decode($session_values);
  //           if(isset($_SESSION['__id']) && $_SESSION['__id']){
  //               \Yii::$app->user->regenerateId = false;
  //               \Yii::$app->user->switchIdentity(\common\models\UserModel::findIdentity($_SESSION['__id']));
  //           }
  //       }
	}	
	/**
	 * 获得请求对象
	 */
	public function getRequest()
	{
		return Yii::$app->getRequest();
	}
	/**
	 * 获得响应对象
	 */
	public function getResponse()
	{
		return Yii::$app->getResponse();
	}
	/**
	 * 获得请求客户端信息
	 * 从request中获得，便于调试，有默认值
	 */
	public function getClient()
	{
		return Yii::$app->getRequest()->getClient();
	}

    public function params()
    {
        return array_merge($_GET, $_POST);
    }
    /**
	 * 翻译文字
	 * @param unknown $key
	 * @param string $channel
	 */
	public function t($key,$channel=''){
	    return \common\helpers\Util::t($key,$channel);
	}
	/**
     * 统一设置cookie
     * @param unknown $name
     * @param unknown $val
     * @param unknown $expire
     * @return boolean
     */
    public function setCookie($name,$val,$expire=0){
        $cookieParams = ['httpOnly' => true, 'domain'=>''];
        if($expire !== null){
            $cookieParams['expire'] = $expire;
        }
        $cookies = new \yii\web\Cookie($cookieParams);
        $cookies->name = $name;
        $cookies->value = $val;
        $this->response->getCookies()->add($cookies);
        return true;
    }
    /**
     * 统一获取cookie
     * @param unknown $name
     * @return mixed
     */
    public function getCookie($name){
        $val = $this->request->getCookies()->getValue($name);
        if($val){
            return $val;
        }
        $val = $this->response->getCookies()->getValue($name);
        return $val;
    }
    /**
     * ajax请求成功返回
     * @DateTime 2018-11-01T17:07:43+0800
     * @param    string                   $msg  [description]
     * @param    array                    $data [description]
     * @return   [type]                         [description]
     */
    public function ajaxSuccess ($msg = '',$data = [])
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;//定义响应格式
		return ["status" => 1,'info' => $msg,"data" => $data];
	}
	/**
     * ajax请求失败返回
     * @DateTime 2018-11-01T17:07:43+0800
     * @param    string                   $msg  [description]
     * @param    array                    $data [description]
     * @return   [type]                         [description]
     */
	public function ajaxError ($msg = '',$data = [])
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;//定义响应格式
		return ["status" => 0,'info' => $msg,"data" => $data];
	}
	public function ajaxArray ($data,$msg = '')
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;//定义响应格式
		return ["status" => 0,'info' => $msg.json_encode($data),"data" => $data];
	}
	public function getControllerName ()
	{
		return $this->id;
	}
	public function getActionName ()
	{
		$requestedRoute = explode("/",Yii::$app->requestedRoute);
		$actionName = $requestedRoute?end($requestedRoute):(Yii::$app->defaultRoute);
		return $actionName;
	}
	public function getApp ()
	{
		return Yii::$app;
	}
	public function debugInfo ()
	{
		$timings = $this->getSqlLog();
		$log = \Yii::getLogger();
		return [
			'run_time' => $log->getElapsedTime()
			,'sql' => array_column($timings, 'info')
		];
	}
	public function getSqlLog ()
	{
		//获取所有的sql
		$messages = \yii\log\Target::filterMessages(Yii::getLogger()->messages, \yii\log\Logger::LEVEL_PROFILE, ['yii\db\Command::query','yii\db\Command::execute', 'yii\db\Command::insert', 'yii\db\Command::batchInsert', 'yii\db\Command::update', 'yii\db\Command::delete']);
		return Yii::getLogger()->calculateTimings($messages);
	}
	public static function explainSql ($sql,$db = '')
	{
		if (!$sql) return false;
		$sql = 'EXPLAIN EXTENDED ' . $sql;
		return self::queryBySql($sql,$db);
	}
	public static function queryBySql ($sql,$db = '')
	{
		if (!$sql) return false;
		if (!$db) $db = \Yii::$app->db;
		$result = $db->createCommand($sql)->queryAll();
		return $result;
	}
}