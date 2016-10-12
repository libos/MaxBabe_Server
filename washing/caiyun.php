<?php 

include "/alidata/www/BebeServ/plugin/simple_html_dom.php";
include "/etc/database/setting.inc";

set_time_limit(0);
define("TTL",345600);
date_default_timezone_set("Asia/Harbin");
$dsettings = new DefaultSettingMakeSureNoSameName();

$weather_name = array('CLEAR_DAY' => '晴', 'CLEAR_NIGHT'=> '晴',
					  'PARTLY_CLOUDY_DAY'=> '多云','PARTLY_CLOUDY_NIGHT'=>'多云',
					  'CLOUDY'=>'阴','RAIN'=>'雨','SLEET'=>'雨夹雪',
					  'SNOW'=>'大雪','WIND'=>'晴','FOG'=>'雾');

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

$city_sql = "select * from city_new;";
if ($result = $mysqli->query($city_sql)) {
	while($obj = $result->fetch_object()){
		$areaid = $obj->uuid;
		$name =  $obj->name;
		$lon = $obj->lon;
		$lat = $obj->lat;

		$URL="https://api.caiyunapp.com/v2/XARLqtNQfZQ5BfBP/{$lon},{$lat}/forecast";

		$string=file_get_contents($URL);

		$caiyun = json_decode($string);
		$iter = 0;
		while(!is_object($caiyun)){
			$string=file_get_contents($URL);
			$caiyun = json_decode($string);
			$iter ++ ;
			if ($iter > 3) {
				break;
			}
		}
		$cyresult = $caiyun->result;
		if ($cyresult->status != 'ok') {
			print_r("Error");
		}

		$hourly = $cyresult->hourly;
		$daily = $cyresult->daily;
		
		// hourly data
		$pm25 = $hourly->pm25;
		$skycon = $hourly->skycon;
		$cloudrate = $hourly->cloudrate;
		$aqi = $hourly->aqi;
		$humidity = $hourly->humidity;
		$precipitation = $hourly->precipitation;
		$wind = $hourly->wind;
		$temperature = $hourly->temperature;
		// daily data
		$sunrise_set = $daily->astro;
		$temp_max_min = $daily->temperature;
		$weather = $daily->skycon;
		$cloudrate_max_min = $daily->cloudrate;
		$precipitation_max_min = $daily->precipitation;
		$wind_max_min = $daily->wind;
		$humidity_max_min = $daily->humidity;

		for ($iter=0; $iter < 48; $iter++) { 
			$timex = $wind[$iter]->datetime;
			$timestamp = date('YmdH',strtotime($timex));
			if (!empty($pm25) && isset($pm25[$iter])) {
				$pm25_value = $pm25[$iter]->value;
			}
			if (!empty($aqi) && isset($aqi[$iter])) {
				$aqi_value = $aqi[$iter]->value;
			}
			$skycon_value = $weather_name[$skycon[$iter]->value];
			$humidity_value = floatval($humidity[$iter]->value)*100;
			$wind_speed = floatval($wind[$iter]->speed);
			$wind_level = get_wind_level($wind_speed);
			$wind_fengxiang = $wind[$iter]->direction;
			$temperature_value = round(floatval($temperature[$iter]->value));
			
			// echo "caiyun_hour_" . $name ."_". $timestamp . "_pm25" . $pm25_value . "\n";
			// echo "caiyun_hour_" . $name ."_". $timestamp . "_aqi" . $aqi_value . "\n";
			// echo "caiyun_hour_" . $name ."_". $timestamp . "_weather_name" . $skycon_value . "\n";
			// echo "caiyun_hour_" . $name ."_". $timestamp . "_humidity" . $humidity_value . "\n";
			// echo "caiyun_hour_" . $name ."_". $timestamp . "_wind_speed" . $wind_speed . "\n";
			// echo "caiyun_hour_" . $name ."_". $timestamp . "_wind_level" . $wind_level . "\n";
			// echo "caiyun_hour_" . $name ."_". $timestamp . "_wind_direction" . $wind_fengxiang . "\n";
			// echo "caiyun_hour_" . $name ."_". $timestamp . "_temperature" . $temperature_value . "\n";

			$redis->setex("caiyun_hour_" . $name ."_". $timestamp . "_pm25",TTL,$pm25_value);
			$redis->setex("caiyun_hour_" . $name ."_". $timestamp . "_aqi",TTL,$aqi_value);
			$redis->setex("caiyun_hour_" . $name ."_". $timestamp . "_weather_name",TTL,$skycon_value);
			$redis->setex("caiyun_hour_" . $name ."_". $timestamp . "_humidity",TTL,$humidity_value);
			$redis->setex("caiyun_hour_" . $name ."_". $timestamp . "_wind_speed",TTL,$wind_speed);
			$redis->setex("caiyun_hour_" . $name ."_". $timestamp . "_wind_level",TTL,$wind_level);
			$redis->setex("caiyun_hour_" . $name ."_". $timestamp . "_wind_direction",TTL,$wind_fengxiang);
			$redis->setex("caiyun_hour_" . $name ."_". $timestamp . "_temperature",TTL,$temperature_value);
		}

		for ($iter=0; $iter < 5; $iter++) { 
			$timestamp = date('Ymd',strtotime($sunrise_set[$iter]->date));
			$sunrise = $sunrise_set[$iter]->sunrise->time;
			$sunset = $sunrise_set[$iter]->sunset->time;
			$temperature_max = round(floatval($temp_max_min[$iter]->max));
			$temperature_avg = round(floatval($temp_max_min[$iter]->avg));
			$temperature_min = round(floatval($temp_max_min[$iter]->min));
			$weather_theday = $weather_name[$weather[$iter]->value];
			$wind_speed_max = floatval($wind_max_min[$iter]->max->speed);
			$wind_speed_avg = floatval($wind_max_min[$iter]->avg->speed);
			$wind_speed_min = floatval($wind_max_min[$iter]->min->speed);
			$wind_direction_max = $wind_max_min[$iter]->max->direction;
			$wind_direction_avg = $wind_max_min[$iter]->avg->direction;
			$wind_direction_min = $wind_max_min[$iter]->min->direction;
			$wind_level_max = get_wind_level($wind_speed_max);
			$wind_level_avg = get_wind_level($wind_speed_avg);
			$wind_level_min = get_wind_level($wind_speed_min);
			$humidity_max = floatval($humidity_max_min[$iter]->max)*100;
			$humidity_avg = floatval($humidity_max_min[$iter]->avg)*100;
			$humidity_min = floatval($humidity_max_min[$iter]->min)*100;

			// echo "caiyun_daily_" . $name ."_". $timestamp . "_sunrise" . $sunrise . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_sunset" . $sunset . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_temperature_min" . $temperature_max . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_temperature_avg" . $temperature_avg . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_temperature_min" . $temperature_min . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_weather_theday" . $weather_theday . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_wind_speed_max" . $wind_speed_max . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_wind_speed_avg" . $wind_speed_avg . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_wind_speed_min" . $wind_speed_min . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_wind_direction_max" . $wind_direction_max . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_wind_direction_avg" . $wind_direction_avg . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_wind_direction_min" . $wind_direction_min . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_wind_level_max" . $wind_level_max . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_wind_level_avg" . $wind_level_avg . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_wind_level_min" . $wind_level_min . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_humidity_max" . $humidity_max . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_humidity_avg" . $humidity_avg . "\n";
			// echo "caiyun_daily_" . $name ."_". $timestamp . "_humidity_min" . $humidity_min . "\n";
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_sunrise",TTL,$sunrise);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_sunset",TTL,$sunset);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_temperature_max",TTL,$temperature_max);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_temperature_avg",TTL,$temperature_avg);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_temperature_min",TTL,$temperature_min);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_weather_theday",TTL,$weather_theday);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_wind_speed_max",TTL,$wind_speed_max);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_wind_speed_avg",TTL,$wind_speed_avg);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_wind_speed_min",TTL,$wind_speed_min);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_wind_direction_max",TTL,$wind_direction_max);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_wind_direction_avg",TTL,$wind_direction_avg);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_wind_direction_min",TTL,$wind_direction_min);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_wind_level_max",TTL,$wind_level_max);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_wind_level_avg",TTL,$wind_level_avg);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_wind_level_min",TTL,$wind_level_min);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_humidity_max",TTL,$humidity_max);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_humidity_avg",TTL,$humidity_avg);
			$redis->setex("caiyun_daily_" . $name ."_". $timestamp . "_humidity_min",TTL,$humidity_min);
		}
		echo "set $name done\n";
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


