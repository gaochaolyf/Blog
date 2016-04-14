<?php
/*
 * 1、编码相关 将某些中文字转化成Unicode编码 因为PHP并不存在内置函数支持Unicode编码 但是破解需要 --！ 
 * mb_convert_encoding 可以指定多种输入编码，它会根据内容自动识别，但是执行效率比iconv差太多。 
 * 一般情况下用 iconv，只有当遇到无法确定原编码是何种编码，或者iconv转化后无法正常显示时才用mb_convert_encoding 函数
 */
$name = '渝';
// $name = iconv($name, 'UCS-2', 'UTF-8'); 异常 转换出来的编码有问题 要使用下面的函数转换才行
$name = mb_convert_encoding ( $name, 'UCS-2', 'UTF-8' ); // 正常
$len = strlen ( $name );
$str = '';
for($i = 0; $i < $len - 1; $i = $i + 2) {
	$c = $name [$i];
	$c2 = $name [$i + 1];
	if (ord ( $c ) > 0) { // 两个字节的文字
		$str .= '%25u' . mb_strtoupper ( base_convert ( ord ( $c ), 10, 16 ) ) . mb_strtoupper ( str_pad ( base_convert ( ord ( $c2 ), 10, 16 ), 2, 0, STR_PAD_LEFT ) );
	} else {
		$str .= $c2;
	}
}

echo $str;

/**
 * 2.json_decode
 * 带BOM的utf-8，用json_decode() 返回null的问题。
 */

$data_html = str_replace ( '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">', '', $data_html );
//$json_array = json_decode ( $data_html, true ); 有可能返回NULL 由于网页有可能存在Bom头部的问题
$json_array = json_decode ( trim ( $data_html, chr ( 239 ) . chr ( 187 ) . chr ( 191 ) ), true ); //正常转化成数组




