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
