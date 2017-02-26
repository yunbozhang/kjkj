<?
class mysql
{
    private $host, $uname, $upass, $db, $scode;
    function __construct($host = 'localhost', $uname = 'root', $upass = '', $db = '',$scode = 'GBK')//构造函数 类在被实例化时就会执行
    {
        $this->host = $host;
        $this->uname = $uname;
        $this->upass = $upass;
        $this->db = $db;
        $this->scode = $scode;
        $this->connect($this->host, $this->uname, $this->upass, $this->db, $this->scode);
    }
    //建立数据库连接
    function connect($host, $uname, $upass, $db, $scode)
    {
        $conn = mysql_connect($host, $uname, $upass) or die($this->error());
        mysql_select_db($db, $conn) or die($this->error());
        mysql_query('set names ' . $scode);
    }
    //获取记录数
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
	//信息列表
	function news_list($sql,$page = 0, $pagesize = 0) //page=0 不启用分页
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
    //普通查询方法
    function query($sql)
    {
        $query = mysql_query($sql) or die("系统错误提示:" . $this->error());
        return $query;
    }
	function noretquery($sql)
	{
	    mysql_query($sql) or die("系统错误提示:" . $this->error());
	}
    //获取字段内容
    function fetch($re)
    {
        $row = mysql_fetch_array($re);
        return $row;
    }
	//显示mysql错误
    function error()
    {
        return mysql_error();
    }
    //关闭数据库
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