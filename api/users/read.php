<?php


// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Test-Header");

// include database and object files
include_once '../config/config.php';
include_once '../objects/user.php';


// instantiate database and task object
$database = new Database();
$dbConn = $database->getConnection();
  
// initialize object
$user = new User($dbConn);

// check if it's only active users we're getting
$activeUsersOnly = (isset($_GET["activeUsersOnly"]) && $_GET["activeUsersOnly"] == 'N') ? false : true;

  
// query users
$stmt = $user->read($activeUsersOnly);
$num = $stmt->rowCount();

// check if more than 0 records found
if($num>0) {
	
    // users array
	$usersArray = array();
	$usersArray['items'] = array();
	
	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		
		$userItem = array(
			'ID' => $row['ID'],
			'username' => $row['username'],
			'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
			'nickname' => $row['nickname']
		);
		
		array_push($usersArray['items'], $userItem);
		
	}
	
	// set response code - 200 OK
	http_response_code(200);
	
	// show task data in json format
	echo(json_encode($usersArray));

}
else {
	// set response code - 404 Not found
	http_response_code(404);

	// tell the user no tasks found
	echo json_encode(
		array("message" => "No users found.")
	);
}

?>