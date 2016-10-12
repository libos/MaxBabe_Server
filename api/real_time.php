<?php

$err = array("state"=>"err");
if(!isset($_POST['id']) || !isset($_POST['auth']) || !isset($_POST['city']) )
{
	echo json_encode($err);
	return;
}
$id = $_POST['id'];
$auth = $_POST['auth'];
$city = $_POST['city'];
$hour = $_POST['hour'];
$month = $_POST['month'];
$date = $_POST['day'];

if (!($id)) {
	echo json_encode($err);
	return;
}

//echo md5($id . ". maxtain ." . $type . ". mybabe ");
if ($auth != md5($city . ". maxtain" . $hour . " . mybabe ")) {
	echo json_encode($err);
	return;
}

include "/etc/database/setting.inc";
date_default_timezone_set("Asia/Harbin");
$dsettings = new DefaultSettingMakeSureNoSameName();

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth($dsettings->redis_pass);

//$redis->setex(,TTL,$temString);
$key_temp =  "babe_realtime_". $city . "_{$month}_{$date}_{$hour}_temp";
$key_weather =  "babe_realtime_". $city . "_{$month}_{$date}_{$hour}_weather";
echo $key_temp . "\n";
echo $key_weather . "\n";
$val_temp = $redis->get($key_temp);
$val_weather = $redis->get($key_weather);

$redis->close();
$weather = array('rtemp' => $val_temp, 'rweather'=> $val_weather);

echo json_encode($weather);

return;




