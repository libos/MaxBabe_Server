<?php 

$err = array("state"=>"err");

if(!isset($_GET['id']) || !isset($_GET['auth']))
{
	echo json_encode($err);
	return;
}
$id = urldecode($_GET['id']);
$auth = $_GET['auth'];

$hour = $_GET['hour'];
$month = $_GET['month'];
$date = $_GET['day'];

if (!($id)) {
	echo json_encode($err);
	return;
}
//echo md5($id . ". maxtain ." . $type . ". mybabe ");
if ($auth != md5($id . ". maxtain .widget. mybabe ")) {
	echo json_encode($err);
	return;
}

include "/etc/database/setting.inc";
date_default_timezone_set("Asia/Harbin");
$dsettings = new DefaultSettingMakeSureNoSameName();

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth($dsettings->redis_pass);

$caiyun_timestamp = date("Y") . "{$month}{$date}";
// $caiyun_timestamp_tomorrow = date('Ymd',strtotime(' +1 day'));

$timestamp = date("Ymd");

$weather = array();
$primary_data = array();

$primarylist  = array(	"babe_realtime_". $id . "_{$month}_{$date}_{$hour}_temp",
						"babe_realtime_". $id . "_{$month}_{$date}_{$hour}_weather",
						"mybabe_id" . $id ."_". $timestamp . "_real_time_temp",
						"mybabe_id" . $id ."_". $timestamp . "_real_time_weather",
						"mybabe_id" . $id ."_". $timestamp . "_today_weather",
						"mybabe_id" . $id ."_". $timestamp . "_today_temp",
						"mybabe_id" . $id ."_". $timestamp . "_today_day_weather",
						"mybabe_id" . $id ."_". $timestamp . "_today_night_weather",
						"mybabe_id" . $id ."_". $timestamp . "_today_day_temp",
						"mybabe_id" . $id ."_". $timestamp . "_today_night_temp",
						"babe_realtime_". $id . "_{$month}_{$date}_14_temp",
						"babe_realtime_". $id . "_{$month}_{$date}_14_weather",
						"babe_realtime_". $id . "_{$month}_{$date}_17_temp",
						"babe_realtime_". $id . "_{$month}_{$date}_17_weather",
						"babe_realtime_". $id . "_{$month}_{$date}_21_temp",
						"babe_realtime_". $id . "_{$month}_{$date}_21_weather",
						"mybabe_id" . $id ."_". $timestamp . "_tomorrow_day_temp",
						"mybabe_id" . $id ."_". $timestamp . "_tomorrow_day_weather",
						"mybabe_id" . $id ."_". $timestamp . "_tomorrow_night_temp",
						"mybabe_id" . $id ."_". $timestamp . "_tomorrow_night_weather",
						
						"caiyun_daily_" . $id ."_". $timestamp . "_temperature_max",
						"caiyun_daily_" . $id ."_". $timestamp . "_temperature_avg",
						"caiyun_daily_" . $id ."_". $timestamp . "_temperature_min",
						"caiyun_hour_" . $id ."_". $caiyun_timestamp . $hour . "_temperature",
						"caiyun_hour_" . $id ."_". $caiyun_timestamp .  "14_temperature",
						"caiyun_hour_" . $id ."_". $caiyun_timestamp .  "18_temperature",
						"caiyun_hour_" . $id ."_". $caiyun_timestamp .  "21_temperature"

);

$keys = array('temp','weather','rtemp','rweather','today_weather',
			  'today_temp','day_weather','night_weather','day_temp','night_temp',
			  'afternoon_temp','afternoon_weather','prenight_temp','prenight_weather',
			  'tonight_temp','tonight_weather','tomo_morning_temp','tomo_morning_weather',
			  'tomo_night_temp','tomo_night_weather',
			  'cy_temp_max','cy_temp_avg','cy_temp_min','cy_realtime_temperature','cy_temp_14','cy_temp_18','cy_temp_21');

$primaryvals = $redis->mGet($primarylist);
$primary_data = array_combine($keys, $primaryvals);
echo json_encode($primary_data);
return;
?>
