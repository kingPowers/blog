<?php

if (!function_exists('dump')) {
	function dump($var, $echo = true, $label = null, $flags = ENT_SUBSTITUTE)
	{
        $label = (null === $label) ? '' : rtrim($label) . ':';
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        if (IS_CLI) {
            $output = PHP_EOL . $label . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, $flags);
            }
            $output = '<pre>' . $label . $output . '</pre>';
        }
        if ($echo) {
            echo($output);
            return;
        } else {
            return $output;
        }
	}
}
if (!function_exists("object2array")) {
    function object2array($stdclassobject,$deep = 1)
    {
        if ($deep >= 10)return "";
        $_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;
        $array = [];
        foreach ($_array as $key => $value) {
            $value = (is_array($value) || is_object($value)) ? object2array($value,$deep++) : $value;
            $array[$key] = $value;
        }
        return $array;
    }
}
if (!function_exists("console_log")) {
    function console_log($data)
    {
        if (empty($data)) $data = "null";
        if (is_array($data))
        {
            echo("<script>console.log(eval('('+'".json_encode($data)."'+')'));</script>");
        } elseif (is_object($data)) {
            //$data = object2array($data);
            $data = (array)$data;
            echo("<script>console.log(eval('('+'".json_encode($data)."'+')'));</script>");
        }
        else
        {
            echo("<script>console.log('".$data."');</script>");
        }
    }
}
/**
 * 根据前后标识提取子字符串
 * @Author   Quanjiaxin
 * @DateTime 2018-07-20T15:25:16+0800
 * @param    [type]                   $source    [源字符串]
 * @param    [type]                   $st_mark   [开始标识]
 * @param    [type]                   $end_mark  [结束标识]
 * @param    boolean                  $pick_mark [标识是否提取]
 * @return   [type]                              [description]
 */
function pickSubstr($source,$st_mark,$end_mark,$pick_mark = true)
{
    $return = [
        'origin'    =>  $source,
        'af_pick'   =>  $source,
        'pick_strs' => []
    ];
    if (is_null($st_mark) || is_null($end_mark) || !$source) 
        return $return;

    $st_location = mb_strpos($source,$st_mark,0,'utf-8');
    $ed_location = mb_strpos($source,$end_mark,$st_location + 1,'utf-8');//防止两个标识一样

    if (($st_location === false) || $ed_location === false || $st_location >= $ed_location)
        return $return;

    if ($pick_mark) {
        $start = $st_location;
        $end = $ed_location - $st_location + mb_strlen($end_mark,'utf-8');
    } else {
        $start = $st_location + mb_strlen($st_mark,'utf-8');
        $end = $ed_location - $st_location - mb_strlen($st_mark,'utf-8');
    }

    $pick_str = mb_substr($source,$start,$end,'utf-8');
    $pick_key = md5($pick_str . rand(00,99) . $st_location . $ed_location);
    $af_pick = str_replace($pick_str, $pick_key, $source);
    $deep_pick = pickSubstr($af_pick,$st_mark,$end_mark);

    $pick_strs = [
        ['pick_str' => $pick_str,'pick_key' => $pick_key],
    ];
    if (isset($deep_pick['pick_strs']) && $deep_pick['pick_strs']) $pick_strs = array_merge($pick_strs,$deep_pick['pick_strs']) ;

    $return['af_pick'] = $deep_pick['af_pick'];
    $return['pick_strs'] = $pick_strs;
    return $return;
}
/**
 * 恢复提取的字符串
 * @Author   Quanjiaxin
 * @DateTime 2018-07-20T15:25:50+0800
 * @param    [type]                   $af_pick   [提取后的字符串]
 * @param    [type]                   $pick_strs [提取到的字符串数组]
 * @return   [type]                              [description]
 */
function recoverPickStr ($af_pick,$pick_strs)
{
    if (!$af_pick || !$pick_strs) 
        return $af_pick;
    
    foreach ($pick_strs as $value) {
        $af_pick = str_replace($value['pick_key'], $value['pick_str'], $af_pick);
    }

    return $af_pick;
}
if (!function_exists('getSql')) {
    function getSql ($query) {
        if (!is_object($query)) return 'this veriable is not a object';
        if (!method_exists($query, 'createCommand'))
            return 'this object not has a method named createCommand';
        return $query->createCommand()->getRawSql();
    }
}
