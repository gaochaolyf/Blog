<?php
/**
 * des加密解密
 */
class DES {
	var $key;
	/**
	 * 约定的key
	 *
	 * @param string $key        	
	 */
	function __construct($key) {
		$this->key = $key;
	}
	
	/**
	 * 需要提供的加密字符串
	 *
	 * @param string $input        	
	 * @return string 生成的加密字符串
	 */
	function encrypt($input) {
		$size = mcrypt_get_block_size ( 'des', 'ecb' );
		$input = $this->pkcs5_pad ( $input, $size );
		$key = $this->key;
		$td = mcrypt_module_open ( 'des', '', 'ecb', '' );
		$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		@mcrypt_generic_init ( $td, $key, $iv );
		$data = mcrypt_generic ( $td, $input );
		mcrypt_generic_deinit ( $td );
		mcrypt_module_close ( $td );
		$data = $this->strToHex ( $data );
		return $data;
	}
	
	/**
	 * 需要解密的字符串
	 *
	 * @param string $encrypted        	
	 * @return string 解密后的结果
	 */
	function decrypt($encrypted) {
		$encrypted = $this->hexToStr ( $encrypted );
		$key = $this->key;
		$td = mcrypt_module_open ( 'des', '', 'ecb', '' );
		// 使用MCRYPT_DES算法,cbc模式
		$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		$ks = mcrypt_enc_get_key_size ( $td );
		@mcrypt_generic_init ( $td, $key, $iv );
		// 初始处理
		$decrypted = mdecrypt_generic ( $td, $encrypted );
		// 解密
		mcrypt_generic_deinit ( $td );
		// 结束
		mcrypt_module_close ( $td );
		$y = $this->pkcs5_unpad ( $decrypted );
		return $y;
	}
	
	/**
	 * 填充
	 *
	 * @param string $text        	
	 * @param int $blocksize        	
	 * @return string
	 */
	function pkcs5_pad($text, $blocksize) {
		$pad = $blocksize - (strlen ( $text ) % $blocksize);
		return $text . str_repeat ( chr ( $pad ), $pad );
	}
	
	/**
	 *
	 * @param string $text        	
	 * @return string
	 */
	function pkcs5_unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text ))
			return false;
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
			return false;
		return substr ( $text, 0, - 1 * $pad );
	}
	
	/**
	 *
	 * @param string $hexdata        	
	 * @return string
	 */
	protected function hex2bin($hexdata) {
		$bindata = '';
		
		for($i = 0; $i < strlen ( $hexdata ); $i += 2) {
			$bindata .= chr ( hexdec ( substr ( $hexdata, $i, 2 ) ) );
		}
		
		return $bindata;
	}
	
	/**
	 *
	 * @param string $string        	
	 * @return string
	 */
	private function strToHex($string) {
		$hex = '';
		for($i = 0; $i < strlen ( $string ); $i ++) {
			$ord = ord ( $string [$i] );
			$hexCode = dechex ( $ord );
			$hex .= substr ( '0' . $hexCode, - 2 );
		}
		return $hex;
	}
	
	/**
	 *
	 * @param string $hex        	
	 * @return string
	 */
	private function hexToStr($hex) {
		$string = '';
		for($i = 0; $i < strlen ( $hex ) - 1; $i += 2) {
			$string .= chr ( hexdec ( $hex [$i] . $hex [$i + 1] ) );
		}
		return $string;
	}
}

$des = new DES ( "CEF5E57376446C22" );
// 加密
$array_input = array ();
$array_input ['hphm'] = '浙A88888';
$array_input ['hpzl'] = '02';
$array_input ['fdjh'] = '888888';
$ret_code = $des->encrypt ( json_encode ( $array_input ) );

// 解密
$ret_decode = $des->decrypt ( $string_need_break );
$resultDate = urldecode ( $ret );
?> 