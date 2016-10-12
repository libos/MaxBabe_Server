<?php
include "/alidata/www/BebeServ/plugin/simple_html_dom.php";
include "/etc/database/setting.inc";

set_time_limit(0);
define("TTL",345600);	//2days
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



$city_sql = "select * from city;";
if ($result = $mysqli->query($city_sql)) {
	while($obj = $result->fetch_object()){
		$lastid = $obj->id;
		$name = $obj->name;
		// $pinyin = $obj->pinyin;
		// $province = $obj->province;
		$uuid = $obj->uuid;

		get_weather($uuid,$name,$redis);
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
	$weather = array(
	"00"=>"晴",
	"01"=>"多云",
	"02"=>"阴",
	"03"=>"阵雨",
	"04"=>"雷阵雨",
	"05"=>"雷阵雨伴有冰雹",
	"06"=>"雨夹雪",
	"07"=>"小雨",
	"08"=>"中雨",
	"09"=>"大雨",
	"10"=>"暴雨",
	"11"=>"大暴雨",
	"12"=>"特大暴雨",
	"13"=>"阵雪",
	"14"=>"小雪",
	"15"=>"中雪",
	"16"=>"大雪",
	"17"=>"暴雪",
	"18"=>"雾",
	"19"=>"冻雨",
	"20"=>"沙尘暴",
	"21"=>"小雨到中雨",
	"22"=>"中雨到大雨",
	"23"=>"大雨到暴雨",
	"24"=>"暴雨到大暴雨",
	"25"=>"大暴雨到特大暴雨",
	"26"=>"小雪到中雪",
	"27"=>"中雪到大雪",
	"28"=>"大雪到暴雪",
	"29"=>"浮尘",
	"30"=>"扬沙",
	"31"=>"强沙尘暴",
	"53"=>"霾");
	$ch = curl_init();
	$url = "http://m.weather.com.cn/mpub/hours/".$cityNumber.".html";
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, "http://m.weather.com.cn/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
	$jsonData = curl_exec($ch);
	if ($jsonData) {
		$jh = json_decode($jsonData,true);
		if ($jh) {
			$jsonData = $jh["jh"];
			if ($jsonData) {
				$mm = date('m');
				$dd = date('d');
				// $allday = array();
				foreach ($jsonData as $hoursData) {
					$time = $hoursData["jf"];
					$year = substr($time, 0, 4);
					$mon = $mm;//substr($time, 4, 2);
					$day = $dd;//substr($time, 6, 2);
					$hours = substr($time, 8, 2);
					$min = substr($time, 10, 2);
					// $timeString = $year.'.'.$mon.'.'.$day."->".$hours.':'.$min;
					$weatherString =$weather[$hoursData["ja"]];
					$temString = $hoursData["jb"];

					// $allday[] = array("t"=>$temString,"w"=>$weatherString);
					$redis->setex("babe_realtime_". $cityName . "_{$mon}_{$day}_{$hours}_temp",TTL,$temString);
					$redis->setex("babe_realtime_". $cityName . "_{$mon}_{$day}_{$hours}_weather",TTL,$weatherString);
				}
				// $timestamp = 
				// $redis->setex("babe_allday_". $cityName . "_{$timestamp}",TTL,json_encode($allday));
				echo $cityName . "update done\n";
			}
		}
	}
}

?>
