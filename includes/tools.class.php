<?php
class tools {
	//���Ƴ����ı��е����
   function txt_substr($str,$start,$len){
      $strlen=$start+$len;
      for($i=0;$i<$strlen;$i++){
         if(ord(substr($str,$i,1))>0xa0){
	        $tmpstr.=substr($str,$i,2);
		    $i++;
	      } else {
	        $tmpstr.=substr($str,$i,1);
	      }
      }
    return $tmpstr;
   }
   //��ȡ�ַ���,string:��Ҫ��ȡ���ַ�����length��Ĭ�Ͻ�ȡ���ȣ�etc����ȡ��Ĭ�Ϻ󲹵��ַ���
   function txt_substr_length($string, $length = 80, $etc = '...')
   {
       if ($length == 0)
          return '';
       if (strlen($string) > $length) {
          $length -= min($length, strlen($etc));
          for($i = 0; $i < $length ; $i++) {
             $strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
	      }
		  return $strcut.$etc;
		 } else {
           return $string;
         }
     }
   //���ı��е������ַ������滻
   function html_replace($content){
      $content=htmlspecialchars($content);//ת���ı��е������ַ�
      $content=str_replace(chr(13),"<br>",$content);
      $content=str_replace(chr(32),"&nbsp;",$content);
      $content=str_replace("[_[","<",$content);
      $content=str_replace(")_)",">",$content);
      $content=str_replace("|_|","",$content);
      return trim($content);
   }
   //����ַ������Ƿ�������ַ�������������ַ��������� (')˫���� (")��б�� (\) NULL����ָ����Ԥ�����ַ�ǰ��ӷ�б��
   function sql_mag_gpc($str)
   {
      if(get_magic_quotes_gpc()==1)
         return $str;
      else
         return addslashes($str);
    }
   //ȡ��lengthλ��str�ڵ������
   function get_random ($length)
   { 
	   $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
       $result = '';
       $l = strlen($str);
       for($i = 0;$i < $length;$i++)
       {
           $num = rand(0, $l-1);
           $result .= $str[$num];
       }
       return $result;
   }
	//�����ļ���
    function create_dirs($dir){
       return is_dir($dir) or ($this->create_dirs(dirname($dir)) and mkdir($dir, 0777));
    }
   //�����ļ�$fileUrl Դ�ļ� $aimUrlĿ���ַ
   function copy_file($fileUrl,$aimUrl) {
       if (!file_exists($fileUrl)) {
          return false;
       }
       if(file_exists($aimUrl)) {
          @unlink($aimUrl);
       }
       copy($fileUrl, $aimUrl);
       return true;
   }
   //��ȡ�ļ���׺�� 
   function get_files_endname($file_name)
   {
      $extend =explode("." , $file_name);
      $va=count($extend)-1;
      return $extend[$va];
   }
   //��ȡ�ļ�ǰ׺��
   function get_file_starname($file_name)
  {
      $extend =explode("." , $file_name);
      return $extend[0];
   }
   //�ϴ�ͼƬ$img:�ϴ��ļ���$_FILES���飬imgname��ͼƬ�����֣�filepath:�ϴ�·��
   function upload_img($img,$imgname,$filepath,$maxfilesize=2){
	   $fileType=array('jpg','gif','png','JPG','GIF','PNG');//�����ϴ����ļ�����
	   if(!in_array(substr($img['name'],-3,3),$fileType))
		   die("<script>alert('�������ϴ������͵��ļ���');history.back();</script>");
	   if(strpos($img['type'],'image')===false)
		   die("<script>alert('�������ϴ������͵��ļ���');history.back();</script>");
	   if($img['size']> $maxfilesize*1024000)
		   die( "<script>alert('�ļ�����');history.back();</script>");
	   if($img['error'] !=0)
		   die("<script>alert('δ֪�����ļ��ϴ�ʧ�ܣ�');history.back();</script>");
	   if(@move_uploaded_file($img['tmp_name'], $filepath.$imgname)){
		   $string='ͼƬ�ϴ��ɹ���';
	   }  else {
		   $string= 'ͼƬ�ϴ�ʧ��';
	   }
    }
   //ȡ�õ�ǰ����ҳ��IP��ַ
   function get_ip(){
       if($_SERVER['HTTP_CLIENT_IP']){
          $onlineip=$_SERVER['HTTP_CLIENT_IP'];//HTTP_CLIENT_IP �ͻ��ˣ�����������ڵĵ��ԣ���ip��ַ
       }elseif($_SERVER['HTTP_X_FORWARDED_FOR']){
          $onlineip=$_SERVER['HTTP_X_FORWARDED_FOR'];
       }else{
          $onlineip=$_SERVER['REMOTE_ADDR'];
       }
	  if($onlineip=='::1'){
	     $onlineip='127.0.0.1';
	  }
	   return $onlineip;
   }
  //ȡ�õ�ǰҳ��ĺ�׺����
   function get_url_query(){
	  $urls = parse_url($_SERVER['REQUEST_URI']);
	  $url = $urls['path'];
	  $urlquery = $urls['query'];
	  return $urlquery;
   }
   //ȡ�õ�ǰҳ������� 
  function get_url_self(){
    $php_self=substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1);
    return $php_self;
   }
  //�ж��Ƿ���΢�������
  function check_is_weixin(){ 
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
		return true;
	}	
	return false;
  }
  //curl get��ȡ
   function http_curl_get($url) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_TIMEOUT, 5000);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	  curl_setopt ($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $res = curl_exec($curl);
      curl_close($curl);
      return $res;
   }
   //curl post�ύ
   function http_curl_post($url, $data = null)
   {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
      if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      }
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $output = curl_exec($curl);
      curl_close($curl);
      return $output;
   }
   //�����ļ��������ͼƬ
   function get_dirs_img($path){
      if(!is_dir($path)) return;
      $handle  = opendir($path);
      $files = array();
      while(false !== ($file = readdir($handle))){         
         if($file != '.' && $file!='..'){
            $path2= $path.'/'.$file;
            if(is_dir($path2)){
               $this->get_dirs_img($path2);         
            }else{
               if(preg_match("/\.(gif|jpeg|jpg|png|bmp)$/i", $file)){
                  $files[] = $file;
               }
            }         
         }
      }
      return $files;
   }
   //�����ļ�
   function create_file($name,$path,$content)
  {
      $toppath=$path.$name;
      $Ts=fopen($toppath,"a+");
      fputs($Ts,$content."\r\n");
      fclose($Ts);
   }
   //�����ʼ�,
   function send_mail($femail,$fpass,$fsmtp,$to, $subject = 'No subject', $body) {
       $loc_host = "test";            //���ż��������������
       $smtp_acc = $femail; //Smtp��֤���û���������fuweng@im286.com������fuweng
       $smtp_pass=$fpass;          //Smtp��֤�����룬һ���ͬpop3����
       $smtp_host=$fsmtp;    //SMTP��������ַ������ smtp.tom.com
       $from=$femail;       //������Email��ַ����ķ��������ַ
       $headers = "Content-Type: text/plain; charset=\"gb2312\"\r\nContent-Transfer-Encoding: base64";
       $lb="\r\n";                    //linebreak
       $hdr = explode($lb,$headers);     //�������hdr
       if($body) {$bdy = preg_replace("/^\./","..",explode($lb,$body));}//�������Body
           $smtp = array(
                //1��EHLO���ڴ�����220����250
                array("EHLO ".$loc_host.$lb,"220,250","HELO error: "),
                //2������Auth Login���ڴ�����334
                array("AUTH LOGIN".$lb,"334","AUTH error:"),
                //3�����;���Base64������û������ڴ�����334
                array(base64_encode($smtp_acc).$lb,"334","AUTHENTIFICATION error : "),
                //4�����;���Base64��������룬�ڴ�����235
                array(base64_encode($smtp_pass).$lb,"235","AUTHENTIFICATION error : "));
        //5������Mail From���ڴ�����250
        $smtp[] = array("MAIL FROM: <".$from.">".$lb,"250","MAIL FROM error: ");
        //6������Rcpt To���ڴ�����250
        $smtp[] = array("RCPT TO: <".$to.">".$lb,"250","RCPT TO error: ");
        //7������DATA���ڴ�����354
        $smtp[] = array("DATA".$lb,"354","DATA error: ");
        //8.0������From
        $smtp[] = array("From: ".$from.$lb,"","");
        //8.2������To
        $smtp[] = array("To: ".$to.$lb,"","");
        //8.1�����ͱ���
        $smtp[] = array("Subject: ".$subject.$lb,"","");
        //8.3����������Header����
        foreach($hdr as $h) {$smtp[] = array($h.$lb,"","");}
        //8.4������һ�����У�����Header����
        $smtp[] = array($lb,"","");
        //8.5�������ż�����
        if($bdy) {foreach($bdy as $b) {$smtp[] = array(base64_encode($b.$lb).$lb,"","");}}
        //9������"."��ʾ�ż��������ڴ�����250
        $smtp[] = array(".".$lb,"250","DATA(end)error: ");
        //10������Quit���˳����ڴ�����221
        $smtp[] = array("QUIT".$lb,"221","QUIT error: ");
        //��smtp�������˿�
        $fp = @fsockopen($smtp_host, 25);
        if (!$fp) echo "Error: Cannot conect to ".$smtp_host."";
        while($result = @fgets($fp, 1024)){if(substr($result,3,1) == " ") { break; }}
        $result_str="";
        //����smtp�����е�����/����
        foreach($smtp as $req){
                //������Ϣ
                @fputs($fp, $req[0]);
                //�����Ҫ���շ�����������Ϣ����
                if($req[1]){
                        //������Ϣ
                        while($result = @fgets($fp, 1024)){
                                if(substr($result,3,1) == " ") { break; }
                        };
                        if (!strstr($req[1],substr($result,0,3))){
                                $result_str.=$req[2].$result."";
                        }
                }
        }
        //�ر�����
        @fclose($fp);
        return $result_str;
   }
}
?>