<?php
namespace backend\models;

/**
* 文章内容处理
* 	处理文章代码块样式
*  	处理图片大小等问题
*/
class ArticleContent
{
	/**
	 * 文章类型 PHP JS HTML 等
	 * @var [type]
	 */
	public $type = "php";
	/**
	 * 原始文章内容
	 * @var [type]
	 */
	public $originContent;
	/**
	 * [处理完成的文章内容
	 * @var [type]
	 */
	public $finalContent;
	/**
	 * 字符串替换数组
	 * @var array
	 */
	public $trans = [];
	public $preg  = [
		'pattern'		=>	[],
		'replacement'	=>	[],
	];
	public $flip = false;
	/*
	是否添加样式
	 */
	public static $is_handle = true;
	/**
	 * 获取类的实例
	 * @return [type] [description]
	 */
	public static function getStatic ()
	{
		return new static();
	}
/**
 * 处理文章外部接口
 * @param  [string] $content 需要处理的文章内容
 * @param  string $type    [description]
 * @return [type]          [description]
 */
public static function handContent ($content,$params = [])
{
	if (!self::$is_handle)
		return $content;

	if (empty($content))return "";
	$model = ArticleContent::getStatic();
	foreach ($params as $key => $value) {
		if (property_exists($model,$key))
			$model->$key = $value;
	}

	$model->originContent = $model->finalContent = $content;
	$model->contentTidy();
	return $model->finalContent;
}
	public function contentTidy()
	{
		if (false == $this->isAllowTrans()) return false;
		$this->setTransArr();
		$this->setPreg();
		if ($this->flip) {

			$this->flipRule();		
			if ($this->trans)
				$this->finalContent = strtr($this->finalContent,$this->trans);

		} else {
			if ($this->preg) {
				$this->finalContent = preg_replace($this->preg['pattern'],$this->preg['replacement'],$this->finalContent);
			}

			if ($this->trans)
				$this->finalContent = strtr($this->finalContent,$this->trans);
		}

			
	}
	public function isAllowTrans ()
	{
		if (false == $this->haveArea("code")) return false;
		return true;
	}
	public function flipRule ()
	{
		$this->trans = [];
		$addTrans = [];
		foreach ($this->addStr() as $key => $value) {
			$addTrans[$value] = "";
		}
		$this->addTrans($addTrans);
	}
	public function setPreg ()
	{
		$functionStart = $this->addStr("code-function");
		$thisStart = $this->addStr("code-this");
		$functionName = $this->addStr("code-function-name");
		$annotationsStart = $this->addStr("code-annotations");
		$codeKey = $this->addStr("code-key");
		$end = $this->addStr("end");
		$stringStart = $this->addStr("code-string");
		$constantStart = $this->addStr("code-constant-name");

		$preg = [	
			'/(::)(^\$)([A-Z])*/' => $constantStart.'${0}'.$end,
			'/(\=&nbsp;|\-&gt;|\-\>|::|\()(.*?)(\()+/'	=>	'${1}'.$functionStart.'${2}'.$end.'${3}',
			'/(\$this\-&gt;|self::|static::)/' =>	$thisStart.'${1}'.$end,	
			//'/(function)(.*?)(\(|\,)(\$\w)(\,|\))/' =>	'${1}${2}${3}'.$thisStart.'{$4}'.$end.'${5}',
			'/(function)(\s|&nbsp;)+(.*?)(\s|&nbsp;)*(\()/'	=>	'${1}${2}'.$functionName.'${3}'.$end.'${4}${5}',
			'/(new)(\s|&nbsp;)+(.*?)(\s|&nbsp;)*(\()/'	=>	'${1}${2}'.$functionStart.'${3}'.$end.'${4}${5}',
			'/(class|Class|interface|Interface|Trait|trait)(\s|&nbsp;)+(.*?)(\s|&nbsp;)*(\{)/' => $functionStart.'${1}'.$end.'${2}'.$functionName.'${3}'.$end.'${4}${5}',
			'/(const)(\s|&nbsp;)+(.*?)(\s|&nbsp;)*(\=)/' => '${1}${2}'.$constantStart.'${3}'.$end.'${4}${5}',
			'/(function)(.*?)(\()(.*?)(\))/' => '${1}${2}${3}'.$thisStart.'${4}'.$end.'${5}',
			'/(\/\/)(.*?)(\t|\n|\<br\/\>|\<)/'	=>	$annotationsStart.'${1}${2}'.$end.'${3}',
			'/(&nbsp;|\s)(\=|\>|\<|\>\=|&gt;\=)(&nbsp;|\s)/'	=>	'${1}'.$codeKey.'${2}'.$end.'${3}',
			'/(\(|\s|&nbsp;)(&#39;)(.*?)(&#39;)/'	=>	'${1}'.$stringStart.'${2}${3}${4}'.$end,
			'/(\(|\s|&nbsp;)(&quot;)(.*?)(&quot;)/'	=>	'${1}'.$stringStart.'${2}${3}${4}'.$end,
			'/(use)(\s|&nbsp;)+(.*?)(\s|&nbsp;|;)/'	=>	$codeKey.'${1}'.$end.'${2}'.$functionName.'${3}'.$end.'${4}', 
		];
		$this->addPreg($preg);
	}
	public function addPreg ($preg)
	{
		if (!is_array($preg)) return false;
		foreach ($preg as $key => $value) {
			$this->preg['pattern'][] = $key;
			$this->preg['replacement'][] = $value;
		}
	}
	public function setTransArr ()
	{
		$this->setCodeTrans();
	}
	public function setCodeTrans ()
	{
		$this->addTrans("<pre class","<pre ".$this->addStr("code-pre")." class");
		$this->setAnnotationsTrans();//注释替换
		$this->setCodeKey();//代码key值替换
		$this->setNomarl();//设置默认属性
	}
	public function setNomarl ()
	{
		$start = $this->addStr("code-nomarl");
		$end = $this->addStr("end");
		$trans = [
			//"("	=>	$start."(".$end,
		];
		$this->addTrans($trans);
	}
	/**
	 * 给注释添加区分标签
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-28T18:59:32+0800
	 */
	public function setAnnotationsTrans ()
	{
		$trans = [
			"/*"		=>	$this->addStr("code-annotations")."/*",
			"*/"		=>	"*/".$this->addStr("end"),
		];
		$this->addTrans($trans);
	}
	/**
	 * 给内容关键字添加区别标签
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-28T18:58:45+0800
	 */
	public function setCodeKey ()
	{
		$trans = [];
		$start = $this->addStr("code-key");
		$end = $this->addStr("end");

		$oneEmptyKey = $this->getOneEmptyKey();//单空格key
		foreach ($oneEmptyKey as $value) {	
			$val = $value."&nbsp;";
			$trans[$val] = $start.$val.$end;
			$val = "&nbsp;".$value;
			$trans[$val] = $start.$val.$end;
		}

		$doubleEmptyKey = $this->getDoubleEmptyKey();//双空格key
		foreach ($doubleEmptyKey as $value) {	
			$val = "&nbsp;".$value."&nbsp;";
			$trans[$val] = $start.$val.$end;
		}

		$noEmptyKey = $this->getNoEmptyKey();//无空格key
		foreach ($noEmptyKey as $value) {	
			$trans[$value] = $start.$value.$end;
		}

		$functionKey = $this->getFunctionKey();//函数key
		$functionStart = $this->addStr("code-function");
		foreach ($functionKey as $value) {	
			$val = $value."&nbsp;";
			$trans[$val] = $functionStart.$val.$end;
		}

		$constantName = $this->getConstantName();//常量
		$constantStart = $this->addStr("code-constant-name");
		foreach ($constantName as $value) {
			$val = "&nbsp;".$value."&nbsp;";
			$trans[$val] = $constantStart.$val.$end;
		}

		$this->addTrans($trans);
	}
	/**
	 * 单空格关键字
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-28T18:58:21+0800
	 * @return   [type]                   [description]
	 */
	public function getOneEmptyKey ()
	{
		$keys = [
			'public','if','elseif','return','break','continue',"@param","@return","static",'foreach','switch','const','protected','private','implements','extends','for','insteadof'
			//"===","!==","<=","=>","=&gt;","->","-&gt;","<>","!=","!","==",
		];
		return $keys;
	}
	/**
	 * php双空格关键字
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-28T18:53:39+0800
	 * @return   [type]                   [description]
	 */
	public function getDoubleEmptyKey ()
	{
		$keys = ['as','extends','new','-','+','||','&&']; 
		return $keys;
	}
	/**
	 * php无空格关键字
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-28T18:53:24+0800
	 * @return   [type]                   [description]
	 */
	public function getNoEmptyKey ()
	{
		//&gt; 代表 >
		$keys = ["::","===","!==","<=","=>","=&gt;","->","-&gt;","<>","!=","!","==",'.=']; 
		return $keys;
	}
	/**
	 * 系统常量
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-28T18:53:00+0800
	 * @return   [type]                   [description]
	 */
	public function getConstantName ()
	{
		$keys = ['true','false','null','TRUE','NULL','FALSE']; 
		return $keys;
	}
	/**
	 * 系统函数关键字
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-28T18:23:10+0800
	 * @return   [type]                   [description]
	 */
	public function getFunctionKey ()
	{
		$keys = ["function",'class','interface','echo','print_r'];
		return $keys;
	}
	public function addTrans ($name,$value = "")
	{
		if (is_array($name)) {
			$this->trans = array_merge($this->trans,$name);
		} elseif (is_string($name)) {
			$this->trans[$name] = $value;
		} 
	}
	/**
	 * 内容添加的样式标签
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-28T18:52:09+0800
	 * @param    string                   $name [样式名]
	 */
	public function addStr ($name = '')
	{
		$str = [
			"end"	=>	"</font preg>",
			"code-pre"	=>	"style='color:#eee;background:#272822;'",
			"code-annotations"	=>	"<font class='code-annotations'>",
			"code-key"	=>	"<font class='code-key'>",
			"code-function"	=>	"<font class='code-function'>",
			"code-nomarl"	=>	"<font class='code-nomarl' style='color:#eee'>",
			"code-this"		=>	"<font class='code-this'>",
			'code-function-name'	=>	"<font class='code-function-name'>",
			"code-string"	=>	"<font class='code-string'>",
			'code-constant-name'	=>	"<font class='code-constant-name'>",
		];
		return $name?$str[$name]:$str;
	}
	/**
	 * 判断文章内容是否包含某个区域
	 * @param  [string] $name 区域名
	 * @return [boolean]       [description]
	 */
	public function haveArea ($name)
	{
		if (empty($name) || (false == $areaKey = $this->getAreaKey($name)))return false;

		$isHave = false;
		foreach ($areaKey as $value) {
			if (false !== stripos($this->originContent,$value)) {
				$isHave = true;
				break;
			}
		}

		return $isHave;
	}

	/**
	 * 获取区域是否存在的关键字
	 * @param  string $name 区域名
	 * @return [type]       [description]
	 */
	public function getAreaKey ($name = '')
	{
		$keys = [
			'code'	=>	['<pre'],
		];
		return $name?$keys[$name]:$keys;
	}
	
}