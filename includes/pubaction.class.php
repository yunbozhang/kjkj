<?
class pubaction extends mysql
{
    //将需要多次使用且不变的地址定义为常量
	const LOGINURL = 'login.php';
	//用户登陆检测
    function admin_logincheck()
    {
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] == '')
        {
            header("Location:".self::LOGINURL);
        }
    }
    //用户退出登陆
    function user_logout()
    {
        session_destroy();
        header("Location:".self::LOGINURL);//self 指向本类本身
    }
    //在线时间检测,大于$logintime T掉用户，结束本次登陆
    function user_online($logintime = 8200)
    {
        $now = mktime();
        if (!$this->checkbasename() && ($now - $_SESSION['ontime'] > $logintime))
        {
            session_destroy();
            $this->showmsg("登陆已超时，请重新登录", self::LOGINURL);
        } else {
            $_SESSION['ontime'] = mktime();
        }
    }
	//取得当前页面的名字
	function geturl()
    {
        return $_SERVER['REQUEST_URI'];
    }
	//信息提示1
	function showalert($msg = '编辑成功！',$tourl='',$type=1)
	{
	  echo "<meta http-equiv='Content-Type' content='text/html;charset=gb2312' />";
	  if($type == 1)
         echo "<script language=javascript>alert('$msg');location='$tourl';</script>";
      else
	     echo "<script language=javascript>location='$tourl';</script>";
	}
}
?>