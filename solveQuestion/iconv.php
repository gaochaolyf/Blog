<?php
/*
 * ������һЩ���� �� ���Ľ������ 
 * 
 * 1��������� ��ĳЩ������ת����Unicode���� ��ΪPHP�����������ú���֧��Unicode���� �����ƽ���Ҫ --��
 * mb_convert_encoding ����ָ������������룬������������Զ�ʶ�𣬵���ִ��Ч�ʱ�iconv��̫�ࡣ
 * һ��������� iconv��ֻ�е������޷�ȷ��ԭ�����Ǻ��ֱ��룬����iconvת�����޷�������ʾʱ����mb_convert_encoding ����
 */

$name = '��';
// $name = iconv($name, 'UCS-2', 'UTF-8'); �쳣 ת�������ı��������� Ҫʹ������ĺ���ת������
$name = mb_convert_encoding ( $name, 'UCS-2', 'UTF-8' ); // ����
$len = strlen ( $name );
$str = '';
for($i = 0; $i < $len - 1; $i = $i + 2) {
	$c = $name [$i];
	$c2 = $name [$i + 1];
	if (ord ( $c ) > 0) { // �����ֽڵ�����
		$str .= '%25u' . mb_strtoupper ( base_convert ( ord ( $c ), 10, 16 ) ) . mb_strtoupper ( str_pad ( base_convert ( ord ( $c2 ), 10, 16 ), 2, 0, STR_PAD_LEFT ) );
	} else {
		$str .= $c2;
	}
}



