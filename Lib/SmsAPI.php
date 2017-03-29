<?php
namespace Lib;

class SmsAPI extends Base {

    public function sendMessage($mobile, $message){
        $api_url = 'http://ums.zj165.com:8888/sms/Api/Send.do';
        // 参数数组
        $data = array(
            'SpCode'=>'236897',
            'LoginName'=>'zj_jlkg',
            'Password'=>'cma2016',
            'MessageContent'=>\mb_convert_encoding('您本次操作的验证码为'.$message.'，5分钟内输入有效', 'gbk', 'utf-8'),
            'UserNumber'=>$mobile,
            'SerialNumber'=>time(),
            'ScheduleTime'=>date("YmdHis")
            );

        $ch = curl_init ();
        // print_r($ch);
        curl_setopt ( $ch, CURLOPT_URL, $api_url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query($data) );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        $return = \mb_convert_encoding($return, 'utf-8', 'gbk');
        \parse_str($return, $new);
        return $new;
    }

}
