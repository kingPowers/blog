<?php
namespace service\models;


/**
* 数据操作基类
*/
class Common
{
	const CLASS_METHOD_SPACE = "/";
	const CONFIG_ERROR = '200';
	const CONFIG_EMPTY = '201';
	const API_SUF = 'Api';//接口方法名后缀
	protected $versionList = ['1.0'];
	protected $clientKeys = ['android' => '123456',"ios" => "111111",'web' => "654321","wx" => "123654"];
	protected $_version;
	protected $_cmd;
	protected $_sign;
	protected $_client;
	protected $_token;
	public $requestData;
	private $rootNamespace = "\@service";
	private $model;
	private $method;
	public function apiRun ()
	{
		$this->_init();
		$versionDir = "version" . trim(str_replace(".","",$this->_version),'0\0');
		$className = "service\models\\{$versionDir}\\{$this->model}";

		if (!class_exists($className))
			throw new \Exception("本版本接口尚未定义此操作",self::CONFIG_ERROR);

		$this->method .= self::API_SUF;
		$modelObject = new $className();
		if 	(!method_exists($modelObject,$this->method)) {
			throw new \Exception("请求接口尚未开发", self::CONFIG_ERROR);			
		}

		$modelObject->apiInit($this);
		return call_user_func([$modelObject,$this->method]);
	}
	private function _init ()
	{
		$notEmpty = ['cmd','sign','client','version'];
		foreach ($notEmpty as $value) {
			$objectValue = $this->$value;
			if (empty($objectValue))
				throw new \Exception("{$value}参数不能为空", self::CONFIG_EMPTY);	
		}
		if (empty($this->model) || empty($this->method)) 
			throw new \Exception("请求接口不存在", self::CONFIG_ERROR);
			
	}
	public function __set ($name,$value)
	{
		$method = "set". ucfirst($name);
		if (method_exists($this, $method)) {
			$this->$method($value);
		} elseif (method_exists($this,"get" . ucfirst($name))) {
			throw new \Exception("{$name} is a read only variable",self::CONFIG_ERROR);
		} elseif (!property_exists($this, $name)) {
			$this->requestData[$name] = $value;
		}
	}
	public function __get ($name) 
	{
		$method = "get" . ucfirst($name);
		if (method_exists($this, $method)) {
			return $this->$method();
		} elseif (method_exists($this, "set" . ucfirst($name))) {
			throw new \Exception("{$name} is a write only variable", self::CONFIG_ERROR);
		} elseif (!property_exists($this, $name)) {
		 	return $this->requestData[$name];
		}
	}
	protected function setVersion ($value)
	{
		if (!in_array($value,$this->versionList)) {
			throw new \Exception("version is unvalidate", self::CONFIG_ERROR);	
		}
		$this->_version = $value;
	}
	protected function getVersion ()
	{
		return $this->_version;
	}
	protected function setCmd ($value)
	{
		$this->checkNecessaryVariable("cmd",$value);
		list($this->model,$this->method) = explode(self::CLASS_METHOD_SPACE,$value);
		$this->_cmd = $value;
	}
	protected function getCmd ()
	{
		return $this->_cmd;
	}
	protected function setSign ($value)
	{
		$this->checkNecessaryVariable('sign',$value);
		$this->_sign = $value;
	}
	protected function getSign ()
	{
		return $this->_sign;
	}
	protected function setClient ($value)
	{
		$this->checkNecessaryVariable('client',$value);
		$this->_client = $value;
	}
	protected function getClient ()
	{
		return $this->_client;
	}
	protected function checkNecessaryVariable ($name,$value)
	{return true;
		$necessaryVariable = ['cmd','sign','client','version'];
		//判断必须参数是否为空
		if (in_array($name,$necessaryVariable) && empty($value)) 
			throw new \Exception("{$name}参数错误", self::CONFIG_EMPTY);
		//验证签名
		if ($name == 'sign' && ($value != md5("{$this->_client}|{$this->clientKeys[$this->_client]}|{$this->_cmd}")))
			throw new \Exception("sign参数值错误", self::CONFIG_ERROR);
		//验证client
		if ($name == 'client' && (!in_array($value,array_keys($this->clientKeys))))	
			throw new \Exception("client参数值错误", self::CONFIG_ERROR);
		//验证cmd
		if ($name == 'cmd' && (!strstr($value,self::CLASS_METHOD_SPACE)))	
			throw new \Exception("cmd参数值错误", self::CONFIG_ERROR);
		//验证version
		if ($name == 'version' && (!in_array($value,$this->versionList))) 
			throw new \Exception("version is unvalidate", self::CONFIG_ERROR);		
		$variable = "_" . $name;
		if ($this->$variable) 
			throw new \Exception("{$name} variable has already seted", self::CONFIG_ERROR);						   	   
	}
}