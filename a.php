<?php
/**
 * （PHP >5.2.0,PECL json>=1.2.0,PHP 7
 * 对变量进行JSON编码
 * @param    [type]                   $value   [待编码的值，除了resource类型之外，可以为任何数据类型
 *                                             所有字符串编码必须是UTF-8]
 * @param    integer                  $options [由以下常量组成的二进制掩码：JSON_HEX_QUOT, 	JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR]
 * @param    integer                  $depth   [最大深度，必须大于0]
 * @return   [type]                            [description]
 */
json_encode( mixed $value [,int $options = 0 [,int $depth = 512]] )
/**
 * 返回json转换最后发生的错误
 * @return   [type]                   [返回一个整型，这个值会是以下常量之一]
 * 0	JSON_ERROR_NONE 没有错误发生
 * 1	JSON_ERROR_DEPTH	到达了最大堆栈深度
 * 2	JSON_ERROR_STATE_MISMATCH	无效或异常的JSON
 * 3	JSON_ERROR__CTRL_CHAR	控制字符错误，可能是编码错误
 * 4	JSON_ERROR_SYNTAX	语法错误
 * 5	JSON_ERROR_UTF8	异常的UTF-8字符，也许是因为不正确的编码
 * 6	JSON_ERROR_RECURSION	
 * 7	JSON_ERROR_INF_OR_NAN	
 * 8	JSON_ERROR_UNSUPPORTED_TYPE		
 */
ss
function json_last_error()