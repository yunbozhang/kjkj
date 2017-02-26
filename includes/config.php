<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
date_default_timezone_set("Etc/GMT-8");
date_default_timezone_set("asia/shanghai");
define('ROOTPATH', dirname(dirname(__FILE__)));
define("DBHOST","localhost");                     //服务器主机名
define("DBUSER","root");                          //mysql数据库用户名
define("DBPASS","");                       //mysql数据库登陆密码
define("DBDATA","kanjia");                      //数据库名字
define("DBQIAN","wxkanjia");                       //数据库表前缀
define("WXTOKEN", "haokuaiwang");                 //微信token
define("WEBNAME","http://test.gope.cn/");  //网站域名,以'/'结尾
?>