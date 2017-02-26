<?php
class wxtoken {
  private $userid;

  public function __construct($userid=0) {
	$this->userid = $userid;
  }

  public function getSignPackage() {
	if($this->userid==0) {
		$row=mysql_fetch_array(mysql_query("select * from ".DBQIAN."_sys_config order by id desc limit 0,1"));
	} else {
		$row=mysql_fetch_array(mysql_query("select * from ".DBQIAN."_sys_config where uid=".$this->userid));
	} 
	  
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $row['cappid'],
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
	if($this->userid==0) {
		$row=mysql_fetch_array(mysql_query("select * from ".DBQIAN."_sys_config order by id desc limit 0,1"));
	} else {
		$row=mysql_fetch_array(mysql_query("select * from ".DBQIAN."_sys_config where uid=".$this->userid));
	} 
    if ($row['ctickettime'] < time()) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $ctickettime = time() + 7000;
        $cticket = $ticket;
        mysql_query("update ".DBQIAN."_sys_config set cticket='".$cticket."',ctickettime=".$ctickettime." where id=".$row['id']);
      }
    } else {
      $ticket = $row['cticket'];
    }

    return $ticket;
  }

  private function getAccessToken() {
	if($this->userid==0) {
		$row=mysql_fetch_array(mysql_query("select * from ".DBQIAN."_sys_config order by id desc limit 0,1"));
	} else {
		$row=mysql_fetch_array(mysql_query("select * from ".DBQIAN."_sys_config where uid=".$this->userid));
	} 
    if ($row['ctokentime'] < time()) {
      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$row['cappid']."&secret=".$row['cappsecret'];
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $ctokentime = time() + 7000;
        $ctoken = $access_token;
        mysql_query("update ".DBQIAN."_sys_config set ctoken='".$ctoken."',ctokentime=".$ctokentime." where id=".$row['id']);
      }
    } else {
      $access_token = $row['ctoken'];
    }
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }
}

