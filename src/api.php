<?php 
header('Content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("localhost", "root", "root", "desserts");

if ($conn->connect_error) {
	die("Database connection established failed!");
}
/* change character set to utf8 */
if ($conn->set_charset("utf8")) {
	$res = array('error' => false);
	$res['charset'] = $conn->character_set_name();
} else {
	// printf("Error loading character set utf8: %s\n", $conn->error);
	$res = array('error' => true);
}

$conn->query("SET GLOBAL sql_mode='STRICT_ALL_TABLES', SESSION sql_mode='STRICT_ALL_TABLES'");

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
	// $result = $conn -> query("DELETE FROM `dessert` WHERE `id` = '$id'");
	$prepared = $conn -> prepare("DELETE FROM `dessert` WHERE `id` = ?");
    if ($prepared == false) die("Error in delete (prepare)");
	$result = $prepared -> bind_param("s", $id);
    if ($result == false) die("Error in delete (bind)");
	$result = $prepared->execute(); 
	if ($result) {
		$res['message'] = "Dessert deleted successfully!";
	} else{
		$res['error'] = true;
		$res['message'] = "Dessert delete failed";
	}
	/* close statement */
	$prepared -> close();
}

if ($action == 'update') {
	$id = $_POST['id'];
	$name = $_POST['name'];
	if (strlen($name) == 0) $name = NULL;
	$calories = $_POST['calories'];
	$fatPercent = $_POST['fatPercent'];
	$isPaleo = $_POST['isPaleo'];
	$edited = date('Y-m-d H:i:s');
	$prepared = $conn -> prepare("UPDATE `dessert` SET `name` = ?, `calories` = ?, `fatPercent` = ?, `isPaleo` = ?, `edited` = ? WHERE `id` = ?");
	if ($prepared == false) die("Error in update (prepare)");
	$result = $prepared->bind_param('sidisi', $name, $calories, $fatPercent, $isPaleo, $edited, $id);
	if ($result == false) die("Error in update (bind)");
	$result = $prepared->execute();
	if ($result) {
		$res['message'] = "Dessert updated successfully!";
	} else{
		$res['error'] = true;
		$res['message'] = "Dessert update failed!";
	}
	$prepared -> close();
}

if ($action == 'create') {
	$name = $_POST['name'];
	if (strlen($name) == 0) $name = NULL;
	$calories = $_POST['calories'];
	$fatPercent = $_POST['fatPercent'];
	$isPaleo = $_POST['isPaleo'];
	$created = date('Y-m-d H:i:s');

	//$result = $conn->query("INSERT INTO `dessert` (`name`, `calories`, `fatPercent`, `isPaleo`, `created`) VALUES ( '$name', '$calories', '$fatPercent', '$isPaleo', '$created')");
	$prepared = $conn -> prepare("INSERT INTO `dessert` (`name`, `calories`, `fatPercent`, `isPaleo`, `created`) VALUES ( ?, ?, ?, ?, ?)");
	if ($prepared == false) die("Error in create (prepare)");
	$result = $prepared->bind_param('sidis', $name, $calories, $fatPercent, $isPaleo, $created);
	if ($result == false) die("Error in create (bind)");
	$result = $prepared->execute();
	
	if ($result) {
		$res['message'] = "Dessert added successfully!";
	} else{
		$res['error'] = true;
		$res['message'] = "Insert dessert fail!";
	}
}

$conn -> close();

echo(json_encode($res, JSON_UNESCAPED_UNICODE));
die();

 ?>