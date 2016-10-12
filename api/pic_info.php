<?php 

$err = array("state"=>"err");
if(!isset($_POST['id']) || !isset($_POST['auth']) || !isset($_POST['reso']))
{
	echo json_encode($err);
	return;
}

date_default_timezone_set("Asia/Harbin");
$id = $_POST['id'];
$auth = $_POST['auth'];
$reso = $_POST['reso'];
$weather = $_POST['weather'];

$now_hour = $_POST['hour'];//G no 0
$now_days_inweek = $_POST['week'];	// no 0

$now_days_inmonth = $_POST['month'] ;	// no 0
$temp = $_POST["temp"];
$aqi = $_POST["aqi"];
if (!($id)) {
	echo json_encode($err);
	return;
}
if (!in_array($reso,array('xx','x')))
{	
	echo json_encode($err);
	return;
}
if ($auth != md5($id . ". maxtain . mybabe ")) {
	echo json_encode($err);
//error_log(date(DATE_RFC2822) ."\n".md5($id . ". maxtain . mybabe "). json_encode($_POST) . "\n",3,'/var/log/api_error.log');
	return;
}
//error_log(date(DATE_RFC2822) . json_encode($_POST) . "\n{$now_days_inmonth}" . "\n",3,'/var/log/api_error.log');
include "/etc/database/setting.inc";
$dsettings = new DefaultSettingMakeSureNoSameName();
/*
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth($dsettings->redis_pass);

$timestamp = date("Ymd");

$temp = $redis->get("mybabe_id" . $id ."_". $timestamp . "_real_time_temp");
$weather = $redis->get("mybabe_id" . $id ."_". $timestamp . "_real_time_weather");

$redis->close();
*/


$with_temp = true;
if(empty($temp))
{
   $with_temp = false;
}
$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user, $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

$background_link = "";
$background_md5 = "";
$bweather = "";
$bgehour = "";
$blehour = "";
$bgemonth = "";
$blemonth = "";
$bgeweek = "";
$bleweek = "";
$bgetemp = "";
$bletemp = "";
$bgeaqi = "";
$bleaqi = "";
$bupdate = "";

$figure_link = "";
$figure_md5 = "";
$fweather = "";
$fgehour = "";
$flehour = "";
$fgemonth = "";
$flemonth = "";
$fgeweek = "";
$fleweek = "";
$fgetemp = "";
$fletemp = "";
$fgeaqi = "";
$fleaqi = "";
$fupdate = "";

$words = "";
$oweather = "";
$ogehour = "";
$olehour = "";
$ogemonth = "";
$olemonth ="";
$ogeweek = "";
$oleweek = "";
$ogetemp = "";
$oletemp = "";
$ogeaqi = "";
$oleaqi = "";
$oupdate = "";


$temp_filter = "";
$reso_filter = "";
// if(!empty($reso))
// 	$reso_filter = "and (reso = '{$reso}')";
$weather_filter = "";

if($weather !="任意天气" && $weather != "*" && !empty($weather))
	$weather_filter = "and (weather = '任意天气' or weather='{$weather}' or '{$weather}' like CONCAT('%',weather,'%'))"; 

if($with_temp)
	$temp_filter = "and (ge_temp <= $temp and le_temp >= $temp)" ;
$aqi_filter = "";
if(!empty($aqi))
	$aqi_filter = "and (ge_aqi <= $aqi and le_aqi >= $aqi)";

$hour_filter = "((ge_hour <= $now_hour and le_hour >= $now_hour and ge_hour <= le_hour) or (ge_hour > le_hour and ((ge_hour <=$now_hour and $now_hour < 25) or (le_hour >= $now_hour and $now_hour >=0))))";
$week_filter = "((ge_week <= $now_days_inweek and le_week >= $now_days_inweek and ge_week <= le_week) or (ge_week > le_week and ((ge_week <= $now_days_inweek and $now_days_inweek <= 7) or (le_week >= $now_days_inweek and $now_days_inweek >=0))))";
$month_filter = "((ge_month <= $now_days_inmonth and le_month >=$now_days_inmonth and ge_month > le_month) or (ge_month < le_month and ((ge_month <=$now_days_inmonth and $now_days_inmonth <=31) or (le_month >= $now_days_inmonth and $now_days_inmonth >=0 ))))";
$filter = "{$hour_filter} and {$week_filter} and {$month_filter} {$weather_filter} {$temp_filter} {$aqi_fliter}";
//background
$filter_bg = $filter . " {$reso_filter}";
$bg_sql = "select * from background where $filter_bg;";
if ($result = $mysqli->query($bg_sql)) {
	$count = $result->num_rows;
	$idx = rand(0,$count-1);
	$result->data_seek(0);

	$obj = $result->fetch_object();
	$background_link = $obj->path;
	$background_md5 = $obj->md5;
	
	$bweather = $obj->weather;
	$bgehour = $obj->ge_hour;
	$blehour = $obj->le_hour;
	$bgemonth = $obj->ge_month;
	$blemonth = $obj->le_month;
	$bgeweek = $obj->ge_week;
	$bleweek = $obj->le_week;
	$bgetemp = $obj->ge_temp;
	$bletemp = $obj->le_temp;
	$bgeaqi = $obj->ge_aqi;
	$bleaqi = $obj->le_aqi;
	$bupdate = $obj->update_flag;
}else{
    print_r($mysqli->error);
	echo json_encode(array("state"=>"err"));
	$result->close();
	free_mysqli();
	$mysqli->close();
	return;
}
$result->close();
free_mysqli();


