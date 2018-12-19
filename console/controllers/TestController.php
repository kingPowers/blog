<?php
namespace console\controllers;

use yii\console\Controller;

/**
* 
*/
class TestController extends Controller
{
	public function optiosName ($name)
	{
		return ['name' => $name];
	}
	/**
	 * [actionMultiadd description]
	 * @Author   Quanjiaxin
	 * @DateTime 2018-06-08T11:48:33+0800
	 * @param    [type]                   $time [description]
	 * @return   [type]                         [description]
	 */
	public function actionMultiadd ($time)
	{
		header('Content-type:test/html;charset=utf-8');
		\backend\models\Test::amountAdd($time);
		echo '--' . date("Y-m-d H:i:s") .'--add success:' . $time . ' records has been add'; 
	}
}