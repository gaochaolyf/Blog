<?php
/**
 * openssl 加密解密
 */
class openSSL {
	private $filePath = "/rsa_private_key.pem";
	private $filePath_public = "/rsa_public_key.pem";
	
	/**
	 * 根据原文生成签名内容
	 *
	 * @param array $data 加密的数组        	
	 * @return string
	 */
	function sign($data) {
		if (! file_exists ( __DIR__ . $this->filePath )) {
			return false;
		}
		
		$param = self::paraFilter ( $data ); // 过滤sign签名和空参数
		$param = self::argSort ( $param ); // 数组重组排序
		$signValue = self::parseUrlParamString ( $param ); // URL 参数以“&”拼接字符串
		
		$pkcs12 = file_get_contents ( __DIR__ . $this->filePath );
		$binarySignature = "";
		if (openssl_sign ( $signValue, $binarySignature, $pkcs12, OPENSSL_ALGO_SHA1 )) {
			return base64_encode ( $binarySignature );
		} else {
			return '';
		}
	}
	
	/**
	 * 验证签名自己生成的是否正确
	 *
	 * @param string $data
	 *        	签名的原文
	 * @param string $signature
	 *        	签名
	 * @return boolean
	 */
	function verifySign($data, $signature) {
		if (! file_exists ( __DIR__ . $this->filePath_public )) {
			return false;
		}
		
		$pkcs12 = file_get_contents ( __DIR__ . $this->filePath_public );
		$ok = openssl_verify ( $data, $signature, $pkcs12 );
		if ($ok == 1) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * 验证返回的签名是否正确
	 *
	 * @param string $data        	
	 * @param string $signature
	 *        	签名内容
	 * @return boolean
	 */
	function verifyRespondSign($data, $signature) {
		$filePath = 'allinpay-pds.pem';
		if (! file_exists ( $filePath )) {
			return false;
		}
		
		$fp = fopen ( $filePath, "r" );
		$cert = fread ( $fp, 8192 );
		fclose ( $fp );
		$pubkeyid = openssl_get_publickey ( $cert );
		
		if (! is_resource ( $pubkeyid )) {
			return false;
		}
		
		$ok = openssl_verify ( $data, $signature, $pubkeyid );
		if ($ok == 1) {
			openssl_free_key ( $pubkeyid );
			return true;
		}
		return false;
	}
	
	/**
	 * 除去数组中的空值和签名参数
	 *
	 * @param array $param        	
	 * @return array
	 */
	private static function paraFilter($param) {
		$para_filter = array ();
		foreach ( $param as $key => $val ) {
			if ($key == "sign" || $val == "" || $key == "client" || $key == "lm_debug")
				continue;
			else
				$para_filter [$key] = $param [$key];
		}
		return $para_filter;
	}
	
	/**
	 * 对数组排序
	 *
	 * @param array $param
	 *        	排序前的数组
	 * @return array 排序后的数组
	 */
	private static function argSort($param) {
		ksort ( $param ); // 函数对关联数组按照键名进行升序排序
		reset ( $param ); // 函数把数组的内部指针指向第一个元素，并返回这个元素的值。
		return $param;
	}
	
	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * 
	 * @param array $param
	 *        	需要拼接的数组
	 * @return string 拼接完成以后的字符串
	 */
	private static function parseUrlParamString($param) {
		$arg = "";
		foreach ( $param as $key => $val ) {
			$arg .= strtolower ( $key ) . "=" . $val . "&";
		}
		// 去掉最后一个&字符
		$arg = substr ( $arg, 0, count ( $arg ) - 2 );
		
		// 如果存在转义字符，那么去掉转义
		if (get_magic_quotes_gpc ()) {
			$arg = stripslashes ( $arg );
		}
		return $arg;
	}
}

$openssl = new openSSL();
$sign_str = $openssl->sign($array); //传参为约定好的数组 返回加密sign