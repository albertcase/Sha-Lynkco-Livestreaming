<?php
namespace CampaignBundle;

use Core\Controller;

class PageController extends Controller {

	public function indexAction() {	
		$this->render('index');
	}

	public function jssdkConfigJsAction() {
		ini_set("display_errors",1);
		$request = $this->Request();
		$fields = array(
		    'url' => array('notnull', '120'),
	    );
		$request->validation($fields);
		$url = urldecode($request->request->get('url'));
	  	$json = file_get_contents("http://lynkcoceo.samesamechina.com/jssdk?url=".$url);
	  	echo $json;
	  	exit;
	}

	public function testAction() {	
		$q1 = '后续关注';
		$q2 = '暂时没有';
		$q3 = '以后再说';
		$name = 'demon';
		$tel = '15121038676';
		$answer = array();
		$answer[] = array('question'=>'您是否愿意见证一个全新汽车品牌的诞生？', 'answer'=>$q1);
		$answer[] = array('question'=>'您是否计划购买一辆新车？', 'answer'=>$q2);
		$answer[] = array('question'=>'您是否愿意到LYNK & CO的活动现场先睹为快？', 'answer'=>$q3);
		//$extDescription = json_encode($answer);
		$extDescription = $answer;
		//$srcExtDesc = array("media"=>"自建媒体","behavior"=>"市场活动","terminal"=>"H5");
		$rs = $this->sendData($name, $tel, $extDescription);
		var_dump($rs);
		exit;
	}
	

	public function smsAction() {
		if (isset($_SESSION['check_timestamp']) &&time() <= ( $_SESSION['check_timestamp']+60)) {
			$data = array('status' => 0, 'msg' => '请勿重复调用');
			$this->dataPrint($data);
		}
		$request = $this->request;
		$fields = array(
			'mobile' => array('cellphone', '120'),
		);
		$request->validation($fields);
		$mobile = $request->request->get('mobile');
		$SmsAPI = new \Lib\SmsAPI();
		$code = mt_rand(100000, 999999);
		$_SESSION['check_code'] = $code;
		$_SESSION['check_timestamp'] = time();
		$rs = $SmsAPI->sendMessage($mobile, $code);
		if ($rs['result'] == 0) 
			$data = array('status' => 1, 'msg' => $rs['description']);
		else
			$data = array('status' => $rs['result'], 'msg' => $rs['description']);
		$this->dataPrint($data);

	}

	public function submitAction() {
		if (!isset($_SESSION['check_code'])) {
			$data = array('status' => 0, 'msg' => '请先请求验证码');
			$this->dataPrint($data);
		}
		$request = $this->request;
		$fields = array(
			'store' => array('notnull', '120'),
			'name' => array('notnull', '121'),
			'tel' => array('notnull', '122'),
			'code' => array('notnull', '123'),
			'weibo' => array('notnull', '124'),
		);
		
		$request->validation($fields);
		$store = $request->request->get('store');
		$name = $request->request->get('name');
		$tel = $request->request->get('tel');
		$code = $request->request->get('code');
		$weibo = $request->request->get('weibo');
		if ($code != $_SESSION['check_code']) {
			$data = array('status' => 2, 'msg' => '验证码不正确');
			$this->dataPrint($data);
		}
		
		unset($_SESSION['check_timestamp']);
		unset($_SESSION['check_code']);
		$data = array('status' => 1, 'msg' => '提交成功');
		$this->dataPrint($data);
		exit;
		$answer = array();
		$answer[] = array('question'=>'您是否愿意见证一个全新汽车品牌的诞生？', 'answer'=>$q1);
		$answer[] = array('question'=>'您是否计划购买一辆新车？', 'answer'=>$q2);
		$answer[] = array('question'=>'您是否愿意到LYNK & CO的活动现场先睹为快？', 'answer'=>$q3);
		//$extDescription = json_encode($answer);
		$extDescription = $answer;
		//$srcExtDesc = array("media"=>"自建媒体","behavior"=>"市场活动","terminal"=>"H5",'extId'=>'20170405133200');
		$rs = $this->sendData($name, $tel, $extDescription);
		$rs = json_decode($rs);
		if ($rs->code == 200) {
			$data = array('status' => 1, 'msg' => '提交成功');
			$this->dataPrint($data);
		} else {
			$data = array('status' => $rs->code, 'msg' => $rs->message);
			$this->dataPrint($data);
		}
		exit;

	}

	private function sendData($name, $tel, $extDescription) {
		$url = WS_URL;
		$lead = array('name'=>$name,'cellPhone1'=>$tel,'extDescription'=>$extDescription);
		$lead1 = json_encode($lead);
		$leadSource = array("media"=>"self_media","behavior"=>"market_activity","terminal"=>"h5",'extId'=>'20170405133200');
		$leadSource1 = json_encode($leadSource);
		$ak = CSB_AK;
		$sk = CSB_SK;
		$api = CSB_API;
		$api_name = CSB_API_NAME;
		$version = CSB_VERSION;
		$data = array('_api_name'=>$api_name, '_api_version' => $version,'_api_access_key' => $ak,'leadWithCarCodeDto'=>$lead1,'leadSource'=>$leadSource1,'leadType'=>'0');
		$phpCaller = new \Lib\HttpcallerAPI();

		$result = $phpCaller->doPost($url, $data, $api, $version, $ak, $sk); 
		return $result;
	}

	public function clearCookieAction() {
		setcookie('_user', json_encode($user), time(), '/');
		$this->statusPrint('success');
	}

}