function get_wind_level($wind_speed)
{
	$wind_level = 0;
	if ($wind_speed <= 0.2) {
		$wind_level = 0;
	}else if ($wind_speed > 0.2 && $wind_speed <= 1.5) {
		$wind_level = 1;
	}else if ($wind_speed > 1.5 && $wind_speed <= 3.3) {
		$wind_level = 2;
	}else if ($wind_speed > 3.3 && $wind_speed <= 5.4) {
		$wind_level = 3;
	}else if ($wind_speed > 5.4 && $wind_speed <= 7.9) {
		$wind_level = 4;
	}else if ($wind_speed > 7.9 && $wind_speed <= 10.7) {
		$wind_level = 5;
	}else if ($wind_speed > 10.7 && $wind_speed <= 13.8) {
		$wind_level = 6;
	}else if ($wind_speed > 13.8 && $wind_speed <= 17.1) {
		$wind_level = 7;
	}else if ($wind_speed > 17.1 && $wind_speed <= 20.7) {
		$wind_level = 8;
	}else if ($wind_speed > 20.7 && $wind_speed <= 24.4) {
		$wind_level = 9;
	}else if ($wind_speed > 24.4 && $wind_speed <= 28.4) {
		$wind_level = 10;
	}else if ($wind_speed > 28.4 && $wind_speed <= 32.6) {
		$wind_level = 11;
	}else if ($wind_speed > 32.6 && $wind_speed <= 36.9) {
		$wind_level = 12;
	}else if ($wind_speed > 36.9 && $wind_speed <= 41.4) {
		$wind_level = 13;
	}else if ($wind_speed > 41.4 && $wind_speed <= 46.1) {
		$wind_level = 14;
	}else if ($wind_speed > 46.1 && $wind_speed <= 50.9) {
		$wind_level = 15;
	}else if ($wind_speed > 50.9 && $wind_speed <= 56.0) {
		$wind_level = 16;
	}else if ($wind_speed > 56.0 && $wind_speed <= 62.2) {
		$wind_level = 17;
	}else{
		$wind_level = 18;
	}
	return $wind_level;
}

?>
