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
if ($auth != md5($email . ". maxtain . mybabe . unique")) {
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

$is_there = "select email from app_user where email = '{$email}'";
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

echo json_encode(array("state"=>"ok"));
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
