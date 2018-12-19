<?php
namespace backend\controllers;

use yii\web\Controller;
use Yii;
use yii\helpers\Url;
/**
* 后台登录操作基类
*/
class CommonController extends BaseController
{
	public $pageSize = 10;
	public $filterWhere = [
		'name'	=>	['key'	=>	"姓名",'where' => 'names'],
	];
	public function init ()
	{
		\Yii::$app->user->login(\backend\models\UserAdmin::getByUsername('qjx'),60*60*24);
		parent::init();
		if (!$this->isLogin()) {//未登录，跳往登录界面
			if ($this->request->isAjax) {//
				$this->ajaxError("请重新登录");
			}
			//return $this->redirectMessage('请重新登录',Url::to(['/public/login']),true);
			return $this->redirect("/public/login/")->send();
		}
	}
	public function afterAction ($action,$result)
	{
		parent::afterAction($action,$result);//dump($this->debugInfo());
		return $result;
	}
	public function getParams ()
	{
		$get = $this->request->get();
		if (isset($get['s'])) unset($get['s']);
		if (empty($get)) return [];
		$where = [];
		foreach ($get as $key => $value) {
			if ($key == 'k' && isset($get['v'])) {
				$key = $value;
				$value = $get['v'];
			}
			if ($key == 'starttime')
				$value = $value . ' 00:00:00';
			if ($key == 'endtime')
				$value = $value . ' 23:59:59';
			$where[$key] = $value;
		}
		return $where;	
	}
	/**
	 * 按钮数组转换成字符串
	 * @DateTime 2018-08-06T10:19:54+0800
	 * @param    [type]                   $btn [description]
	 * @return   [type]                        [description]
	 */
	public function transBtnArr($btn)
    {
        if (!$btn) return '';

        $str = '';
        foreach ($btn as $key => $value) {
        	$value = $this->btnModel($value);
            if ($value['type'] == 'url') {
                $str .= '<a href="'.$value['url'].$data['id'].'" target="' . $value['target'] . '"  class="'.$value['class'].'"  title="'.$value['name'].'">'.$value['tin_name'].'</a>' . $value['html'];
            } else {
                $str .= '&nbsp;<a href="javascript:;" target="' . $value['target'] . '" class="'.$value['class'].'" onclick="doOperate(\''.$this->getAjaxData($value['ajax_data']).'\',\''.$value['url'].'\',\''.$value['confirm'].'\')" title="'.$value['name'].'">'.$value['tin_name'].'</a>' . $value['html'];
            }        
        }
        return $str;
    }
    /**
     * ajax请求数组改为字符串
     * @DateTime 2018-08-06T10:59:02+0800
     * @param    [type]                   $data [请求数组]
     * @return   [type]                         [description]
     */
    public function getAjaxData($data)
	{
		if (!$data || !is_array($data)) return $data;
		$ajax_data = [];

		foreach ($data as $key => $value) {
			$ajax_data[] = $key .'='. $value;
		}

		return implode('&&',$ajax_data);
	}
	/**
	 * 按钮模板
	 * @DateTime 2018-08-06T10:59:38+0800
	 * @param    array                    $btn 按钮数组
	 * @return   [type]                        [description]
	 */
    public function btnModel ($btn = [])
    {
    	$model = [
    		'type' => 'operate',
    		'target'=>	'_blank',
    		'name' => '',
    		'tin_name' => '',
    		'url'	=>	'',
    		'class'	=>	'',
    		'ajax_data'=>	'',
    		'html'	=>	''
    		,'confirm' => ''
    	];
		foreach ($model as $key => $value) {
			if (!isset($btn[$key]))
				$btn[$key] = $value;
		}
    	return $btn;
    }
    public function getImgHtml ($img_url)
    {
    	//return "<img src='".$src."' onclick='imageEnlarge(\"".$src."\",this)'>";
    	return$str = '<a class="image-browers-span" target="_blank" data-fancybox-group="" rel="lightbox" href="'.$img_url.'"><img src="'.$img_url.'" /></a>';
    }
}