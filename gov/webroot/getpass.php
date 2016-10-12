<?php 
$err = array("state"=>"err");

if(!isset($_POST['email']) || !isset($_POST['vstr']) )
{
	echo json_encode($err);
	return;
}

$email = $_POST['email'];
$hash = $_POST['vstr'];
$password = $_POST['password'];
$confirm_password = $_POST['password_confirm'];

if (!($email)) {
	echo json_encode($err);
	return;
}
if (strlen($password) < 6 || strlen($password) > 36) {
  echo json_encode(array('state'=>'password'));
  return;
}
if ($password != $confirm_password) {
 	echo json_encode($err);
	return;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL))
{
	echo json_encode($err);
	return;
}

include "/etc/database/setting.inc";
$dsettings = new DefaultSettingMakeSureNoSameName();

date_default_timezone_set("Asia/Harbin");
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->auth($dsettings->redis_pass);

$shash = $redis->get('_maxbabe_passwd_fg_reset' . $email . "_hash");
if ($hash != $shash) {
	echo json_encode(array('state'=>'illegal request'));
	$redis->close();
	return;
}

$mysqli = new mysqli($dsettings->mysql_host, $dsettings->mysql_user, $dsettings->mysql_pass, "mybabe");
if ($mysqli->connect_errno) {
   echo json_encode($err);
    exit();
    return;
}

$mysqli->query("SET NAMES 'utf8'");
$mysqli->autocommit(TRUE);

$email = $mysqli->real_escape_string($email);
$password = $mysqli->real_escape_string($password);
$password = md5(md5($password . '@maxtain @babe @app @.'));

$update_sql = "update app_user set password='{$password}' where email='{$email}';";

if ($mysqli->real_query($update_sql)) {
  echo json_encode( array('state' => 'done', 'user'=>$email));
  $redis->delete('_maxbabe_passwd_fg_reset' . $email . "_hash");
}else{
  echo json_encode($err);
}

free_mysqli();
$redis->close();
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
