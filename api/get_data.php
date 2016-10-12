<?php

$err = array("state"=>"err");

if(!isset($_GET['id']) || !isset($_GET['type']) || !isset($_GET['auth']) || !isset($_GET['user']) )
{
	echo json_encode($err);
	return;
}
$id = urldecode($_GET['id']);
$type = $_GET['type'];
$auth = $_GET['auth'];

$user = $_GET['user'];
$hour = $_GET['hour'];
$month = $_GET['month'];
$date = $_GET['day'];
if (!($id)) {
	echo json_encode($err);
	return;
}
if (!in_array($type, array("all","real_time","alarm"))) {
	echo json_encode($err);
	return;
}
//echo md5($id . ". maxtain ." . $type . ". mybabe ");
if ($auth != md5($id . ". maxtain ." . $type . ". mybabe ")) {
//	echo json_encode($err);
//	return;
}
//echo "_{$month}_{$date}_{$hour}_{$id}_{$type}"; 

include "/etc/database/setting.inc";
date_default_timezone_set("Asia/Harbin");
$dsettings = new DefaultSettingMakeSureNoSameName();

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth($dsettings->redis_pass);
$redis->incr("user_" . $user);

$timestamp = date("Ymd");
$weather = array();
$primary_data = array();
if ($type == "all" || $type == "real_time" ) {
	$key_temp =  "babe_realtime_". $id . "_{$month}_{$date}_{$hour}_temp";
	$key_weather =  "babe_realtime_". $id . "_{$month}_{$date}_{$hour}_weather";
	$primarylist  = array(	$key_temp,$key_weather,
							"mybabe_id" . $id ."_". $timestamp . "_real_time_update_time",
							"mybabe_id" . $id ."_". $timestamp . "_real_time_temp",
							"mybabe_id" . $id ."_". $timestamp . "_real_time_fengxiang",
							"mybabe_id" . $id ."_". $timestamp . "_real_time_fengxiang_level",
							"mybabe_id" . $id ."_". $timestamp . "_real_time_humidity",
							"mybabe_id" . $id ."_". $timestamp . "_real_time_weather",
							"mybabe_id" . $id ."_". $timestamp . "_aqi_level",
							"mybabe_id" . $id ."_". $timestamp . "_today_weather",
							"mybabe_id" . $id ."_". $timestamp . "_today_temp",
							"mybabe_id" . $id ."_". $timestamp . "_today_day_weather",
							"mybabe_id" . $id ."_". $timestamp . "_today_night_weather",
							"mybabe_id" . $id ."_". $timestamp . "_today_day_temp",
							"mybabe_id" . $id ."_". $timestamp . "_today_night_temp",
							"mybabe_id" . $id ."_". $timestamp . "_tomo_weather",
							"mybabe_id" . $id ."_". $timestamp . "_tomo_temp",
							"mybabe_id" . $id ."_". $timestamp . "_tomorrow_day_weather",
							"mybabe_id" . $id ."_". $timestamp . "_tomorrow_night_weather",
							"mybabe_id" . $id ."_". $timestamp . "_tomorrow_day_temp",
							"mybabe_id" . $id ."_". $timestamp . "_tomorrow_night_temp",
							"mybabe_id" . $id ."_". $timestamp . "_today_weather_detail",
							"mybabe_id" . $id ."_". $timestamp . "_is_there_alarm"
	);
	
	$keys = array('temp','weather','updatetime','rtemp','fengxiang','fenglevel','humidity','rweather',
				  'aqi',"today_weather","today_temp",'day_weather','night_weather','day_temp','night_temp',
				  'tomo_weather','tomo_temp','next_day_weather','next_night_weather','next_day_temp',
				  'next_night_temp','weather_detail','has_alarm');

	$primaryvals = $redis->mGet($primarylist);
//print_r($primaryvals);
	$primary_data = array_combine($keys, $primaryvals);
	if ($type == "real_time") {
		$weather = $primary_data;
	}
}
//print_r($primary_data);
if ($type == "all" || $type == "alarm") {
	$alarm_data = array();
	if ($primary_data['has_alarm']) {
		$alarmlist = array(	"mybabe_id" . $id ."_". $timestamp . "_alarm_type",
							"mybabe_id" . $id ."_". $timestamp . "_alarm_level",
							"mybabe_id" . $id ."_". $timestamp . "_alarm_issuetime",
							"mybabe_id" . $id ."_". $timestamp . "_alarm_content"
							// "mybabe_id" . $id ."_". $timestamp . "_alarm_type_en",
							// "mybabe_id" . $id ."_". $timestamp . "_alarm_level_en"
		);
		$alarmvals = $redis->mGet($alarmlist);
		$alarmkeys = array('alarm_type','alarm_level','alarm_issuetime','alram_content');
		$alarm_data = array_combine($alarmkeys, $alarmvals);
	}

	if (!empty($alarm_data)) {
		if ($type == "all") {
			$weather = array_merge($primary_data,$alarm_data);
		}else if ($type == "alarm") {
			$weather = $alarm;
		}
	}else{
	     if ($type == "all") {
		$weather = $primary_data;
	     }
		//we have return is there alarm boolean value
	}
}

$redis->close();

// $other = array('background'=>'sunny.png','figure'=>'default.png','oneword'=>"今天天气不错哦！");

// $assemble = array_merge($weather,$other);
echo json_encode($weather);

return;