// figure
$fg_sql = "select * from figure where $filter_bg;";
if ($result = $mysqli->query($fg_sql)) {

	$count = $result->num_rows;
	$idx = rand(0,$count-1);
	$result->data_seek($idx);

	$obj = $result->fetch_object();
	$figure_link = $obj->path;
	$figure_md5 = $obj->md5;

	$fweather = $obj->weather;
	$fgehour = $obj->ge_hour;
	$flehour = $obj->le_hour;
	$fgemonth = $obj->ge_month;
	$flemonth = $obj->le_month;
	$fgeweek = $obj->ge_week;
	$fleweek = $obj->le_week;
	$fgetemp = $obj->ge_temp;
	$fletemp = $obj->le_temp;
	$fgeaqi = $obj->ge_aqi;
	$fleaqi = $obj->le_aqi;
	$fupdate = $obj->update_flag;

}else{
	echo json_encode(array("state"=>"err"));
	$result->close();
	free_mysqli();
	$mysqli->close();
	return;
}
$result->close();
free_mysqli();


$ow_sql = "select * from oneword where $filter;";
if ($result = $mysqli->query($ow_sql)) {
	$count = $result->num_rows;
	$idx = rand(0,$count-1);
	$result->data_seek($idx);

	$obj = $result->fetch_object();

	$words = $obj->word;
	$oweather = $obj->weather;
	$ogehour = $obj->ge_hour;
	$olehour = $obj->le_hour;
	$ogemonth = $obj->ge_month;
	$olemonth = $obj->le_month;
	$ogeweek = $obj->ge_week;
	$oleweek = $obj->le_week;
	$ogetemp = $obj->ge_temp;
	$oletemp = $obj->le_temp;
	$ogeaqi = $obj->ge_aqi;
	$oleaqi = $obj->le_aqi;
	$oupdate = $obj->update_flag;

}else{
	echo json_encode(array("state"=>"err"));
	$result->close();
	free_mysqli();
	$mysqli->close();
	return;
}
$result->close();
free_mysqli();

$mysqli->close();

$return_other_data = array('background'=>$background_link,'bmd5'=>$background_md5,'bweather'=>$bweather,'bgehour'=>$bgehour,'blehour'=>$blehour,'bgemonth'=>$bgemonth,'blemonth'=>$blemonth,'bgeweek'=>$bgeweek,'bleweek'=>$bleweek,'bgetemp'=>$bgetemp,'bletemp'=>$bletemp,'bgeaqi'=>$bgeaqi,'bleaqi'=>$bleaqi,'bupdate'=>$bupdate,
							'figure'=>$figure_link,'fmd5'=>$figure_md5,'fweather'=>$fweather,'fgehour'=>$fgehour,'flehour'=>$flehour,'fgemonth'=>$fgemonth,'flemonth'=>$flemonth,'fgeweek'=>$fgeweek,'fleweek'=>$fleweek,'fgetemp'=>$fgetemp,'fletemp'=>$fletemp,'fgeaqi'=>$fgeaqi,'fleaqi'=>$fleaqi,'fupdate'=>$fupdate,
							'words'=>$words,'oweather'=>$oweather,'ogehour'=>$ogehour,'olehour'=>$olehour,'ogemonth'=>$ogemonth,'olemonth'=>$olemonth,'ogeweek'=>$ogeweek,'oleweek'=>$oleweek,'ogetemp'=>$ogetemp,'oletemp'=>$oletemp,'ogeaqi'=>$ogeaqi,'oleaqi'=>$oleaqi,'oupdate'=>$oupdate);
echo json_encode($return_other_data);
return;



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

