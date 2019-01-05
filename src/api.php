<?php 
header('Content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("localhost", "root", "passwdRoot", "desserts");

if ($conn->connect_error) {
	die("Database connection established failed!");
}

$res = array();
// $res = array('error' => true);

/* change character set to utf8 */
if ($conn->set_charset("utf8")) {
	$res['error'] = false;
	$res['charset'] = $conn->character_set_name();
} else {
	// printf("Error loading character set utf8: %s\n", $conn->error);
	$res['error'] = true;
}

$conn->query("SET GLOBAL sql_mode='STRICT_ALL_TABLES', SESSION sql_mode='STRICT_ALL_TABLES'");

$action = 'read'; // default action

if (isset($_GET['action'])) { 
	$action = $_GET['action']; // get action value
}

if ($action == 'read') {
	try {
		$result = $conn->query("SELECT * FROM `dessert`");
		// Fetch all
		$desserts = array();
		$desserts = mysqli_fetch_all($result, MYSQLI_ASSOC);
		// while ($row = $result->fetch_assoc()){
		// 	array_push($desserts, $row);
		// }
		$res['desserts'] = $desserts;
		$res['message'] = "Dessert read successfully!";
	} catch (Exception $e) {
		$res['error'] = true;
		$res['exceptionMessage'] = $e->getMessage();
		$res['message'] = "Dessert read failed!";
	}
}

if ($action == 'delete') {
	$id = $_POST['id'];

	// $result = $conn -> query("DELETE FROM `dessert` WHERE `id` = '$id'");
	$prepared = $conn -> prepare("DELETE FROM `dessert` WHERE `id` = ?");
    if ($prepared == false) die("Error in delete (prepare)");
	$result = $prepared -> bind_param("s", $id);
	if ($result == false) die("Error in delete (bind)");
	
	try {
		$prepared->execute();
		$res['message'] = "Dessert delete successfully!";
	} catch (Exception $e) {
		$res['error'] = true;
		$res['exceptionMessage'] = $e->getMessage();
		$res['message'] = "Dessert delete failed!";
	}

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
	
	try {
		$prepared->execute();
		$res['message'] = "Dessert updated successfully!";
	} catch (Exception $e) {
		$res['error'] = true;
		$res['exceptionMessage'] = $e->getMessage();
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
	
	try {
		$prepared->execute();
		$res['message'] = "Dessert added successfully!";
	} catch (Exception $e) {
		$res['error'] = true;
		$res['exceptionMessage'] = $e->getMessage();
		$res['message'] = "Dessert added failed!";
	}

	$prepared -> close();
}

$conn -> close();

echo(json_encode($res, JSON_UNESCAPED_UNICODE));
die();

 ?>