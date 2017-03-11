<?php
require_once("../includes/conn.php");
$nid=intval($_GET['nid']);
$newsrow=$dbconn->fetch($dbconn->query("select * from ".DBQIAN."_kanjia_list where id=$nid"));
$sysconfig=$dbconn->fetch($dbconn->query("select * from ".DBQIAN."_sys_config where uid=$newsrow[uid]"));
$cwxzhanghao=$sysconfig['cwxzhanghao'];
$cwxnewsurl=$sysconfig['cwxnewsurl'];
header("location:".$cwxnewsurl);
?>