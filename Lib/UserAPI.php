<?php
namespace Lib;

use Core\Response;

class UserAPI extends Base {

  private $_db;

  public function __construct() {
    $this->_db = new DatabaseAPI();
  }

  public function userLoad($openid = 0){
    if($openid) {
      if($user = $this->_db->findUserByOpenid($openid)) {
        return $user;
      } else {
        return FALSE;
      }
    } else {
      if($_user = $this->isUserLogin()){
        $data = new \stdClass();
        $data->uid = $_user->uid;
        $data->openid = $_user->openid;
        $data->nickname = $_user->nickname;
        $data->headimgurl = $_user->headimgurl;
        // if($re = $this->_db->findInfoByUid($_user->uid)) {
        //   $data->info = $re;
        // }
        return $data;
      } else {
        return (object) array('uid' => '0', 'openid' => '0');
      }
    }
  }

  public function userLogin($openid){
    $user = $this->_db->findUserByOpenid($openid);
    if($user) {
      return $this->userLoginFinalize($user);
    }
    return FALSE;
  }

  public function isUserLogin() {
    // $r = new \stdClass();
    // $r->uid = '1';
    // $r->openid = 'asf';
    // return $r;
    if(USER_STORAGE == 'COOKIE') {
      if(isset($_COOKIE['_user0206'])) {
        return json_decode($_COOKIE['_user0206']);
      }
    } else {
      if(isset($_SESSION['_user0206'])) {
        return json_decode($_SESSION['_user0206']);
      }
    }
    return FALSE;
  }

  public function userLoginFinalize($user) {
    if(USER_STORAGE == 'COOKIE') {
      setcookie('_user0206', json_encode($user), time() + 3600 * 24 * 100, '/');
    } else {
      $_SESSION['_user0206'] = json_encode($user);
    }
    return $user;
  }

  public function userRegister($openid){
    $userinfo = new \stdClass();
    $userinfo->openid = $openid;
    $user = $this->_db->insertUser($userinfo);
    return $this->userLoginFinalize($user);
  }

  public function userRegisterOauth($info){
    $user = $this->_db->insertUser($info);
    return $this->userLoginFinalize($user);
  }

  public function oauthAction($scope, $redirect_uri) {
    $wechatUserAPI = new \Lib\WechatAPI();
    $param['redirect_uri'] = $redirect_uri;
    $callback = BASE_URL . CALLBACK . '?' . http_build_query($param);
    $url = $wechatUserAPI->getAuthorizeUrl(APPID, $callback, $scope);
    $response = new Response();
    $response->redirect($url);  
  }

}