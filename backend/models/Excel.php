<?php
namespace backend\models;

use PHPExcel;
use PHPExcel_Writer_Excel5;
use PHPExcel_IOFactory;
/**
* 
*/
class Excel
{
	public $excel_index = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',"AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ"];

	public $error;
	public $savePath;
	//
	public function __construct ()
	{
		// require_once(VENDOR_PATH . '/PHPExcel/PHPExcel.php');
		// require_once(VENDOR_PATH . '/PHPExcel/PHPExcel/IOFactory.php');
		// require_once(VENDOR_PATH . '/PHPExcel/PHPExcel/Writer/Excel5.php');
		$this->savePath = UPLOAD_PATH . "Excel" . DS;
	}
	/*
	导出循环数据的excel
	@param:cols excel列标题
	@param:data 数据
	@param:config Excel配置
	 */
	public function exportLoopExcel ($cols = '',$data = '',$config = [])
	{
		$PHPExcel = $this->getPHPExcelObject();
		$sheet = $PHPExcel->getActiveSheet();
		$sheet = $this->setLoopCell($cols,$data,$sheet,$config["colsLine"]?:1,$config["dataLine"]?:2,$config["combine"]?:null);
		if (empty($config['type'])) {
			$this->exportToBrowser($PHPExcel,$config['name']?:date("Y年m月d日").rand(0000,9999).".xls");
		}
	}
	private $_phpExcelObject;
    public function getPHPExcelObject(){
        if($this->_phpExcelObject===null){
            $this->_phpExcelObject = new \PHPExcel();
        }
        return $this->_phpExcelObject;
    }
    /**
     * 同一文件多sheet导出EXCEL
     * @param  array  $data   [导出的数据]
     *                       ex. $data = 
     *  [
        	[
        		'cols' => [
        			['name','姓名'],
        			['age','年龄'],
        			['sex','行别'],
        		],
        		'data'	=> [
        			['name' => 'jack','age' => 23, 'sex' => '男'],
        			['name' => 'lucy','age' => 25, 'sex' => '女'],
        		],
        		'config' =>	[]
        	],
        	[
        		'cols' => [
        			['class','班级'],
        			['mate','同学'],
        			['grade','成绩'],
        		],
        		'data'	=> [
        			['class' => '36','mate' => '张三', 'grade' => 200],
        			['class' => '32','mate' => '李四', 'grade' => 150],
        		],
        		'config' =>	[]
        	],
        ];
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    public function exportMultiSheet ($data = [],$config = []) 
    {
    	$PHPExcel = $this->getPHPExcelObject();
    	$pro = $PHPExcel->getProperties();
    	$this->setProperties ($pro,$config);

    	$sheetLen = count($data);
    	$cols = [];
    	$sheetData = [];

    	for ($i=0; $i < $sheetLen; $i++) { 
    		$cols = $data[$i]['cols'];
    		$sheetData = $data[$i]['data'];
    		$sheetConfig = $data[$i]['config'];

    		if ($i !== 0) $PHPExcel->createSheet();

    		$PHPExcel->setactivesheetindex($i);
    		$sheet = $PHPExcel->getActiveSheet();
    		if ($sheetConfig['sheetName']) $sheet->setTitle($sheetConfig['sheetName']);
    		$sheet = $this->setLoopCell($cols,$sheetData,$sheet,$sheetConfig["colsLine"]?:1,$sheetConfig["dataLine"]?:2,$sheetConfig["combine"]?:null);
    	}
    	
    	if (empty($config['type'])) {
			$this->exportToBrowser($PHPExcel,$config['name']?:date("Y年m月d日").rand(0000,9999).".xls");
		}
    }
    /**
     * 设置EXCEL文件属性
     * @param [type] $proObj     [description]
     * @param array  $properties [description]
     */
    public function setProperties ($proObj,$properties = [])
    {
    	if (empty($properties) || !$proObj) return $proObj;

    	$obpe_pro->setCreator($properties['creator'])//设置创建者
         ->setLastModifiedBy($properties['time']?:date('Y-m-d H:i:s'))//设置时间
         ->setTitle($properties['title'])//设置标题
         ->setSubject($properties['remark'])//设置备注
         ->setDescription($properties['description'])//设置描述
         ->setKeywords($properties['keywords'])//设置关键字 | 标记
         ->setCategory($properties['categories']);//设置类别
    }
    /*
	excel循环数据设置
	@param:cols 循环数据标题
	@param:data 循环数据
	@param:sheet Excel单元设置对象 eg: $PHPExcel->getActiveSheet();
	@param:colsLine 标题所在行 默认第一行
	@param:dataLine 数据循环开始显示行 默认第二行
	@param:combie 每列合并单元格列数 默认一列
	@return:object
	 */
	public function setLoopCell ($cols,$data,$sheet,$colsLine = 1,$dataLine = 2,$combine = null)
	{
		$word = $this->excel_index;
		foreach ($cols as $key => $value) {
			if (is_null($combine) || !is_numeric($combine)) {
                                $sheet->getStyle($word[$key].$colsLine)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);//加边框;
				$sheet->setCellValue($word[$key].$colsLine,$value[1]);
			} else {
				$sheet->mergeCells($word[$key*$combine].$colsLine.":".$word[($key+1)*$combine-1].$colsLine);
				$sheet->setCellValue($word[$key*$combine].$colsLine,$value[1]);
                                $sheet->getStyle($word[$key*$combine].$colsLine)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);//加边框;
			}		
		}
		if ($data) {
			$i = $dataLine;
			$cols_count = count($cols);
			foreach ($data as $val) {
				if (is_null($combine) || !is_numeric($combine)) {
					for ($j = 0;$j < $cols_count;$j++) {
                                                $cellFormat = isset($cols[$j][2])?$cols[$j][2]:\PHPExcel_Cell_DataType::TYPE_STRING;
						$sheet->setCellValueExplicit($word[$j].$i,$val[$cols[$j][0]],$cellFormat);
						$sheet->getStyle($word[$j].$i)->getNumberFormat()->setFormatCode("@");
                                                $sheet->getStyle($word[$j].$i)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);//加边框;
					}
				} else {
					for ($j = 0;$j < $cols_count;$j++) {
						$sheet->mergeCells($word[$j*$combine].$i.":".$word[($j+1)*$combine-1].$i);
						$sheet->setCellValueExplicit($word[$j*$combine].$i,$val[$cols[$j][0]],\PHPExcel_Cell_DataType::TYPE_STRING);
						$sheet->getStyle($word[$j*$combine].$i)->getNumberFormat()->setFormatCode("@");
                                                $sheet->getStyle($word[$j*$combine].$i)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);//加边框;
					}
				}
				$i++;
			}
		}
		return $sheet;
	}    
	/*
	Excel 数据通过浏览器下载
	@param:PHPExcel excel对象
	@param:filename 下载文件名
	 */
	public function exportToBrowser ($PHPExcel,$filename)
	{
		$xlsWriter = new \PHPExcel_Writer_Excel5($PHPExcel);
		$filename = $filename?$filename:time().".xls";
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$filename.'"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $xlsWriter->save( "php://output" );
        exit();
	}
	public function saveExcelFile ($PHPExcel,$filename)
	{
		if (!is_dir($this->savePath)) {
			if(false == mkdir($this->savePath,0777,true)) {
				$this->error = "创建目录失败";
				return false;
			}
		}
		$saveFile = $this->savePath . $filename;
		$xlsWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");
		if (false == $xlsWriter->save($saveFile,true)) {
			$this->error = "保存文件失败";
			return false;
		}
		return $saveFile;
	}
	/*
	导入Excel表格并返回数据

	 */
	public function import_excel($file){
	    // 判断文件是什么格式
	    $type = pathinfo($file); 
	    $type = strtolower($type["extension"]);
	    $type=$type==='csv' ? $type : 'Excel5';
	    ini_set('max_execution_time', '0');
	    // 判断使用哪种格式
	    $objReader = \PHPExcel_IOFactory::createReader($type);
	    $objPHPExcel = $objReader->load($file); 
	    $sheet = $objPHPExcel->getSheet(0); 
	    // 取得总行数 
	    $highestRow = $sheet->getHighestRow();     
	    // 取得总列数      
	    $highestColumn = $sheet->getHighestColumn(); 
	    //循环读取excel文件,读取一条,插入一条
	    $data=array();
	    //从第一行开始读取数据
	    for($j=1;$j<=$highestRow;$j++){
	        //从A列读取数据
	        for($k='A';$k<=$highestColumn;$k++){
	            // 读取单元格
	            $data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
	        } 
	    }  
	    return $data;
	}
}