<?php

include "/alidata/www/BebeServ/plugin/simple_html_dom.php";
include "/etc/database/setting.inc";

set_time_limit(0);
define("TTL",345600);
define("mTTL",172800);
date_default_timezone_set("Asia/Harbin");
$dsettings = new DefaultSettingMakeSureNoSameName();


$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth($dsettings->redis_pass);


$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user , $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

$fxArr = array( "西南风"=>"SW","东南风"=>"SE","西北风"=>"NW",
				"东北风"=>"NE","东风"=>"E","西风"=>"W","南风"=>"S","北风"=>"N");

$city_sql = "select * from city_new;";
if ($result = $mysqli->query($city_sql)) {
	while($obj = $result->fetch_object()){
		$lastid = $obj->id;
		$name = $obj->name;
		$pinyin = $obj->pinyin;
		$province = $obj->province;

		$uuid = $obj->uuid;
		$datafrom = $obj->datafrom;
		$aqifrom = $obj->aqifrom;

		$real_time_update_time ="";
		$real_time_temp ="";
		$real_time_temp_huashi = "";
		$real_time_fengxiang = "";
		//$real_time_fengxiang_code = "";
		$real_time_fengxiang_level = "";
		$real_time_humidity = "";
		$real_time_weather = "";
		$aqi_level = "";
		$three_days_data = "";
		
		$today_day_weather = "";
		$today_night_weather = "";
		$today_day_temp = "";
		$today_night_temp ="";

		$tomorrow_day_weather = "";
		$tomorrow_night_weather = "";
		$tomorrow_day_temp = "";
		$tomorrow_night_temp = "";

		$today_weather_detail = "";
		$is_there_alarm = false;
		$alarm_type = "";
		$alarm_level = "";
		$alarm_issuetime = "";
		$alarm_content = "";
		$alarm_type_en = "";
		$alarm_level_en = "";
		if ( $datafrom == "weather.com.cn" ) {
			// $option = array(
			// 	'http' => array('header' => "Referer:http://www.weather.com.cn/")
			// );
			// $citydata = file_get_html("http://d1.weather.com.cn/sk_2d/" . $uuid . ".html",false,stream_context_create($option))->plaintext;
			$citydata_obj = file_get_htmlx("http://d1.weather.com.cn/sk_2d/" . $uuid . ".html");
			$iter = 2;
			while(!is_object($citydata_obj)){
				$citydata_obj = file_get_htmlx("http://d1.weather.com.cn/sk_2d/" . $uuid . ".html");
				$iter -- ;
				echo "retry $iter\n";
				if ($iter <=0) {
					break;
				}
			}
			$citydata = $citydata_obj->plaintext;
			$cd = explode("=", $citydata);
			$skarr = json_decode($cd[1]);
			//get real time temp
			if ($skarr->temp != "暂无实况") {
				$real_time_update_time = $skarr->time;
				$real_time_temp = $skarr->temp;
				$real_time_temp_huashi = $skarr->tempf;		//华氏摄氏度
				$real_time_fengxiang = $skarr->WD;
				//$real_time_fengxiang_code = $fxArr[$skarr->WD];
				$real_time_fengxiang_level =  $skarr->WS;
				if (!empty($skarr->aqi)) {
					$aqi_level = $skarr->aqi;	
				}
				$real_time_humidity = $skarr->SD;
				$real_time_weather = $skarr->weather;//晴、阴

			}else{

			}

			//get tomorrow temp
			// $three_days_arr = array();
			$four_days_arr = array();
			$get_three_days_temp_from_mobile = file_get_htmlx("http://m.weather.com.cn/mweather/" . $uuid . ".shtml");
			$iter = 3;
			while(!is_object($get_three_days_temp_from_mobile))
			{
				$get_three_days_temp_from_mobile = file_get_htmlx("http://m.weather.com.cn/mweather/" . $uuid . ".shtml");
				$iter --;
				echo "retry... $iter..\n";
				if($iter <=0)
					break;
			}
			if(is_object($get_three_days_temp_from_mobile)){
				foreach ($get_three_days_temp_from_mobile->find('.days7 li') as $idx => $li) {
					$now_hour = date('G');
					// $tmp_day_weather = "";
					$tmp_day_weather = "";
					$tmp_night_weather = "";
					$tmp_day_temp = "";
					$tmp_night_temp = "";
					$imgcount = $li->find('img',1);
					if (($now_hour >= 18 || !is_object($imgcount)) && $idx == 0) {
						// $tmp_day_weather = $li->find('img',0)->alt;
					 
							$tmp_night_weather = $li->find('img',0)->alt;
							$tmp_night_temp = str_replace("℃","",$li->find('span',0)->plaintext);	
				 
						// $tmp_temp_explode_array = explode('/', $tmp_temp);
						// $tmp_day_temp = str_replace("℃","",$tmp_temp_explode_array[0]);
						// $tmp_night_temp = str_replace("℃","",$tmp_temp_explode_array[1]);

					}else{
						$tmp_day_weather = $li->find('img',0)->alt;
						$tmp_night_weather = $li->find('img',1)->alt;
						$tmp_temp = $li->find('span',0)->plaintext;
						$tmp_temp_explode_array = explode('/', $tmp_temp);
						$tmp_day_temp = str_replace("℃","",$tmp_temp_explode_array[0]);
						$tmp_night_temp = str_replace("℃","",$tmp_temp_explode_array[1]);
					}

					if ($idx == 0) {
						if ($now_hour >= 18 || !is_object($imgcount)) 
						{
							$today_night_temp = $tmp_night_temp;	
							$today_night_weather = $tmp_night_weather;
						}else{
							$today_day_weather = $tmp_day_weather;
							$today_night_weather = $tmp_night_weather;
							$today_day_temp = $tmp_day_temp;
							$today_night_temp = $tmp_night_temp;	
						}
						
					}
					if ($idx == 1) {
						$tomorrow_day_weather = $tmp_day_weather;
						$tomorrow_night_weather = $tmp_night_weather;
						$tomorrow_day_temp = $tmp_day_temp;
						$tomorrow_night_temp = $tmp_night_temp;
					}
					if ($idx >= 1) {
						$four_days_arr[$idx] = array(	"day_weather"	=>	$tmp_day_weather	,
										"night_weather"	=>	$tmp_night_weather	,
										"day_temp"	=>	$tmp_day_temp		,
										"night_temp"	=>	$tmp_night_temp
									);
					}
					if ($idx == 4) {
						break;
					}
				}
			}
			//print_r($three_days_arr);

			// $three_days_data = json_encode($three_days_arr);
			$four_days_data = json_encode($four_days_arr);
			//echo $four_days_data;
			//get alarm and part of temp;
			// $option = array(
			// 	'http' => array('header' => "Referer:http://m.weather.com.cn/mweather/101010100.shtml")
			// );
	 		
			$city_alarm_obj = file_get_htmlx("http://d1.weather.com.cn/dingzhi/" . $uuid . ".html");
			$iter = 3;
			while(!is_object($city_alarm_obj))
			{
				$city_alarm_obj = file_get_htmlx("http://d1.weather.com.cn/dingzhi/" . $uuid . ".html");
				$iter --;
				echo "retry... $iter..\n";
				if($iter <=0)
					break;
			}
			if (is_object($city_alarm_obj)) {
				$city_alarm = $city_alarm_obj->plaintext;
				$tmp_alarm = explode('=', $city_alarm);
				if (count($tmp_alarm) == 3) {
					$tmp_info = explode(';', $tmp_alarm[1]);
					$info = json_decode($tmp_info[0]);
					$alarm = json_decode($tmp_alarm[2]);
					$today_day_temp = str_replace("℃","",$info->weatherinfo->temp);
					$today_night_temp = str_replace("℃","",$info->weatherinfo->tempn);	 
					// $temp_day = $info->temp;
					// $temp_night = $info->tempn;
					$today_weather_detail = $info->weatherinfo->weather;
					if (!empty($alarm->w) && isset($alarm->w[0]->w11)) {
						$is_there_alarm = true;
						$alarm_detail = $alarm->w[0]->w11;
						$alarm_raw_data_from_js_obj = file_get_htmlx("http://www.weather.com.cn/data/alarm/" . $alarm_detail);
						$iter = 3;
						while(!is_object($alarm_raw_data_from_js_obj))
						{
							$alarm_raw_data_from_js_obj = file_get_htmlx("http://www.weather.com.cn/data/alarm/" . $alarm_detail);
							$iter --;
							echo "retry... $iter..\n";
							if($iter <=0)
								break;
						}
						if (is_object($alarm_raw_data_from_js_obj)) {
							$alarm_raw_data_from_js = $alarm_raw_data_from_js_obj->plaintext;
							$alarm_json_parse = explode('=', $alarm_raw_data_from_js);
							if (count($alarm_json_parse) == 2) {
								$alarm_detail_structure = json_decode($alarm_json_parse[1]);
								$alarm_type = $alarm_detail_structure->SIGNALTYPE;
								$alarm_level = $alarm_detail_structure->SIGNALLEVEL;
								$alarm_issuetime = $alarm_detail_structure->ISSUETIME;
								$alarm_content = $alarm_detail_structure->ISSUECONTENT;
								$alarm_type_en = $alarm_detail_structure->YJTYPE_EN;
								$alarm_level_en = $alarm_detail_structure->YJYC_EN;
							}
						}else{
							$is_there_alarm = false;
						}
					}else{
						$is_there_alarm = false;
					}
				}	
			}else{
				$is_there_alarm = false;
			}
			
	  	}

		$is_there_alarm_t = $is_there_alarm == true ? "yes,there is." : "no, false alarm";
		$output = <<<EOD
{$name},{$pinyin},{$uuid},
{$real_time_update_time},{$real_time_temp},{$real_time_temp_huashi},
{$real_time_fengxiang},{$real_time_fengxiang_level},
{$real_time_humidity},{$real_time_weather},
{$aqi_level},
{$four_days_data},
{$today_day_weather},{$today_night_weather},{$today_day_temp},{$today_night_temp},
{$tomorrow_day_weather},{$tomorrow_night_weather},{$tomorrow_day_temp},{$tomorrow_night_temp},
{$today_weather_detail},
{$is_there_alarm_t},
{$alarm_type},{$alarm_level},{$alarm_issuetime},{$alarm_content},{$alarm_type_en},{$alarm_level_en}
EOD;
		// echo $output;

		$timestamp = date('Ymd');
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_four_days_data",TTL,$four_days_data);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_update_time",TTL,$real_time_update_time);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_temp",TTL,$real_time_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_temp_huashi",TTL,$real_time_temp_huashi);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_fengxiang",TTL,$real_time_fengxiang);
		//$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_fengxiang_code",TTL,$real_time_fengxiang_code);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_fengxiang_level",TTL,$real_time_fengxiang_level);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_humidity",TTL,$real_time_humidity);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_weather",TTL,$real_time_weather);
