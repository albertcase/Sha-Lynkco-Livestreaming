<?php
namespace Lib;

class WechatAPI {

	private $_token;
	private $_appid;
	private $_appsecret;

	public function __construct($token = '', $appid = '', $appsecret = '') {
		$this->_token = $token;
		$this->_appid = $appid;
		$this->_appsecret = $appsecret;
	}

	public function getAccessToken() {
		$applink = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
		$url = sprintf($applink, $this->_appid, $this->_appsecret);
		$data = file_get_contents($url);
		$data = json_decode($data);
		return $data;
	}

	public function getUserInfo($openid) {
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$this->_access_token}&openid={$openid}&lang=zh_CN";
		$return = file_get_contents($url);
		return json_decode($return);
	}

	public function isUserSubscribed($openid) {
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$this->_access_token}&openid={$openid}&lang=zh_CN";
		$return = file_get_contents($url);
		$rs = json_decode($return);
		if(isset($rs->subscribe) && $rs->subscribe == 1)
		  return TRUE;
		else
		  return FALSE;
	}

	public function getAuthorizeUrl($appid, $callback, $scope) {
		$redirect_uri = urlencode($callback);
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state=STATE#wechat_redirect";
		return $url;
	}

	public function getSnsAccessToken($code, $appid, $appsecret) {
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?code={$code}&grant_type=authorization_code&appid={$appid}&secret={$appsecret}";
		$return = file_get_contents($url);
		return json_decode($return);
	}

	public function getSnsUserInfo($openid, $sns_access_token) {
		  $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$sns_access_token}&openid={$openid}&lang=zh_CN";
		  $userinfo = file_get_contents($url);
		  $userinfo = json_decode($userinfo);
		  return $userinfo;
	}
	
}