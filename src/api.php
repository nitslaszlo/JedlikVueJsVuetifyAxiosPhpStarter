<?php 
$conn = new mysqli("localhost", "root", "", "desserts");

if ($conn->connect_error) {
	die("Database connection established failed!");
}
/* change character set to utf8 */
if ($conn->set_charset("utf8")) {
	// printf("Current character set: %s\n", $conn->character_set_name());
	$res = array('charset' => $conn->character_set_name());
	$res['error'] = false; // no error
} else {
	// printf("Error loading character set utf8: %s\n", $conn->error);
	$res = array('error' => true);
}

// $conn->query("SET GLOBAL sql_mode='STRICT_ALL_TABLES', SESSION sql_mode='STRICT_ALL_TABLES'");

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

if ($action == 'delete') {
	$id = $_POST['id'];
	$result = $conn->query("DELETE FROM `dessert` WHERE `id` = '$id'");
	if ($result) {
		$res['message'] = "Dessert deleted successfully!";
	} else{
		$res['error'] = true;
		$res['message'] = "Dessert delete failed";
	}
}

if ($action == 'update') {
	$id = $_POST['id'];
	$name = $_POST['name'];
	if (strlen($name) == 0) $name = NULL;
	$calories = $_POST['calories'];
	$fatPercent = $_POST['fatPercent'];
	$isPaleo = $_POST['isPaleo'];
	$edited = date('Y-m-d H:i:s');
	$result = $conn->query("UPDATE `dessert` SET `name` = '$name', `calories` = '$calories', `fatPercent` = '$fatPercent', `isPaleo` = '$isPaleo', `edited` = '$edited'  WHERE `id` = '$id'");
	if ($result) {
		$res['message'] = "Dessert updated successfully!";
	} else{
		$res['error'] = true;
		$res['message'] = "Dessert update failed!";
	}
}

if ($action == 'create') {
	$name = $_POST['name'];
	if (strlen($name) == 0) $name = NULL;
	$calories = $_POST['calories'];
	$fatPercent = $_POST['fatPercent'];
	$isPaleo = $_POST['isPaleo'];
	$created = date('Y-m-d H:i:s');

	$result = $conn->query("INSERT INTO `dessert` (`name`, `calories`, `fatPercent`, `isPaleo`, `created`) VALUES ( '$name', '$calories', '$fatPercent', '$isPaleo', '$created')");
	if ($result) {
		$res['message'] = "Dessert added successfully!";
	} else{
		$res['error'] = true;
		$res['message'] = "Insert dessert fail!";
	}
}

$conn -> close();

header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
echo(json_encode($res, JSON_UNESCAPED_UNICODE));
die();

 ?>