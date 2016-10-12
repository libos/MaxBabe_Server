<?php
include "/alidata/www/BebeServ/plugin/simple_html_dom.php";
include "/etc/database/setting.inc";

set_time_limit(0);
define("TTL",345600);	//4days
define("mTTL",172800);	//2days
date_default_timezone_set("Asia/Harbin");
$dsettings = new DefaultSettingMakeSureNoSameName();


$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
// CONFIG SET requirepass
$redis->auth($dsettings->redis_pass);


$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user, $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);



$city_sql = "select * from baidu_city_aqi;";
if ($result = $mysqli->query($city_sql)) {
	while($obj = $result->fetch_object()){
		$lastid = $obj->id;
		$name = $obj->city;
		get_weather($lastid,$name,$redis);
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

function get_weather($cityNumber,$cityName,$redis){
	$ch = curl_init();
	$url = "http://api.map.baidu.com/telematics/v3/weather?location={$cityName}&output=json&ak=u9v8T5A3xc1zTnOZOZ2lBMF5";
// 	vVrrq4WM35hdQ17yVDw7I0mB&mcode=A0:7F:1D:12:99:3F:33:01:48:8C:F3:E2:AF:57:69:40:98:F2:68:71;com.maxtain.bebe";
//	$url = "http://api.map.baidu.com/telematics/v3/weather?location={$cityName}&output=json&ak=RB5K1HQG8X0duK4ZyuVcsiW2";
/*	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, "http://api.map.baidu.com/");
   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HEADER, false);
*/
	$json_obj = file_get_htmlx($url);
	$iter = 3;
	while(!is_object($json_obj))
	{
		$json_obj = file_get_htmlx($url);
		$iter --;
	echo "retry $iter\n";
		if($iter <= 0) break;
	}
	if(is_object($json_obj))
	{
		$jsonData = $json_obj->plaintext;
		$jh = json_decode($jsonData,true);
		if ($jh && isset($jh["error"])) {
			$has_error = $jh["error"];
			if ($has_error == 0) {
				$results = $jh["results"];
				if(isset($results[0]["pm25"]) && !empty($results[0]["pm25"]))
				{
					
					$aqi_level = $results[0]["pm25"];
					$temperature = $results[0]["weather_data"][0]["temperature"];
					$weather = $results[0]["weather_data"][0]["weather"];
					$tomo_temp = $results[0]["weather_data"][1]["temperature"];
					$tomo_weather = $results[0]["weather_data"][1]["weather"];
					$timestamp = date('Ymd');
					if(!empty($aqi_level))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_aqi_level",TTL,$aqi_level);
					if(!empty($weather))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_today_weather",TTL,$weather);
					if(!empty($temperature))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_today_temp",TTL,$temperature);
					if(!empty($tomo_weather))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_tomo_weather",TTL,$tomo_weather);
					if(!empty($tomo_temp))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_tomo_temp",TTL,$tomo_temp);
					$timestamp = date('Ymd', strtotime(' +1 day'));
					if(!empty($aqi_level))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_aqi_level",mTTL,$aqi_level);
					if(!empty($weather))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_today_weather",mTTL,$weather);
					if(!empty($temperature))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_today_temp",mTTL,$temperature);
					if(!empty($tomo_weather))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_tomo_weather",mTTL,$tomo_weather);
					if(!empty($tomo_temp))
						$redis->setex("mybabe_id" . $cityName ."_". $timestamp . "_tomo_temp",mTTL,$tomo_temp);
	
			//		$redis->setex("babe_realtime_". $cityName . "_{$mon}_{$day}_{$hours}_temp",TTL,$temString);
			//		$redis->setex("babe_realtime_". $cityName . "_{$mon}_{$day}_{$hours}_weather",TTL,$weatherString);
					echo "$cityName aqi has been update\n";
				}
			}
		}
	}
}

?>
