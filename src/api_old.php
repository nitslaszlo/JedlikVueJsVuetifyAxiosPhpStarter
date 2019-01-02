<?php 
header('Access-Control-Allow-Origin: *');
// header('Content-type: application/json');
// data have to fetched from local server 
$mysql_host = 'localhost'; 
  
// user name is root 
$mysql_user = 'root';

if (!@mysql_connect ($mysql_host, $mysql_user)) 
{ 
    die('Cannot connect to server!'); 
} 
else
{ 
    // database name is server 
    if (@mysql_select_db('filmek')) 
    { 
        echo "Connection Success!"; 
    } 
    else
    { 
        die('Cannot connect to database!'); 
    } 
} 

// $conn = new mysqli("localhost", "root", "", "filmek");
// if ($conn->connect_error) {
// 	die("Database connection established Failed..");
// } 
$res = array('error' => false);
$action = 'read';
if (isset($_GET['action'])) {
	$action = $_GET['action'];
}
if ($action == 'read') {
	// $result = $conn->query("SELECT * FROM `film`");
	// $films = array();
	// while ($row = $result->fetch_assoc()){
	// 	array_push($films, $row);
	// 	echo($row);
	// }
	// $res['films'] = $films;
	// echo[$films[0].id];
	// sql query to fetch all the data 
	$query = "SELECT * FROM `film`"; 
	if ($is_query_run = mysql_query($query)) 
	{ 
		$films = array();
    	while ($query_executed = mysql_fetch_assoc ($is_query_run)) 
    	{ 
			
			// echo "new row";
			array_push($films, $query_executed);
		}
		$res['films'] = $films;
		// print_r($films);
	} 
	else
	{ 
   		 echo "Error in execution"; 
	}	 
}
if ($action == 'create') {
	$username = $_POST['username'];
	$email = $_POST['email'];
	$mobile = $_POST['mobile'];
	$result = $conn->query("INSERT INTO `users` (`username`, `email`, `mobile`) VALUES ('$username', '$email', '$mobile') ");
	if ($result) {
		$res['message'] = "User Added successfully";
	} else{
		$res['error'] = true;
		$res['message'] = "Insert User fail";
	}
}
if ($action == 'update') {
	$id = $_POST['id'];
	$username = $_POST['username'];
	$email = $_POST['email'];
	$mobile = $_POST['mobile'];
	$result = $conn->query("UPDATE `users` SET `username` = '$username', `email` = '$email', `mobile` = '$mobile'WHERE `id` = '$id'");
	if ($result) {
		$res['message'] = "User Updated successfully";
	} else{
		$res['error'] = true;
		$res['message'] = "User Update failed";
	}
}
if ($action == 'delete') {
	$id = $_POST['id'];
	$username = $_POST['username'];
	$email = $_POST['email'];
	$mobile = $_POST['mobile'];
	$result = $conn->query("DELETE FROM `users` WHERE `id` = '$id'");
	if ($result) {
		$res['message'] = "User deleted successfully";
	} else{
		$res['error'] = true;
		$res['message'] = "User delete failed";
	}
}
$conn -> close();
header("Content-type: application/json");
echo json_encode($res);
die();
?>