<?
require_once("config.php");
$db=mysql_connect(DBHOST,DBUSER,DBPASS) or die("���ݿ����Ӵ����������Ա��ϵ");
mysql_query("SET NAMES 'GBK'");
mysql_select_db(DBDATA,$db);
 ?>
