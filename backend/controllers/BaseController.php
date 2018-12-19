<?php
namespace backend\controllers;

use yii\web\Controller;
use Yii;
/**
* 后台操作基类
*/
class BaseController extends \common\components\BaseController
{
    const MSG_NORMAL = 0;
    const MSG_SUCCESS = 1;
    const MSG_ERROR = 2;
	public $layout = 'header';
	public function isLogin ()
	{
        return !\Yii::$app->user->getIsGuest();
		return \Yii::$app->session['user'];
	}
	public function assign ($name,$value,$recover = true)
	{
		$view = $this->app->getView();
		if (empty($name)) return;
		$view->params[$name] = $value;
	}
	/*
	导出csv格式的excel文件
	 */
	//设置文件名 ***.csv
	protected function _setcsvHeader($filename)
    {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-type: application/vnd.ms-excel; charset=utf8");
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        //设置utf-8 + bom ，处理汉字显示的乱码
        print(chr(0xEF) . chr(0xBB) . chr(0xBF));
    }
    //导出数据
	protected function _array2csv(array &$array)
    {
        if (count($array) == 0) {
            return null;
        }
        set_time_limit(0);//响应时间改为60秒
        ini_set('memory_limit', '512M');
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }
    public function goHome ()
    {
        return $this->redirect('/')->send();
    }
    public function redirectMessage ($message,$url = '',$isParent = false,$msgType = self::MSG_NORMAL)
    {
        return $this->render('/message',[
            'url' => $url
            ,'message' => $message
            ,'isParent' => $isParent
        ]);
    }
}