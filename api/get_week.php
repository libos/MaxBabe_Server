<?php
$err = array("state"=>"err");
if(!isset($_POST['auth']) || !isset($_POST['city']) )
{
	echo json_encode($err);
	return;
}

$auth = $_POST['auth'];
$city = $_POST['city'];
$year = $_POST['year'];
$date = $_POST['date'];
$month = $_POST['month'];

//echo md5($id . ". maxtain ." . $type . ". mybabe ");
if ($auth != md5($city . ". maxtain" . $date . " . mybabe ")) {
	echo json_encode($err);
	return;
}

include "/etc/database/setting.inc";
date_default_timezone_set("Asia/Harbin");
$dsettings = new DefaultSettingMakeSureNoSameName();

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth($dsettings->redis_pass);

$key_four =  "mybabe_id" . $city ."_". $year . $month . $date . "_four_days_data";
$val_four = $redis->get($key_four);

$data = json_decode($val_four,true);
for ($idx=0; $idx < 4; $idx++) { 
	$timestamp = date('Ymd', strtotime(" +{$idx} day"));
	$data[$idx]["night_temp"] = $redis->get("caiyun_daily_" . $city ."_". $timestamp . "_temperature_min");
	$data[$idx]["day_temp"] = $redis->get("caiyun_daily_" . $city ."_". $timestamp . "_temperature_max");
	if ($idx == 0) {
		$data[$idx]["night_weather"] = $redis->get("caiyun_daily_" . $city ."_". $timestamp . "_temperature_min");
		$data[$idx]["day_weather"] = $redis->get("caiyun_daily_" . $city ."_". $timestamp . "_temperature_max");
	}
}

//echo $key_four . "\n";

$redis->close();
// print_r(json_decode($val_four));
// echo $val_four;
//$new_array = array_merge(,$temperature);

echo json_encode($data);


