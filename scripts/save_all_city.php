<?php
/*
create database mybabe CHARACTER SET utf8 COLLATE utf8_general_ci;
use mybabe;
CREATE TABLE `city` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pinyin` varchar(255) DEFAULT NULL,
  `level2` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `uuid` varchar(255) DEFAULT NULL,
  `aqi_uuid` varchar(255) DEFAULT NULL,
  `englishname` varchar(255) DEFAULT NULL,
  `ext` varchar(255) DEFAULT NULL,
  `datafrom` varchar(255) DEFAULT NULL,
  `aqifrom` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) DEFAULT CHARSET=utf8;
*/
include "../config/city.inc";
include "../plugin/simple_html_dom.php";
include "/etc/database/setting.inc";
$dsettings = new DefaultSettingMakeSureNoSameName();

$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user, $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}
$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

foreach ($city_code as $name => $uuid) {
	
	$option = array(
		'http' => array('header' => "Referer:http://www.weather.com.cn/weather1d/101101100.shtml")
	);
	$citydata = file_get_html("http://d1.weather.com.cn/sk_2d/" . $uuid . ".html",false,stream_context_create($option))->plaintext;
	
	$cd = explode("=", $citydata);
	$arr = json_decode($cd[1]);
  $weather = $arr->weather;
  $aqi_uuid="";
  $aqifrom = "";
  $aqi = 0;
  $datafrom = "weather.com.cn";
  if (!empty($arr->aqi)) {
    $aqi_uuid = $uuid;
    $aqifrom = $datafrom;
    $aqi = $arr->aqi;
  }

// print_r($arr);
  $html = file_get_html("http://www.weather.com.cn/weather1d/{$uuid}.shtml");

	// $city = $html->find(".cityName .f1 h1",0)->plaintext;
  
  $p2 = $html->find(".cityName h2",0);
  $level2 = "";
  if (!empty($p2)) {
    $level2 = $p2->plaintext;
  }
  $p3 = $html->find(".cityName>.f1>h3",0)->plaintext;

 	$sql =<<<EOD
 insert into city (id,name,pinyin,level2,province,country,uuid,aqi_uuid,englishname,ext,datafrom,aqifrom)
 values (NULL,"{$name}","{$arr->nameen}","{$level2}","{$p3}","中国","{$uuid}","{$aqi_uuid}","{$arr->nameen}","{$weather}","{$datafrom}","{$aqifrom}");
EOD;
//echo $sql . "\n";

  if ($mysqli->query($sql)) {
      echo "insert city {$name} done.AQI:{$aqi}.\n";
      free_mysqli();
  }else{
      print_r($mysqli->error);
      exit(1);
  }

}
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
