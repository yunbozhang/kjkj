<?
class mysql
{
    private $host, $uname, $upass, $db, $scode;
    function __construct($host = 'localhost', $uname = 'root', $upass = '', $db = '',$scode = 'GBK')//���캯�� ���ڱ�ʵ����ʱ�ͻ�ִ��
    {
        $this->host = $host;
        $this->uname = $uname;
        $this->upass = $upass;
        $this->db = $db;
        $this->scode = $scode;
        $this->connect($this->host, $this->uname, $this->upass, $this->db, $this->scode);
    }
    //�������ݿ�����
    function connect($host, $uname, $upass, $db, $scode)
    {
        $conn = mysql_connect($host, $uname, $upass) or die($this->error());
        mysql_select_db($db, $conn) or die($this->error());
        mysql_query('set names ' . $scode);
    }
    //��ȡ��¼��
    function countn($tbname, $req = '')
    {
        if ($req == '')
        {
            $sql = "select count(*) as nums from " . $tbname;
        } else
        {
            $sql = "select count(*) as nums from " . $tbname . " where " . $req;
        }
        $re = $this->query($sql);
        $row = $this->fetch($re);
        return $row["nums"];
    }
    function countfet($sql)
    {
        $re = $this->query($sql);
        $row = mysql_num_rows($re);
        return $row;
    }
	function countjoint($req)
	{
	    $sql="select count(*) as nums from " . $req;
        $re = $this->query($sql);
        $row = $this->fetch($re);
        return $row["nums"];
	}
	//��Ϣ�б�
	function news_list($sql,$page = 0, $pagesize = 0) //page=0 �����÷�ҳ
	{
	   if($page == 0)
	   {
	      $sql = $sql;
	   }else  {
	      $sql =$sql ." limit ".($page - 1) * $pagesize. "," . $pagesize;
	   }
       $re = $this->query($sql);
	   return $re;
	}
    //��ͨ��ѯ����
    function query($sql)
    {
        $query = mysql_query($sql) or die("ϵͳ������ʾ:" . $this->error());
        return $query;
    }
	function noretquery($sql)
	{
	    mysql_query($sql) or die("ϵͳ������ʾ:" . $this->error());
	}
    //��ȡ�ֶ�����
    function fetch($re)
    {
        $row = mysql_fetch_array($re);
        return $row;
    }
	//��ʾmysql����
    function error()
    {
        return mysql_error();
    }
    //�ر����ݿ�
	function close()
    {
        return mysql_close();
    }
    function __destruct()
    {
        $this->close();
    }
}
?>