<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
date_default_timezone_set("Etc/GMT-8");
date_default_timezone_set("asia/shanghai");
define('ROOTPATH', dirname(dirname(__FILE__)));
define("DBHOST","localhost");                     //������������
define("DBUSER","root");                          //mysql���ݿ��û���
define("DBPASS","");                       //mysql���ݿ��½����
define("DBDATA","kanjia");                      //���ݿ�����
define("DBQIAN","wxkanjia");                       //���ݿ��ǰ׺
define("WXTOKEN", "haokuaiwang");                 //΢��token
define("WEBNAME","http://test.gope.cn/");  //��վ����,��'/'��β
?>