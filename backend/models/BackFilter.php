<?php
namespace backend\models;

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
/**
* 
*/
class BackFilter
{
	static $un_addtime = [];
	static $un_lasttime = [];
	static $un_operate = [];
	public static function getFilter ($type,$params = [])
	{
		if (!$type) return [];
		$filters = self::filters();
		if (!isset($filters[$type])) return [];
		$filter = $filters[$type];
		$defaults = self::defaultKey();
		$return = [];
		foreach ($filter as $item) {
			foreach ($defaults as $key => $default) {
				if (!isset($item[$key]))
					$item[$key] = $default['default'];
			}
			if (isset($params[$item['key']]))
				$item['value'] = $params[$item['key']];
			$return[] = $item;
		}
		return $return;
	}
	public static function filters ()
	{
		$filters = [
            'banner' => [
            	['name' => '键','key' => 'k','type' => 'select','class' => 'abc input-default top-input mid']
          		,['name' => '值','key' => 'v','class' => 'abc input-default top-input mid']
            	,['name' => '状态','key' => 'status','type' => 'select','class' => 'form-control mid']          
            ]
            ,'label' => [
            	['name' => '键','key' => 'k','type' => 'select','class' => 'abc input-default top-input mid']
          		,['name' => '值','key' => 'v','class' => 'abc input-default top-input mid']
            	,['name' => '状态','key' => 'status','type' => 'select','class' => 'form-control mid']          
            ]
            ,'article' => [
            	['name' => '键','key' => 'k','type' => 'select','class' => 'abc input-default top-input mid']
          		,['name' => '值','key' => 'v','class' => 'abc input-default top-input mid']
            	,['name' => '状态','key' => 'status','type' => 'select','class' => 'form-control mid']  
            	,['name' => '置顶','key' => 'top','type' => 'select','class' => 'form-control mid']  
            	,['name' => '最新','key' => 'new','type' => 'select','class' => 'form-control mid']
            	,['name' => '标签','key' => 'label','type' => 'select','class' => 'form-control mid']         
            ]
        ];
		return $filters;
	}
	public static function defaultKey ()
	{
		return ['name' => ['default' => ''],'key' => ['default' => ''],'type' => ['default' => 'normal'],'value' => ['default' => ''],'class' => ['default' => 'form-control']];
	}
	public static function filterHtml ($type,$params = [])
	{
		$filters = self::getFilter($type,$params);
		$str = '';
		ActiveForm::begin(['id' => 'searchform']);
		if ($filters) {
			foreach ($filters as $key => $value) {
				$str .= '<label class="layui-form-label">'.$value['name'].'：</label><div class="layui-input-inline">';
				switch ($value['type']) {
					case 'select':
						$str .= Html::dropDownList($value['key'],\Yii::$app->getRequest()->get($value['key']),$value['value'],['class'=>$value['class']]);
						break;
					case 'time':
						$str .= Html::input('text',$value['key'],\Yii::$app->getRequest()->get($value['key']),['onfocus' => "WdatePicker({dateFmt:'yyyy-MM-dd'})",'class' => $value['class']]);
						break;
					default:
						$str .= Html::input('text',$value['key'],\Yii::$app->getRequest()->get($value['key']),['class' => $value['class']]);
						break;
				}
				$str .= '</div>';
			}
			$str .= '<input type="submit" name="search_submit"  value="查询" class="btn btn-sm btn-white">';
		}
		return $str;
	}
	static function getParams ()
	{
		$get = \Yii::$app->getRequest()->get();
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
	public static function allRule ()
	{
		return [
			'banner' => [
				'id' => ['type' => 'normal','value' => ['=','id','_VALUE_']]
				,'status' => ['type' => 'normal','value' => ['=','status','_VALUE_']]
				,'title' => ['type' => 'normal','value' => ['like','title','_VALUE_']]
			]
			,'label' => [
				'id' => ['type' => 'normal','value' => ['=','id','_VALUE_']]
				,'status' => ['type' => 'normal','value' => ['=','status','_VALUE_']],
				'name' => ['type' => 'normal','value' => ['like','name','_VALUE_']]
			]
			,'article' => [
				'id' => ['type'=>'normal','value' => ['=','id','_VALUE_']]
				,'status' => ['type' => 'normal','value' => ['=','status','_VALUE_']]
				,'new' => ['type' => 'normal','value' => ['=','is_new','_VALUE_']]
				,'top' => ['type' => 'normal','value' => ['=','is_top','_VALUE_']]
				,'names' => ['type' => 'method','value' => ['in','author',['class' => '\backend\models\UserAdmin','method' => 'getUserIdsByNames']]]
				,'label' => ['type' => 'method','value' => ['in','major_label',['class' => '\common\services\ArticleService','method' => 'getMajorLabelTree']]]
			]
		];
	}
	public static function whereRule ($type)
	{
		$rules = self::allRule();
		$params = self::getParams();
		if (!isset($rules[$type]) || !($rule = $rules[$type]) || !$params) return [];
		$where = [];
		foreach ($params as $key => $value) {
			if (isset($rule[$key]) && $value) {
				$rule_val = $rule[$key]['value'];
				switch ($rule[$key]['type']) {
					case 'normal':
						$rule_val[2] = str_replace('_VALUE_',$value,$rule[$key]['value'][2]);
						$where[] = $rule_val;
						break;
					case 'method':
						$rule_val[2] = call_user_func_array([$rule_val[2]['class'],$rule_val[2]['method']], [$value]);
						$where[] = $rule_val;
						break;
				}	
			}
		}
		if ($where) array_unshift($where,'and');
		return $where;
	}
	public static function addAllSelect ($select)
	{
		$return = ['全部'];
		foreach ($select as $key => $value) {
			$return[$key] = $value;
		}
		return $return;
	}
}