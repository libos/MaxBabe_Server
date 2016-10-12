<?php 

$err = array("state"=>"err");
if(!isset($_POST['id'])  || !isset($_POST['auth']) )
{
	echo json_encode($err);
	return;
}
$id = $_POST['id'];
$city = $id;
$auth = $_POST['auth'];

if (!($id)) {
	echo json_encode($err);
	return;
}
if ($auth != md5($id . ". maxtain . mybabe ")) {
	echo json_encode($err);
	return;
}

include "/etc/database/setting.inc";
$dsettings = new DefaultSettingMakeSureNoSameName();

$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user, $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}

$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

$city_info = array();
$city_sql = "select * from city where name='{$city}' or (name like '%{$city}%') or ('$city' like CONCAT('%',name,'%')) limit 1;";
//echo $city_sql;
if ($result = $mysqli->query($city_sql)) {
	$obj = $result->fetch_object();
	if (!isset($obj)) {
		echo json_encode(array("state"=>"err"));
		$mysqli->close();
		return;
	}
	$thecity = $obj;
	$city_info = array("id"=>$thecity->id,"name"=> $thecity->name,"level2" => $thecity->level2,
						"pinyin" => $thecity->pinyin,"province" => $thecity->province);

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

echo json_encode($city_info);
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


?>
