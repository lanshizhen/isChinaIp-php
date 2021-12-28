#!/bin/bash
#更新国内ip地址列表
#首先生成china_ip.txt
#然后根据上面的文件生成first_china_ip.txt,和china_range_ip.txt
#first_china_ip.txt  ip第一段的列表
#china_range_ip.txt  ip范围列表

#文件目录/可换成路径的字符串
path=`pwd`
ip_txt_path=${path}'/china_ip.txt';
china_range_ip_path=${path}'/china_range_ip.txt';
first_china_ip_path=${path}'/first_china_ip.txt';
ip_url='http://ftp.apnic.net/apnic/stats/apnic/delegated-apnic-latest';

#php的运行文件
php_path=/Applications/MAMP/bin/php/php5.6.40/bin/php
#本地
script_path=${path}/putip.php

# cur_time=$(date +"%Y%m%d%H%M%S");
if [ -e ${ip_txt_path} ];then
    rm ${ip_txt_path}
    #    mv ${ip_txt_path} ${ip_txt_path}_${cur_time};
fi
# if [ -f ${china_range_ip_path} ];then
#     rm ${china_range_ip_path}
#     #    mv ${china_range_ip_path} ${china_range_ip_path}_${cur_time};
# fi
# if [ -f ${first_china_ip_path} ];then
#     rm ${first_china_ip_path}
#     #    mv ${first_china_ip_path} ${first_china_ip_path}_${cur_time};
# fi

#download 用curl下载，保存到我们所定义的文本文件中
/usr/bin/curl ${ip_url} | grep ipv4 | grep CN | awk -F\| '{ printf("%s/%d\n", $4, 32-log($5)/log(2)) }' >${ip_txt_path}

# echo "begin parse ip\n";
${php_path} ${script_path}