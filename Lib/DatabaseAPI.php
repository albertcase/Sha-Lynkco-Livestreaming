<?php
namespace Lib;
/**
 * DatabaseAPI class
 */
class DatabaseAPI {

	private $db;

	private function connect() {
		$connect = new \mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		$this->db = $connect;
		$this->db->query('SET NAMES UTF8');
		return $this->db;
	}
	/**
	 * Create user in database
	 */
	// public function insertUser($userinfo){
	// 	$nowtime = NOWTIME;
	// 	$sql = "INSERT INTO `user` SET `openid` = ?, `created` = ?, `updated` = ?"; 
	// 	$res = $this->connect()->prepare($sql); 
	// 	$res->bind_param("sss", $userinfo->openid, $nowtime, $nowtime);
	// 	if($res->execute()) 
	// 		return $this->findUserByOpenid($userinfo->openid);
	// 	else 
	// 		return FALSE;
	// }

	public function insertUser($userinfo){
		$nowtime = NOWTIME;
		$sql = "INSERT INTO `user` SET `openid` = ?, `nickname` = ?, `headimgurl` = ?, `created` = ?, `updated` = ?"; 
		$res = $this->connect()->prepare($sql); 
		$res->bind_param("sssss", $userinfo->openid, $userinfo->nickname, $userinfo->headimgurl, $nowtime, $nowtime);
		if($res->execute()) 
			return $this->findUserByOpenid($userinfo->openid);
		else 
			return FALSE;
	}

