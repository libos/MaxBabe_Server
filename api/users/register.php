<?php 
/*
create table app_user (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `sex` int(1) default 1,
  `weibo` varchar(255) default "",
  `weixin` varchar(255) default "",
  `qq`  varchar(255) default "",
  `facebook`  varchar(255) default "",
  `instagram`  varchar(255) default "",
  `line` varchar(255) default "",
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` datetime not null 
);
*/
$err = array("state"=>"err");
if(!isset($_POST['email']) || !isset($_POST['phone'])|| !isset($_POST['password']) || !isset($_POST['nick']) || !isset($_POST['auth']) )
{
	echo json_encode($err);
	return;
}

$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];
$nickname = $_POST['nick'];
$sex = $_POST['sex'] == "1" ? 1 : 0;
$auth = $_POST['auth'];
  
if (!($email)) {
	echo json_encode($err);
	return;
}
if ($auth != md5($email . ". maxtain . mybabe " . $password)) {
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
$phone = $mysqli->real_escape_string($phone);
$password = md5(md5($mysqli->real_escape_string($password) . '@maxtain @babe @app @.'));
$nickname = $mysqli->real_escape_string($nickname);

$is_there = "select email from app_user where email = '{$email}' or phone='{$phone}'";
if ($result = $mysqli->query($is_there)) {
  if ($result->num_rows >0) {
    echo json_encode(array("state"=>"duplicate"));
    $result->close();
    free_mysqli();
    $mysqli->close();
    return;
  }
  $result->close();
}else{
  echo json_encode(array("state"=>"errx"));
  $result->close();
  free_mysqli();
  $mysqli->close();
  return;
}

free_mysqli();

$reg_sql = "insert into app_user (id,email,phone,password,nickname,sex,created) values (NULL,'{$email}','{$phone}','{$password}','{$nickname}',{$sex},now());";
if ($mysqli->real_query($reg_sql)) {
  echo json_encode( array('state' => 'ok', 'user'=>$email));
}else{
  echo "hi";
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
