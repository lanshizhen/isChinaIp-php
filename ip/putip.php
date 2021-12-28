<?php
/*

解析国内ip地址列表，以ip地址的第一段为索引，
保存到redis中的一个hash中

*/
//------------------------------------------------settings
ini_set("display_errors","On");
error_reporting(E_ALL);

define("IP_FILE", "./china_ip.txt");
define("FIRST_IP_FILE", "./first_china_ip.txt");
define("RANGE_IP_FILE", "./china_range_ip.txt");

//------------------------------------------------main
set_ip_list(IP_FILE);
// get_china_ip_list(FIRST_IP_FILE);

//------------------------------------------------function
function set_ip_list($ip_file) {

    //从文件中得到所有的国内ip
    $arr_all = file($ip_file);
    //遍历，得到所有的第一段
    $arr_first = array();
    foreach ($arr_all as $k => $rangeone) {
        $rangeone = trim($rangeone);
        if ($rangeone == "") {
            continue;
        }
        $first = explode(".", $rangeone);
        if (isset($first[0]) && $first[0]!='') {
              $arr_first[] = $first[0];
        }
    }

    //对所有的第一段去除重复
    $arr_first = array_unique($arr_first);
    if (count($arr_first) == 0) {
        return FALSE;
    }
    $first_array = array();
    $str = '';
    foreach ($arr_first as $k => $val) {
        $str .= $val."\r\n";
        $first_array[] = $val;
    }
    
    $first_result = file_put_contents(FIRST_IP_FILE, $str);
    //得到每个第一段下面对应的所有ip地址段,保存到redis
    $range_ip_list = array();
    foreach ($arr_first as $k => $first) {
        
        $range_ip_list['first'.$first] = add_a_list_by_first($first, $arr_all);
    }

    $result = file_put_contents(RANGE_IP_FILE, serialize($range_ip_list));
    
    return TRUE;
}

//把所有的第一段为指定数字的ip,添加到redis
function add_a_list_by_first($first, $arr) {

    $arr_line = array();
    $str = '';
    foreach ($arr as $k => $rangeone) {
            $rangeone = trim($rangeone);
        $first_a = explode(".", $rangeone);
        if (!isset($first_a[0]) || $first_a[0] == "") {
                continue;
        }
        $cur_first = $first_a[0];
        if ($cur_first == $first) {
            $line = get_line_by_rangeone($rangeone);
            $arr_line[] = $line;
            $str .= $line."\r\n";
        } else {
                continue;
        }
    }
    if (sizeof($arr_line) >0) {
        return $arr_line;
    }else{
        return FALSE;
    }
    
}

//得到一个ip地址段的起始范围
function get_line_by_rangeone($networkRange) {
    $s = explode('/', $networkRange);
    $network_start = (double) (sprintf("%u", ip2long($s[0])));
    $network_len = pow(2, 32 - $s[1]);
    $network_end = $network_start + $network_len - 1;
    $line = $network_start."--".$network_end;
    return $line;
}

function get_china_ip_list($china_ip_file) {
    //从文件中得到所有的国内ip
    $allip = array();
    $arr_all = file($china_ip_file);
    foreach ($arr_all as $k => $rangeone) {
        $rangeone = trim($rangeone);
        if ($rangeone == "") {
            continue;
        }
        $allip[] = $rangeone;
    }
    var_export($allip);exit;
}
