<?php 

include "/alidata/www/BebeServ/plugin/simple_html_dom.php";
include "/etc/database/setting.inc";

set_time_limit(0);
define("TTL",345600);
date_default_timezone_set("Asia/Harbin");
$dsettings = new DefaultSettingMakeSureNoSameName();
$private_key = '4886c6_SmartWeatherAPI_34986e0';
$appid='17f49878c1a33fa4';
$appid_six=substr($appid,0,6);
$type='forecast_v';

$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user, $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
// CONFIG SET requirepass
$redis->auth($dsettings->redis_pass);

$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

$city_sql = "select * from city_new;";
if ($result = $mysqli->query($city_sql)) {
	while($obj = $result->fetch_object()){
		$areaid = $obj->uuid;
		$name =  $obj->name;
		$date=date("YmdHi");
		$public_key="http://open.weather.com.cn/data/?areaid=".$areaid."&type=".$type."&date=".$date."&appid=".$appid;
		$key = base64_encode(hash_hmac('sha1',$public_key,$private_key,TRUE));
		$URL="http://open.weather.com.cn/data/?areaid=".$areaid."&type=".$type."&date=".$date."&appid=".$appid_six."&key=".urlencode($key);

		$string=@file_get_contents($URL);

		$arr = json_decode($string);
		$iter = 0;
		while(!is_object($arr)){
			$string=@file_get_contents($URL);
			$arr = json_decode($string);
			$iter ++ ;
			if ($iter > 3) {
				break;
			}
		}
		foreach ($arr->f->f1 as $idx => $aday) {
			$timing = explode('|', $aday->fi);
			$sunrise = $timing[0];
			$sunset = $timing[1];
			
			if ($idx == 0) {
				$timestamp = date('Ymd', strtotime(' +0 day'));
				$redis->setex("city_" . $name ."_". $timestamp . "_sunrise",TTL,$sunrise);	
				$redis->setex("city_" . $name ."_". $timestamp . "_sunset",TTL,$sunset);
			}else if ($idx == 1) {
				$timestamp = date('Ymd', strtotime(' +1 day'));
				$redis->setex("city_" . $name ."_". $timestamp . "_sunrise",TTL,$sunrise);	
				$redis->setex("city_" . $name ."_". $timestamp . "_sunset",TTL,$sunset);
			}else if ($idx == 2) {
				$timestamp = date('Ymd', strtotime(' +2 day'));
				$redis->setex("city_" . $name ."_". $timestamp . "_sunrise",TTL,$sunrise);	
				$redis->setex("city_" . $name ."_". $timestamp . "_sunset",TTL,$sunset);
			}
			
			echo "$timestamp $name sunrise at $sunrise,sunset at $sunset\n";
		}
	}
}else{
	print_r($mysqli->error);
	exit(1);
}
$result->close();
free_mysqli();



$mysqli->close();
$redis->close();

function free_mysqli()
{
  global $mysqli;
  while($mysqli->more_results())
  {
      $mysqli->next_result();
      if($res = $mysqli->store_result()) // added closing bracket
      {
          $res->free();
      }
  }
}

?>
