<?php
/*
判断一个ip是否国内的ip
*/
//------------------------------------------------settings
// ini_set("display_errors", "On");
error_reporting(E_ALL);
define("FIRST_IP_FILE", "ip/first_china_ip.txt");
define("RANGE_IP_FILE", "ip/china_range_ip.txt");
class IpClass{
    
    //判断一个ip是否属于china
    public static function is_ip_in_china($ip) {
        $ip = trim($ip);
        $first_a = explode(".", $ip);
        if (!isset($first_a[0]) || $first_a[0] == "") {
            //ip有误，按国外算
            return false;
        }
        $first = $first_a[0];
        
        #判断ip第一段/可缓存起来
        $china_first_ip_list = self::get_file_transform_list(FIRST_IP_FILE);
        if (!$china_first_ip_list) {
            return false;
        }
        if (!in_array($first, $china_first_ip_list)) {
            return false;
        }
        #获取国内Ip地址范围/可缓存起来
        $all_range_ip_list = self::get_range_ip_list($first);
        if (!$all_range_ip_list) {
            return false;
        }
        $range_ip_list = unserialize($all_range_ip_list[0]);
        $china_range_ip_list = $range_ip_list['first'."$first"];
        
        if (!is_array($china_range_ip_list) || sizeof($china_range_ip_list) == 0) {
            return false;
        }
        if (self::is_ip_in_arr_range($ip, $china_range_ip_list) == true) {
            return true;
        } else {
            return false;
        }
    }

    //判断一个ip是否属于ip的range数组
    public static function is_ip_in_arr_range($ip, $china_range_ip_list) {

        $ip_long = (double) (sprintf("%u", ip2long($ip)));
        foreach ($china_range_ip_list as $k => $one) {
            $one = trim($one);
            $arr_one = explode("--", $one);
            if (!isset($arr_one[0]) || !isset($arr_one[1])) {
                continue;
            }
            $begin = $arr_one[0];
            $end = $arr_one[1];
            if ($ip_long >= $begin && $ip_long <= $end) {
                return true;
            }
        }
        return false;
    }

    //获取文件每行数据转成数组
    public static function get_file_transform_list($file_path) {

        $allip = array();
        $arr_all = file($file_path);
        foreach ($arr_all as $k => $rangeone) {
            $rangeone = trim($rangeone);
            if ($rangeone == "") {
                continue;
            }
            $allip[] = $rangeone;
        }
        return $allip;
    }

    //获取文件每行数据转成数组
    public static function get_range_ip_list() {

        $arr_all = file(RANGE_IP_FILE);
        return $arr_all;
    }
}