//		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_aqi_level",TTL,$aqi_level);
		// $redis->setex("mybabe_id" . $name ."_". $timestamp . "_three_days_data",TTL,$three_days_data);
		if(!empty($today_day_weather) && $today_day_weather != "")
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_day_weather",TTL,$today_day_weather);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_night_weather",TTL,$today_night_weather);
		if(!empty($today_day_temp) && $today_day_temp!="")
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_day_temp",TTL,$today_day_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_night_temp",TTL,$today_night_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_tomorrow_day_weather",TTL,$tomorrow_day_weather);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_tomorrow_night_weather",TTL,$tomorrow_night_weather);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_tomorrow_day_temp",TTL,$tomorrow_day_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_tomorrow_night_temp",TTL,$tomorrow_night_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_weather_detail",TTL,$today_weather_detail);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_is_there_alarm",TTL,($is_there_alarm == true ? 1 : 0));
		if ($is_there_alarm) {
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_type",TTL,$alarm_type);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_level",TTL,$alarm_level);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_issuetime",TTL,$alarm_issuetime);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_content",TTL,$alarm_content);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_type_en",TTL,$alarm_type_en);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_level_en",TTL,$alarm_level_en);
		}

		$timestamp = date('Ymd', strtotime(' +1 day'));
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_four_days_data",mTTL,$four_days_data);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_update_time",mTTL,$real_time_update_time);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_temp",mTTL,$real_time_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_temp_huashi",mTTL,$real_time_temp_huashi);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_fengxiang",mTTL,$real_time_fengxiang);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_fengxiang_level",mTTL,$real_time_fengxiang_level);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_humidity",mTTL,$real_time_humidity);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_real_time_weather",mTTL,$real_time_weather);
		if(!empty($today_day_weather) && $today_day_weather != "")
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_day_weather",mTTL,$today_day_weather);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_night_weather",mTTL,$today_night_weather);
		if(!empty($today_day_temp) && $today_day_temp!="")
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_day_temp",mTTL,$today_day_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_night_temp",mTTL,$today_night_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_tomorrow_day_weather",mTTL,$tomorrow_day_weather);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_tomorrow_night_weather",mTTL,$tomorrow_night_weather);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_tomorrow_day_temp",mTTL,$tomorrow_day_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_tomorrow_night_temp",mTTL,$tomorrow_night_temp);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_today_weather_detail",mTTL,$today_weather_detail);
		$redis->setex("mybabe_id" . $name ."_". $timestamp . "_is_there_alarm",mTTL,($is_there_alarm == true ? 1 : 0));
		if ($is_there_alarm) {
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_type",mTTL,$alarm_type);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_level",mTTL,$alarm_level);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_issuetime",mTTL,$alarm_issuetime);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_content",mTTL,$alarm_content);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_type_en",mTTL,$alarm_type_en);
			$redis->setex("mybabe_id" . $name ."_". $timestamp . "_alarm_level_en",mTTL,$alarm_level_en);
		}




		echo "{$name} update done \n";
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


?>
