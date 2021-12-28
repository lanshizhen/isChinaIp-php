<?php
include_once "./is_china_ip.php";
/*

解析国内ip地址列表，以ip地址的第一段为索引，

*/
echo getcwd();
exit;
$ip = $argv[0];
set_ip_list($ip);
function set_ip_list($ip) {
    $ipclass = new IpClass();
    if ($ipclass->is_ip_in_china($ip)){
        echo '是';
    }else{
        echo '否';
    }
}