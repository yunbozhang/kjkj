<?
class pubaction extends mysql
{
    //����Ҫ���ʹ���Ҳ���ĵ�ַ����Ϊ����
	const LOGINURL = 'login.php';
	//�û���½���
    function admin_logincheck()
    {
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] == '')
        {
            header("Location:".self::LOGINURL);
        }
    }
    //�û��˳���½
    function user_logout()
    {
        session_destroy();
        header("Location:".self::LOGINURL);//self ָ���౾��
    }
    //����ʱ����,����$logintime T���û����������ε�½
    function user_online($logintime = 8200)
    {
        $now = mktime();
        if (!$this->checkbasename() && ($now - $_SESSION['ontime'] > $logintime))
        {
            session_destroy();
            $this->showmsg("��½�ѳ�ʱ�������µ�¼", self::LOGINURL);
        } else {
            $_SESSION['ontime'] = mktime();
        }
    }
	//ȡ�õ�ǰҳ�������
	function geturl()
    {
        return $_SERVER['REQUEST_URI'];
    }
	//��Ϣ��ʾ1
	function showalert($msg = '�༭�ɹ���',$tourl='',$type=1)
	{
	  echo "<meta http-equiv='Content-Type' content='text/html;charset=gb2312' />";
	  if($type == 1)
         echo "<script language=javascript>alert('$msg');location='$tourl';</script>";
      else
	     echo "<script language=javascript>location='$tourl';</script>";
	}
}
?>