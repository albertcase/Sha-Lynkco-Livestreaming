<?php
namespace Lib;

use Core\Response;
/********************* *
* PHP HTTP SDK CLASS BEGIN
* 这里定义了要调用 CSB Broker 提供的 HTTP 服务的相关的方法 *
***********************/
class HttpCallerAPI {

	// 全局常量 标识是否打印调试信息 
	var $PRINT_DEBUG = false;

	// 打印调试信息的方法
	protected function myPrint($msg) {
		if ($this->PRINT_DEBUG) {
			echo $msg."<br>"; 
		}
	}

	/** 获取毫秒级时间戳 */
	protected function get_millistime() {
		$microtime = microtime();
		$comps = explode(' ', $microtime);
		return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
	}
	/** 生成签名的方法
		params 原始的输入参数数组 keyValuePairs api 调用服务 API 名
		version 服务版本
		ak accessKey
		sk secretKey
		返回 签名过的 URL 请求数组 
	*/
	protected function sign($params = array(), $api, $version, $ak, $sk){
		$headers = array();
		$headers['_api_name'] = $api; 
		$headers['_api_version'] = $version; 
		$headers['_api_access_key'] = $ak; 
		$headers['_api_timestamp'] = $this->get_millistime();
		$signParams = array();
		foreach ($params as $k => $v) { 
			$signParams[$k] = $v;
		}
		foreach ($headers as $k => $v) { 
			$signParams[$k] = $v;
			$params[$k] = $v;
		} 
		ksort($signParams);
		$signstr = '';
		foreach ($signParams as $k => $v) {
			if ($k == '_api_signature') 
				continue;
			if ($signstr != '') 
				$signstr = $signstr."&";
			$signstr = $signstr.$k.'='.$v; 
		}
		$this->myPrint('signstr='.http_build_query($signParams));
		$signature = base64_encode(hash_hmac('sha1', $signstr, $sk, true)); 
		$this->myPrint('signature='.$signature);
		$headers['_api_signature'] = $signature;
		// test
		return $headers;
		
		if ($this->PRINT_DEBUG) {
			echo "<br>headers:<br>";
			print_r($headers); 
		}
		//transfer header
		$theaders = array();
		foreach ($headers as $k => $v) {
			$theaders[] = $k.":".$v; 
		}
		return $theaders;
	}

