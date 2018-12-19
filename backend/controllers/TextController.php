<?php
namespace backend\controllers;

use backend\models\ArticleContent;
/**
* 
*/
class TextController extends CommonController
{
	
	function actionText1 ()
	{
		
	}
	public function actionText2 ()
	{
		$a  = '22222<pre> asss </pre>1111<pre> fgtjyjki </pre>44444<pre> 666666 </pre>hhhhhh';
		$b = $this->pickSubstr($a,'<pre>','</pre>');
		$c = $this->recoverPickStr($b['af_pick'],$b['pick_strs']);
		dump($b);
		dump($this->recoverPickStr($c));
	}
	public function substrArr ($kw1,$mark1,$mark2,$pick_mark = false)
	{
		$kw=$kw1;
		$kw='123'.$kw.'123';
		$st =stripos($kw,$mark1);echo $st;
		$ed =stripos($kw,$mark2);echo $ed;
		if(($st==false||$ed==false)||$st>=$ed)
		return 0;
		if ($pick_mark) {
			$start = $st;
			$end = $ed-$st+strlen($mark2);
		} else {
			$start = $st+strlen($mark1);
			$end = $ed-$st-strlen($mark1);
		}
		$kw = substr($kw,$start,$end);
		return $kw;
	}
	public function pickSubstr($source,$st_mark,$end_mark,$pick_mark = true)
	{
		$return = [
			'origin'	=>	$source,
			'af_pick'	=>	$source,
		];
		if (is_null($st_mark) || is_null($end_mark) || !$source) 
			return $return;

		$st_location = stripos($source,$st_mark);
		$ed_location = stripos($source,$end_mark);

		if (($st_location === false) || $ed_location === false || $st_location >= $ed_location)
			return $return;

		if ($pick_mark) {
			$start = $st_location;
			$end = $ed_location-$st_location+strlen($end_mark);
		} else {
			$start = $st_location+strlen($st_mark);
			$end = $ed_location-$st_location-strlen($st_mark);
		}

		$pick_str = substr($source,$start,$end);
		$pick_key = md5($pick_str . rand(00,99));
		$af_pick = str_replace($pick_str, $pick_key, $source);
		$deep_pick = $this->pickSubstr($af_pick,$st_mark,$end_mark);

		$pick_strs = [
			['pick_str' => $pick_str,'pick_key' => $pick_key],
		];
		if (!is_null($deep_pick['pick_strs'])) $pick_strs = array_merge($pick_strs,$deep_pick['pick_strs']) ;

		$return['af_pick'] = $deep_pick['af_pick'];
		$return['pick_strs'] = $pick_strs;
		return $return;
	}
	public function recoverPickStr ($af_pick,$pick_strs)
	{
		if (!$af_pick || !$pick_strs) 
			return $af_pick;
		
		foreach ($pick_strs as $value) {
			$af_pick = str_replace($value['pick_key'], $value['pick_str'], $af_pick);
		}

		return $af_pick;
	}
}