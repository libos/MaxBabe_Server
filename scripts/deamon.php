<?php 

$s = curl_init("http://apibabe.maxtain.com/real_time.php"); 

curl_setopt($s,CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1');
curl_setopt($s,CURLOPT_TIMEOUT,30); 
curl_setopt($s,CURLOPT_RETURNTRANSFER,true); 
curl_setopt($s,CURLOPT_FOLLOWLOCATION,1); 

$_webpage = curl_exec($s);
$_status = curl_getinfo($s,CURLINFO_HTTP_CODE); 
curl_close($s);

if ($_status == 200) {
 	echo "ok";
}else{
    date_default_timezone_set('Asia/Shanghai');
    $date = date('Y.d.m h:i:s');
    $log = "{$date} ==> Server Status is {$_status}\n";
    error_log($log, 3, "/var/log/server_status.log"); 
    error_log("{$date} ==> Server Status is {$_status}\n", 1,
               "libo@maxtain.com","Subject:  Server {$_status} Error\nFrom: info@maxtain.com\n");
}

if ($_status == 502 || $_status == 0) {
	exec("service php5-fpm restart");
}

?>
