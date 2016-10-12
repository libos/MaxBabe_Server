<?php 
$err = array("state"=>"err");
if(!isset($_POST['email'])  || !isset($_POST['password'])
	|| !isset($_POST['auth']) || !isset($_POST['field_name'])
	|| !isset($_POST['field_value']) ) 
{
	echo json_encode($err);
	return;
}

$email = $_POST['email'];
$password = $_POST['password'];
$field_name = $_POST['field_name'];
$field_value = $_POST['field_value'];
$auth = $_POST['auth'];

if (!($email)) {
	echo json_encode($err);
	return;
}
if ($auth != md5($email . ". maxtain . mybabe . update " . $password)) {
	echo json_encode($err);
 	return;
}
if (!in_array($field_name, array('nick','sex','new_password','phone'))) {
	echo json_encode($err);
	return;
}
include "/etc/database/setting.inc";
$dsettings = new DefaultSettingMakeSureNoSameName();

$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user, $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
    echo json_encode($err);//echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
    return;
}

$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

$email = strtolower($mysqli->real_escape_string($email));
$password = md5(md5($mysqli->real_escape_string($password) . '@maxtain @babe @app @.'));
if ($field_name == 'sex') {
	$field_value = $field_value == "1" ? 1 : 0;	
}else{
	$field_value = $mysqli->real_escape_string($field_value);
}
if ($field_name == "nick"){
	$field_name = "nickname";
}
if ($field_name == "new_password") {
	$field_name = "password";
	if (strlen($field_value) <6 || strlen($field_value) > 36) {
		echo json_encode($err);
		$mysqli->close();
		return;
	}
	$field_value = md5(md5($mysqli->real_escape_string($field_value) . '@maxtain @babe @app @.'));
}

if ($field_name == "phone") {
	$is_there_phone = "select phone from app_user where phone = '{$field_value}'";
	if ($result = $mysqli->query($is_there_phone)) { 
	  if ($result->num_rows >0) {
	    echo json_encode(array("state"=>"duplicate"));
	    $result->close();
	    free_mysqli();
	    $mysqli->close();
	    return;
	  }
	  $result->close();
	  free_mysqli();
	}else{
	  echo json_encode(array("state"=>"errx"));
	  $result->close();
	  free_mysqli();
	  $mysqli->close();
	  return;
	}
}

$app_user = array();

$last_id = 0;
$no_user_err = array('state'=>'no_user');
$is_there = "select id from app_user where email = '{$email}' and password='{$password}'";
if ($result = $mysqli->query($is_there)) {
	if ($result->num_rows == 0) {
		echo json_encode($no_user_err);
		$result->close();
		$mysqli->close();
		return;
	}
	$obj = $result->fetch_object();
	if (!isset($obj)) {
		echo json_encode($no_user_err);
		$result->close();
		$mysqli->close();
		return;
	}
	$last_id = $obj->id;
	$result->close();
	free_mysqli();


}else{
  echo json_encode(array("state"=>"err"));
  $result->close();
  free_mysqli();
  $mysqli->close();
  return;
}

$update_sql = "update app_user set {$field_name}='{$field_value}' where id={$last_id}";
if ($field_name == 'sex') {
	$update_sql = "update app_user set {$field_name}={$field_value} where id={$last_id}";
}
if ($mysqli->real_query($update_sql)) {
  echo json_encode( array('state' => 'done', 'user'=>$email));
}else{
  echo json_encode($err);
} 
free_mysqli();
$mysqli->close();
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
