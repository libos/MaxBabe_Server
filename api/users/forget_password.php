<?php 
$err = array("state"=>"err");
if(!isset($_POST['email']) || !isset($_POST['auth']) )
{
	echo json_encode($err);
	return;
}

$email = $_POST['email'];
$auth = $_POST['auth'];

if (!($email)) {
	echo json_encode($err);
	return;
}
if ($auth != md5($email . ". maxtain . mybabe . forgetpassword ")) {
	echo json_encode($err);
	return;
}


include "/etc/database/setting.inc";
$dsettings = new DefaultSettingMakeSureNoSameName();

$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user, $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
   	echo json_encode($err);
    exit();
    return;
}

$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

$email = strtolower($mysqli->real_escape_string($email));




$no_user_err = array('state'=>'no_user');
$field = "email";
if(!filter_var($email, FILTER_VALIDATE_EMAIL))
{
	echo json_encode($no_user_err);
	return;

	// $field = "phone";
}
$app_user = array();
$is_there = "select email,nickname from app_user where {$field} = '{$email}' ";
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
	$app_user = array('state'=>'ok','email'=>$user->email,'nickname'=>$user->nickname);
}else{
  echo json_encode(array("state"=>"err"));
  $result->close();
  free_mysqli();
  $mysqli->close();
  return;
}
define("TTL",86400);	//1days
date_default_timezone_set("Asia/Harbin");
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth($dsettings->redis_pass);

$now = date("YmdHis");
$hash = md5($app_user['email'] . $now . " forgetpassword " . $app_user['nickname']);

// $redis->setex('_maxbabe_passwd_fg_reset' . $app_user['email'] . "_key",TTL,$now);
$redis->setex('_maxbabe_passwd_fg_reset' . $app_user['email'] . "_hash",TTL,$hash);

$redis->close();
$fp = fsockopen("api.babe.maxtain.com", 80, $errno, $errstr, 30);  
if (!$fp){
    echo 'error fsockopen';
}else{
    stream_set_blocking($fp,0);  
    $http = "GET /users/sendmail.php?hash={$hash}&email={$app_user['email']}&nickname={$app_user['nickname']} HTTP/1.1\r\n";
    $http .= "Host: api.babe.maxtain.com\r\n";
    $http .= "Connection: Close\r\n\r\n";
    fwrite($fp,$http);
    fclose($fp);
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
