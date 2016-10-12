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


$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

$city_sql = "select * from city_new;";
if ($result = $mysqli->query($city_sql)) {
	while($obj = $result->fetch_object()){
		$areaid = $obj->uuid;
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

		$longititude = $arr->c->c13;
		$latitude = $arr->c->c14;
		$atitude = $arr->c->c15;


		$sql = <<<EOD
		update city_new set lon = "{$longititude}",lat = "{$latitude}",ati = "{$atitude}" where uuid = "{$areaid}";
EOD;

		if ($mysqli->query($sql)) {
		   echo "update city $sql {$areaid} done.\n";
		   free_mysqli();
		}else{
		   print_r($mysqli->error);
		   exit(1);
		}

	}
}else{
	print_r($mysqli->error);
	exit(1);
}
$result->close();
free_mysqli();



$mysqli->close();


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
