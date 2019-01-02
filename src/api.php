<?php 
$conn = new mysqli("localhost", "root", "", "desserts");

if ($conn->connect_error) {
	die("Database connection established failed!");
}
/* change character set to utf8 */
if ($conn->set_charset("utf8")) {
	// printf("Current character set: %s\n", $conn->character_set_name());
	$res = array('charset' => $conn->character_set_name());
} else {
	// printf("Error loading character set utf8: %s\n", $conn->error);
	$res = array('error' => true);
}

// $res = array('error' => false); // no error
$res['error'] = false;

$action = 'read'; // default action

if (isset($_GET['action'])) { 
	$action = $_GET['action']; // get action value
}

if ($action == 'read') {
	$result = $conn->query("SELECT * FROM `dessert`");
	$desserts = array();
	// Fetch all
	$desserts = mysqli_fetch_all($result, MYSQLI_ASSOC);
	// while ($row = $result->fetch_assoc()){
	// 	array_push($desserts, $row);
	// }
	$res['desserts'] = $desserts;
}

$conn -> close();

header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
//$myjson = json_encode($res);

echo(json_encode($res, JSON_UNESCAPED_UNICODE));
die();

 ?>