<?php

$err = array("state"=>"err");
if(!isset($_POST['auth']) || !isset($_POST['city']) )
{
	echo json_encode($err);
	return;
}

$auth = $_POST['auth'];
$city = $_POST['city'];
// $year = $_POST['year'];
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

$allday = array();

for ($idx=0; $idx < 24; $idx++) {
	$hour = sprintf("%02d",$idx);
	$t = $redis->get("babe_realtime_". $city . "_{$month}_{$date}_{$hour}_temp");
	$t = $t == false ? "" : $t;
	$w = $redis->get("babe_realtime_". $city . "_{$month}_{$date}_{$hour}_weather");
	$w = $w == false ? "" : $w;
	$allday[$hour] = array("t" => $t,  "w"=> $w  );
}
//print_r($allday);

$redis->close();
echo json_encode($allday);

return;
