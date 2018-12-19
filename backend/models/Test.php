<?php
namespace backend\models;

/**
* 标签管理
*/
class Test extends Common
{
	public static function tableName ()
	{
		return "{{test}}";
	}

	public function edit ($testid)
	{
		if (!$testid) return $this->error('testid错误');
		$testInfo = self::findOne($testid);
		$testInfo->update_num = $testInfo->update_num+1;
		if ($testInfo->save(false)) return $this->error('更新失败');
		return true;
	}
	public static function amountAdd ($num)
	{
		$each = 100;
		if ($num >= $each) {
			$times = floor((int)$num/$each);
			$extra = $num%$each;
		} else {
			$times = 1;
			$extra = -($each-$num%$each);
		}
		$i_sql = 'INSERT INTO `test` (`test_uniqued`,`test_content`,`test_combine_a`,`test_combine_b`,`status`) VALUES ';
		$datas = [];

		for ($i=0; $i < $times; $i++) {		
			$v_sql = '';
			$time = ($i == ($times - 1))?($each+$extra):$each; 
			for ($j=0; $j < $time; $j++) { 			
				$data = [];
				$data['test_uniqued'] = "'" . uniqid() . "'";
				$data['test_content'] = "'" . self::getChar(rand(50,100)) . "'";
				$data['test_combine_a'] = rand(0,99999);
				$data['test_combine_b'] = rand(0,99999);
				$data['status'] = rand(1,2);
				$datas[] = $data;
				$v_sql .= '(' . implode(',', $data) . '),';
			}	
			$sql = $i_sql . trim($v_sql,',');
			\Yii::$app->db->createCommand($sql)->execute();
		}		
	}
	public function behaviors ()
	{
		return [];
	}
	private static function getChar($num)  // $num为生成汉字的数量
    {
        $b = '';
        for ($i=0; $i<$num; $i++) {
            // 使用chr函数拼接双字节汉字，前一个chr为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }
}