<?php
namespace common\services;

use yii\base\Object;
/**
* 文件下载
*/
class DownloadService extends Object
{
	public $file;
    public $uploadName;
	public function downloadWebFile ()
	{
		if (!$this->isFileExist())
			exit('文件不存在');
        if (!$this->uploadName) {
            $ext = strrchr($this->file,".");    
            $this->uploadName = date("YmdHis").$ext;  
        }
		//开始下载
        $file = fopen($this->file,"r");
        header("Content-type: application/octet-stream");    
        header("Accept-Ranges: bytes");    
        header("Accept-Length: ".filesize($this->file));
        header("Content-Disposition: attachment; filename=".$this->uploadName);
        echo fread ($file,filesize($this->file));    
        fclose($file);    
        exit(); 
	}
	public function isFileExist ()
	{
		if (!$this->file) return false;
        $header = get_headers($this->file);
        $statusCode=substr($header[0], 9, 3);

        if ($statusCode == 200 || $statusCode == 304)
            return true;

        return false;
	}
}