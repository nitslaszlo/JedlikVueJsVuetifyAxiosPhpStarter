<?php 
header('Content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$res = array('error' => false);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn= NULL;

// Connect to mySQL host:
try {
		$conn = new mysqli("localhost", "root", "", "desserts");
} catch (Exception $e) {
		$res['error'] = true;
		$msg = $e->getMessage();
		$res['message'] = "Database connection established failed! {$msg}";
		echo(json_encode($res, JSON_UNESCAPED_UNICODE));
		die();
}


// Change character set to utf8: (don't throw exception)
if ($conn->set_charset("utf8")) {
	$res['charset'] = $conn->character_set_name();
} else {
	$res['error'] = true;
	$msg = $conn->error;
	$res['message'] = "Change character set to utf8 failed! {$msg}";
	echo(json_encode($res, JSON_UNESCAPED_UNICODE));
	die();
}

$conn->query("SET GLOBAL sql_mode='STRICT_ALL_TABLES', SESSION sql_mode='STRICT_ALL_TABLES'");

$action = 'read'; // default action

if (isset($_GET['action'])) { 
	$action = $_GET['action']; // get action value
}

if ($action == 'read') {
	try {
		$table = 'dessert';
		$result = $conn->query("SELECT * FROM {$table}");
		// Fetch all
		$desserts = array();
		$desserts = mysqli_fetch_all($result, MYSQLI_ASSOC);
		// while ($row = $result->fetch_assoc()){
		// 	array_push($desserts, $row);
		// }
		$res['desserts'] = $desserts;
		$res['message'] = "Desserts read successfully!";
	} catch (Exception $e) {
		$res['error'] = true;
		$msg = $e->getMessage();
		$res['message'] = "Desserts read failed! {$msg}";
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
		$msg = $e->getMessage();
		$res['message'] = "Dessert delete failed! {$msg}";
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
		$msg = $e->getMessage();
		$res['message'] = "Dessert update failed! {$msg}";
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
		$msg = $e->getMessage();
		$res['message'] = "Dessert added failed! {$msg}";
	}

	$prepared -> close();
}

$conn -> close();

echo(json_encode($res, JSON_UNESCAPED_UNICODE));
die();

 ?>