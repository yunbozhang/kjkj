<?php
require_once("public.inc.php");
$wechatObj = new msCallbackapi();
if (isset($_GET['echostr'])) {
	$wechatObj->valid();
} else {
	define("MYUID",$_GET['uid']);
	$wechatObj->responseMsg();
}

class msCallbackapi 
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
	
    public function responseMsg()
    {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr)){
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;//（用户OpenID） 
                $toUsername = $postObj->ToUserName;//开发者微信号
				$MsgType=$postObj->MsgType;//事件类型
				$time = time();
				switch($MsgType){
					case "event":
					   //自定义菜单事件
                       if($postObj->Event == "CLICK"){
						   $this->checkUser($fromUsername);
						   $keyword = trim($postObj->EventKey);
						   $keyword=iconv("UTF-8","GBK",$keyword);
 					       $query= mysql_query("select * from ".DBQIAN."_news_key where sname = '$keyword' and stype=1 and uid=".MYUID." limit 0,1");
						   $num=mysql_num_rows($query);
						      if($num > 0){
						         $row=mysql_fetch_array($query);
						         if($row['kcode']==0){//文本
							         $this->textMsg($fromUsername,$toUsername,$row['id']);							   
						         } else if($row['kcode']==1){//图片
						             $this->imgMsg($fromUsername,$toUsername,$row['id']);
						          }
						      }
					   }
					   //第一次关注事件
					   if($postObj->Event == "subscribe"){
						   $this->checkUser($fromUsername);
						   $query=mysql_query("select * from ".DBQIAN."_news_key where stype=0 and uid=".MYUID." order by id desc limit 0,1");
						   $num=mysql_num_rows($query);
						   if($num!=0){
							   $row=mysql_fetch_array($query);
							   if($row['kcode']==0){//文本
							   	  $this->textMsg($fromUsername,$toUsername,$row['id']);
							   } else if($row['kcode']==1){//图片
							      $this->imgMsg($fromUsername,$toUsername,$row['id']);
							   }
						   }
					   }
					   //取消关注事件					   
					   if($postObj->Event == "unsubscribe"){
						   $this->delUser($fromUsername);
					   }					   
					   break;
					case "text"://文本消息
					   $this->checkUser($fromUsername);
					   $keyword = trim($postObj->Content);	
					   $keyword=iconv("UTF-8","GBK",$keyword);	
					   //$this->insertTxt($fromUsername,$keyword); 
					   $query= mysql_query("select * from ".DBQIAN."_news_key where sname = '$keyword' and stype=2 and uid=".MYUID." limit 0,1");
					   $num=mysql_num_rows($query);
					   if($num!=0){						   
						   $row=mysql_fetch_array($query); 
						   if($row['kcode']==0){//文本
							 $this->textMsg($fromUsername,$toUsername,$row['id']);							   
						   } else if($row['kcode']==1){//图片
							 $this->imgMsg($fromUsername,$toUsername,$row['id']);
						   }
					   }
					   break;
					default :
					   echo "";
					   break;
				}
        } else {
        	echo "";
        	exit;
        }
    }	
	//用户加入数据库
	private function checkUser($ucode=''){
		if($ucode!=""){
		   $num=mysql_num_rows(mysql_query("select * from ".DBQIAN."_user_list where ucode='$ucode' and uid=".MYUID));
		   if($num==0){
			   $atime=time();
			   mysql_query("insert into ".DBQIAN."_user_list(ucode,utime,uid)values('$ucode',$atime,".MYUID.")");
			}
		}
	}
	//插入用户发送的消息
	private function insertTxt($ucode='',$utxt=''){
		$atime=date("Y-m-d H:i:s");
		mysql_query("insert into weixin_user_txt(ucode,utxt,utime)values('$ucode','$utxt','$atime')");
	}
	//删除用户
	private function delUser($ucode=""){
		@mysql_query("delete from ".DBQIAN."_user_list where ucode='$ucode' and uid=".MYUID);
	}
	
	//推送图片
	private function imgMsg($fromUsername, $toUsername,$sendid){
		//消息类型
		$time = time();
		$newsContent="";
		$row=mysql_fetch_array(mysql_query("select count(*) as nums from ".DBQIAN."_news_send where kid=$sendid"));
		$imgnum=$row['nums'];
        $newsStart= "<xml>
					<ToUserName><![CDATA[$fromUsername]]></ToUserName>
					<FromUserName><![CDATA[$toUsername]]></FromUserName>
					<CreateTime>$time</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>$imgnum</ArticleCount>
					<Articles>
					";
		$newsEnd="  </Articles>
					</xml> 
					";
		$query=mysql_query("select * from ".DBQIAN."_news_send where kid=$sendid order by snum desc, id desc");
		while($crow=mysql_fetch_array($query)){
		   $msgtitle = iconv("GBK","UTF-8",$crow["sname"]);
		   $msgcontent = iconv("GBK","UTF-8",$crow["sdec"]) ;
		   $msgpicurl = WEBNAME."uploads/".substr($crow["stime"],0,4)."/".substr($crow["stime"],5,2)."/".$crow["spic"];
		   $urls = parse_url($crow['surl']);
		   $str=$urls['query'];
		   if($str!="")
		   {
				$str="&ucode=".$fromUsername;
		   } else {
				$str="?ucode=".$fromUsername;
		   }
		   $urls=$crow['surl'].$str;
		   $msgclickurl = $urls;
		   $newsContent=$newsContent . "
						   <item>
						   <Title><![CDATA[$msgtitle]]></Title> 
						   <Description><![CDATA[$msgcontent]]></Description>
						   <PicUrl><![CDATA[$msgpicurl]]></PicUrl>
						   <Url><![CDATA[$msgclickurl]]></Url>
						   </item>
						   ";
		}
		$resultStr=$newsStart.$newsContent.$newsEnd;
		echo $resultStr;
	}
	//推送文字
	private function textMsg($fromUsername,$toUsername,$sendid=0){
			//文字消息
			$time = time();
			$msgcontent="";
			$textTpl="
			<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
			</xml>
			";
			$row=mysql_fetch_array(mysql_query("select * from ".DBQIAN."_news_send where kid=$sendid"));
			if($row['surl']!=""){
			   $urls = parse_url($row['surl']);
			   $str=$urls['query'];
			   if($str!="")
			   {
				   $str="&ucode=".$fromUsername;
			   } else {
				   $str="?ucode=".$fromUsername;
			   }
			   $urls=$row['surl'].$str;
			   $msgcontent=$msgcontent."<a href=\"".$urls."\">".$row['sname']."</a>";
			} else {
				$msgcontent=$msgcontent.$row['sname'];
			}
			$msgcontent=$msgcontent.' '.$row['sdec'];
			$msgcontent=iconv("GBK","UTF-8",$msgcontent);
			$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time,$msgcontent);
			echo $resultStr;
	}
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = WXTOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}
?>