<?php
namespace Lib;

class JSSDKAPI extends Base {

  public function getTicket($type = 'jsapi_ticket') {
    $types = array('jsapi_ticket' => 'jsapi', 'api_ticket' => 'wx_card');
    $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$this->_access_token}&type={$types[$type]}";
    $ticket = file_get_contents($url);
    return json_decode($ticket);
  }

  public function getJSSDKConfig($appid, $jsApiTicket, $url, $noncestr = 'KiehlsDFsf') {
    $nowtime = time();
    $ticketstr = "jsapi_ticket={$jsApiTicket}&noncestr={$noncestr}&timestamp={$nowtime}&url={$url}";
    $signature = sha1($ticketstr);
    return array(
      'appId' => $appid,
      'timestamp' => $nowtime,
      'nonceStr' => $noncestr,
      'signature' => $signature,
      );
  }

}