<?php 
$err = array("state"=>"err");
if(!isset($_POST['email'])  || !isset($_POST['password'])|| !isset($_POST['auth']) )
{
	echo json_encode($err);
	return;
}

$email = $_POST['email'];
$password = $_POST['password'];
$auth = $_POST['auth'];

if (!($email)) {
	echo json_encode($err);
	return;
}
if ($auth != md5($email . ". maxtain . mybabe . login " . $password)) {
	echo json_encode($err);
	return;
}


include "/etc/database/setting.inc";
$dsettings = new DefaultSettingMakeSureNoSameName();

$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user, $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
    // echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    // exit();
    echo json_encode($err);
    exit();
    return;
}

$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

$email = strtolower($mysqli->real_escape_string($email));
$password = md5(md5($mysqli->real_escape_string($password) . '@maxtain @babe @app @.'));


$app_user = array();

$field = "email";
if(!filter_var($email, FILTER_VALIDATE_EMAIL))
{
	$field = "phone";
}
$no_user_err = array('state'=>'no_user');
$is_there = "select * from app_user where {$field} = '{$email}' and password='{$password}'";
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
	$user = $obj;
	$app_user = array('state'=>'login','id'=>$user->id,'email'=>$user->email,
		'nickname'=>$user->nickname,'sex'=>"{$user->sex}",'phone'=>$user->phone);

}else{
  echo json_encode(array("state"=>"err"));
  $result->close();
  free_mysqli();
  $mysqli->close();
  return;
}

echo json_encode($app_user);
$result->close();
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