	/** 使用 GET 方式调用 HTTP 服务的方法
		data 原始的输入参数数组 
		api 调用服务 API 名 
		version 服务版本
		ak accessKey 
		sk secretKey
		返回 从 HTTP 服务端返回的串 
	*/
	public function doGet($url, $data = array(), $api, $version, $ak, $sk){ 
		$headers = $this->sign($data,$api, $version, $ak, $sk);
		$ch = curl_init(); // 初始化一个 curl 资源类型变量
		/*设置访问的选项*/
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 启用时会将服务器返回 的 Location: 放在 header 中递归的返回给服务器,即允许跳转
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true ); // 将获得的数据返回而 不是直接在页面上输出
		//curl_setopt($ch,CURLOPT_PROTOCOLS,CURLPROTO_HTTP); //设置访问地址 用的协议类型为 HTTP
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); // 访问的超时时间限制为15s
		// curl_setopt($ch, CURLOPT_TIMEOUT, 15); //返回的超时设置
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在 启 用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查 SSL 加密 算法是否存在
		$url = $url.'?'.http_build_query($data);
		$this->myPrint("request url=".$url."<br>");
		curl_setopt($ch, CURLOPT_URL, $url); // 设置即将访问的 URL 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //put signature-related headers
		curl_setopt($ch, CURLOPT_HEADER, false); // 设置不显示头信息 
		$result = curl_exec($ch); // 执行本次访问,返回一个结果
		$this->myPrint("<br>returnStr=<xmp>".$result."</xmp><br>");
		curl_close($ch); // 关闭
		// ... // 针对结果的正确与否做一些操作
		return $result; 
	}

	/**
		* 供 doPostXXX 调用的内部方法,进行实际的 POST 调用,并返回调用结果 
	*/
	protected function postInner($ch, $url, $headers) {
		/*设置访问的选项*/
		curl_setopt($ch,CURLOPT_POST,true); //设置为POST传递形式
		$headers[] = 'Expect:';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 启用时会将服务器返回 的 Location: 放在 header 中递归的返回给服务器,即允许跳转
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true ); // 将获得的数据返回而 不是直接在页面上输出
		curl_setopt($ch,CURLOPT_PROTOCOLS,CURLPROTO_HTTP); //设置访问地址 用的协议类型为 HTTP
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); // 访问的超时时间限制为15s
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在 启 用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_USERAGENT, ''); // 将用户代理置空 
		curl_setopt($ch, CURLOPT_HEADER, false); // 设置不显示头信息 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); // 对认证证书来源的检查 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查 SSL 加密算法是否存在
		$this->myPrint("<br>request url=".$url); 
		curl_setopt($ch, CURLOPT_URL, $url); // 设置即将访问的 URL 
		$result = curl_exec($ch); // 执行本次访问,返回一个结果 
		$info = curl_getinfo($ch); // 获取本次访问资源的相关信息 
		//echo $info."<br>";
		$this->myPrint("<br>returnStr=<xmp>".$result."</xmp><br>"); 
		curl_close($ch); // 关闭
		// ... // 针对结果的正确与否做一些操作 
		return $result;
	}

	/** 使用 POST 方式调用 HTTP 服务的方法
		data 请求参数数组 (Key-Vale pairs) api 调用服务 API 名
		version 服务版本
		ak accessKey
		sk secretKey
		返回 从 Http 服务端返回的串
	*/
	public function doPost($url, $data, $api, $version, $ak, $sk){
		$ch = curl_init(); // 初始化一个 curl 资源类型变量
		$data['_api_timestamp'] = $this->get_millistime();
		$headers = $this->sign($data,$api, $version, $ak, $sk);
		$data['_api_signature'] = $headers['_api_signature'];
		$pd = http_build_query($data,"","&"); 
		curl_setopt($ch,CURLOPT_POSTFIELDS,$pd); //设置POST传递的数据
		return $this->postInner($ch, $url, $headers); 
	}

	/** 使用 POST 方式调用 HTTP 服务的方法
		data
		jsonStr
		api
		version 服务版本 ak accessKey sk secretKey
		请求参数
		请求的 JSON 格式串 (例如: "{"name" : "Banana"}")
		调用服务 API 名
		返回 从 HTTP 服务端返回的串
	*/
	public function doPostJsonString($url, $data, $jsonStr, $api, $version, $ak, $sk){
		$ch = curl_init(); // 初始化一个 curl 资源类型变量 
		$url = $url.'?'.http_build_query($data);
		$headers = $this->sign($data,$api, $version, $ak, $sk);
		$headers[] = 'Content-Type:application/json'; 
		//$headers['Content-Length'] = strlen($jsonStr); 
		curl_setopt($ch,CURLOPT_POSTFIELDS,$jsonStr); //设置POST传递的数据
		return $this->postInner($ch, $url, $headers); 
	}

	/** 使用 POST 方式调用 HTTP 服务的方法
		data
		byteArray
		api
		version 服务版本 ak accessKey sk secretKey
		原始的输入参数数组
		请求的 byte[] 这个 byte 数组可以是从一个文件中读取的内容
		调用服务 API 名
		返回 从 HTTP 服务端返回的串 
	*/
	public function doPostByteArray($url, $data, $byteArray, $api, $version, $ak, $sk){
		$ch = curl_init(); // 初始化一个 curl 资源类型变量
		$url = $url.'?'.http_build_query($data);
		$headers = $this->sign($data,$api, $version, $ak, $sk);
		$headers[] = 'Content-Type: application/octet-stream'; 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $byteArray); // 设置 POST 传递的数
		return $this->postInner($ch, $url, $headers); 
	}

	/** 一个帮助方法,读取文件内容并且转换为二进制数组 *
	*/
	public function readFileAsByteArray($filename) {
		$handle = fopen($filename, "rb"); 
		$fsize = filesize($filename); 
		$contents = fread($handle, $fsize); 
		fclose($handle);
		return $contents; 
	}
}
/*********************
*
* PHP HTTP SDK class END * ***********************/
?>