	public function updateUser($data) {
		if ($this->findUserByOauth($data->openid)) {
			return TRUE;
		}
		$sql = "INSERT INTO `oauth` SET `openid` = ?, nickname = ?, headimgurl = ?";
		$res = $this->db->prepare($sql); 
		$res->bind_param("sss", $data->openid, $data->nickname, $data->headimgurl);
		if ($res->execute()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function findUserByOauth($openid) {
		$sql = "SELECT id  FROM `oauth` WHERE `openid` = ?"; 
		$res = $this->db->prepare($sql);
		$res->bind_param("s", $openid);
		$res->execute();
		$res->bind_result($uid);
		if($res->fetch()) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Create user in database  
	 */
	// public function findUserByOpenid($openid){
	// 	$sql = "SELECT `uid`, `openid` FROM `user` WHERE `openid` = ?"; 
	// 	$res = $this->connect()->prepare($sql);
	// 	$res->bind_param("s", $openid);
	// 	$res->execute();
	// 	$res->bind_result($uid, $openid);
	// 	if($res->fetch()) {
	// 		$user = new \stdClass();
	// 		$user->uid = $uid;
	// 		$user->openid = $openid;
	// 		return $user;
	// 	}
	// 	return NULL;
	// }

	/**
	 * Create user in database
	 */
	public function findUserByOpenid($openid){
		$sql = "SELECT `uid`, `openid`, `nickname`, `headimgurl` FROM `user` WHERE `openid` = ?"; 
		$res = $this->connect()->prepare($sql);
		$res->bind_param("s", $openid);
		$res->execute();
		$res->bind_result($uid, $openid, $nickname, $headimgurl);
		if($res->fetch()) {
			$user = new \stdClass();
			$user->uid = $uid;
			$user->openid = $openid;
			$user->nickname = $nickname;
			$user->headimgurl = $headimgurl;
			return $user;
		}
		return NULL;
	}

	/**
	 * 
	 */
	public function saveInfo($data){
		if($this->findInfoByUid($data->uid)) {
			$this->updateInfo($data);
		} else {
			$this->insertInfo($data);
		}
	} 

	/**
	 * 
	 */
	public function insertInfo($data){
		$nowtime = NOWTIME;
		$sql = "INSERT INTO `info` SET `uid` = ?, `name` = ?, `cellphone` = ?, `address` = ?, `created` = ?, `updated` = ?"; 
		$res = $this->connect()->prepare($sql); 
		$res->bind_param("ssssss", $data->uid, $data->name, $data->cellphone, $data->address, $nowtime, $nowtime);
		if($res->execute()) 
			return $res->insert_id;
		else 
			return FALSE;
	}

	/**
	 * 
	 */
	public function updateInfo($data){
		$nowtime = NOWTIME;
		$sql = "UPDATE `info` SET `name` = ?, `cellphone` = ?, `address` = ?, `updated` = ? WHERE `uid` = ?"; 
		$res = $this->connect()->prepare($sql); 
		$res->bind_param("sssss", $data->name, $data->cellphone, $data->address, $nowtime, $data->uid);
		if($res->execute()) 
			return $this->findInfoByUid($data->uid);
		else 
			return FALSE;
	}

	/**
	 * Create user in database
	 */
	public function findInfoByUid($uid){
		$sql = "SELECT `id`, `name`, `cellphone`, `address` FROM `info` WHERE `uid` = ?"; 
		$res = $this->connect()->prepare($sql);
		$res->bind_param("s", $uid);
		$res->execute();
		$res->bind_result($id, $name, $cellphone, $address);
		if($res->fetch()) {
			$info = new \stdClass();
			$info->id = $id;
			$info->name = $name;
			$info->cellphone = $cellphone;
			$info->$address = $address;
			return $info;
		}
		return NULL;
	}

	/**
	 * 
	 */
	public function insertMake($data){
		if($rs = $this->loadMakeByUid($data->uid)) {
			$this->updateAnswer($data);
			return $rs->id;
		} else {
			return $this->insertAnswer($data);
		}
	}

	public function insertAnswer($data){
		$sql = "INSERT INTO `answer` SET `uid` = ?, `nickname` = ?, `answer1` = ?, `answer2` = ?, `answer3` = ?, `answer4` = ?, `answer5` = ?, `total` = ?, `image` = ?"; 
		$res = $this->connect()->prepare($sql); 
		$res->bind_param("sssssssss", $data->uid, $data->nickname, $data->answer1, $data->answer2, $data->answer3, $data->answer4, $data->answer5, $data->total, $data->image);
		if($res->execute()) 
			return $res->insert_id;
		else 
			return FALSE;
	}

	public function updateAnswer($data){
		$sql = "UPDATE `answer` SET `nickname` = ?, `answer1` = ?, `answer2` = ?, `answer3` = ?, `answer4` = ?, `answer5` = ?, `total` = ?, `image` = ? WHERE `uid` = ?"; 
		$res = $this->connect()->prepare($sql); 
		$res->bind_param("sssssssss", $data->nickname, $data->answer1, $data->answer2, $data->answer3, $data->answer4, $data->answer5, $data->total, $data->image, $data->uid);
		if($res->execute()) 
			return TRUE;
		else 
			return FALSE;
	}

	public function loadMakeById($id){
		$sql = "SELECT `id`, `uid`, `nickname`, `total`, `image` FROM `answer` WHERE `id` = ?"; 
		$res = $this->connect()->prepare($sql);
		$res->bind_param("s", $id);
		$res->execute();
		$res->bind_result($id, $uid, $nickname, $total, $image);
		if($res->fetch()) {
			$info = new \stdClass();
			$info->id = $id;
			$info->uid = $uid;
			$info->nickname = $nickname;
			$info->total = $total;
			$info->image = $image;
			return $info;
		}
		return NULL;
	}

	public function loadMakeByUid($uid){
		$sql = "SELECT `id`, `uid`, `nickname`, `total`, `image` FROM `answer` WHERE `uid` = ?"; 
		$res = $this->connect()->prepare($sql);
		$res->bind_param("s", $uid);
		$res->execute();
		$res->bind_result($id, $uid, $nickname, $total, $image);
		if($res->fetch()) {
			$info = new \stdClass();
			$info->id = $id;
			$info->uid = $uid;
			$info->nickname = $nickname;
			$info->total = $total;
			$info->image = $image;
			return $info;
		}
		return NULL;
	}

	public function loadListByUid($uid) {
		$sql = "SELECT * FROM `answer` WHERE uid in (select fuid from band where uid = '".intval($uid)."') or uid = '".$uid."' order by total desc"; 
		$res = $this->db->query($sql);
		$data = array();
		while($rows = $res->fetch_array(MYSQLI_ASSOC))
		{
			$data[] = $rows;
		}	
		return $data;
	}

	/**
	 * 
	 */
	public function bandShare($uid, $fuid){
		$sql = "INSERT INTO `band` SET `uid` = ?, `fuid` = ?"; 
		$res = $this->connect()->prepare($sql); 
		$res->bind_param("ss", $uid, $fuid);
		if($res->execute()) 
			return $res->insert_id;
		else 
			return FALSE;
	}

	public function insertSubmit($data){
		$sql = "INSERT INTO `submit` SET `uid` = ?, `name` = ?, `info` = ?"; 
		$res = $this->connect()->prepare($sql); 
		$res->bind_param("sss", $data->uid, $data->name, $data->info);
		if($res->execute()) 
			return $res->insert_id;
		else 
			return FALSE;
	}

	public function loadSubmit($uid){
		$sql = "SELECT `name`, `info` FROM `submit` WHERE `uid` = ?"; 
		$res = $this->connect()->prepare($sql);
		$res->bind_param("s", $uid);
		$res->execute();
		$res->bind_result($name, $info);
		if($res->fetch()) {
			$data = new \stdClass();
			$data->name = $name;
			$data->info = $info;
			return $data;
		}
		return NULL;
	}

	public function clearMake(){
		$sql = "TRUNCATE table answer"; 
		$res = $this->connect()->prepare($sql); 
		if($res->execute()) {
			$sql2 = "TRUNCATE table band"; 
			$res2 = $this->connect()->prepare($sql2); 
			$res2->execute();
			return TRUE;
		}
			
		else 
			return FALSE;
	}

}
