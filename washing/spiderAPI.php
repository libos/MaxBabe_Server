<?php 

set_time_limit(0);
date_default_timezone_set("Asia/Harbin");
$private_key = '4886c6_SmartWeatherAPI_34986e0';
$appid='17f49878c1a33fa4';
$appid_six=substr($appid,0,6);
$type='forecast_v';
// fa 白天天气现象编号 01
// fb 晚上天气现象编号 01
// fc 白天天气温度(摄氏度) 11
// fd 晚上天气温度(摄氏度) 0
// fe 白天风向编号 4
// ff 晚上风向编号 4
// fg 白天风力编号 1
// fh 晚上风力编号 0
// fi 日出日落时间(中间用|分割) 06:44|18:21

$fab = array('00' => '晴','01' => '多云','02' => '阴','03' => '阵雨','04' => '雷阵雨','05' => '雷阵雨伴有冰雹','06' => '雨夹雪','07' => '小雨','08' => '中雨','09' => '大雨','10' => '暴雨','11' => '大暴雨','12' => '特大暴雨','13' => '阵雪','14' => '小雪','15' => '中雪','16' => '大雪','17' => '暴雪','18' => '雾','19' => '冻雨','20' => '沙尘暴','21' => '小到中雨','22' => '中到大雨','23' => '大到暴雨','24' => '暴雨到大暴雨','25' => '大暴雨到特大暴雨','26' => '小到中雪','27' => '中到大雪','28' => '大到暴雪','29' => '浮尘','30' => '扬沙','31' => '强沙尘暴','53' => '霾','99' => '无');
$fef = array('0' => '无持续风向','1' => '东北风','2' => '东风','3' => '东南风','4' => '南风','5' => '西南风','6' => '西风','7' => '西北风','8' => '北风','9' => '旋转风');
$fgh = array('0' => '1级','1' => '3级','2' => '4级','3' => '5级','4' => '6级','5' => '7级','6' => '8级','7' => '9级','8' => '10级','9' => '11级');
// get city list

$areaid = '101340203';
$date=date("YmdHi");
$public_key="http://open.weather.com.cn/data/?areaid=".$areaid."&type=".$type."&date=".$date."&appid=".$appid;
$key = base64_encode(hash_hmac('sha1',$public_key,$private_key,TRUE));
$URL="http://open.weather.com.cn/data/?areaid=".$areaid."&type=".$type."&date=".$date."&appid=".$appid_six."&key=".urlencode($key);
// echo $URL."\n";

$string=file_get_contents($URL);

$arr = json_decode($string);
$iter = 0;
while(!is_object($arr->f->f1)){
	$string=file_get_contents($URL);
	$arr = json_decode($string);
	$iter ++ ;
	if ($iter > 3) {
		break;
	}
}
foreach ($arr->f->f1 as $idx => $aday) {
	$daytime_weather = $fab[$aday->fa];
	$nighttime_weather = $fab[$aday->fa];
	$daytime_temp = $aday->fc;
	$nighttime_temp = $aday->fd;
	$daytime_fengxiang = $fef[$aday->fe];
	$nighttime_fengxiang = $fef[$aday->ff];
	$daytime_level = $fgh[$aday->fg];
	$nighttime_level = $fgh[$aday->fh];
	

	echo $daytime_weather . "\n";
	echo $nighttime_weather . "\n";
	echo $daytime_temp . "\n";
	echo $nighttime_temp . "\n";
	echo $daytime_fengxiang . "\n";
	echo $nighttime_fengxiang . "\n";
	echo $daytime_level . "\n";
	echo $nighttime_level . "\n";
	echo "\n";
}




// print_r(json_decode($string));



?>