<?php


// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Test-Header");

// include database and object files
include_once '../config/config.php';
include_once '../objects/task.php';


//var_dump($_SERVER);

// set response code - 200 OK
//	http_response_code(200);
	
	// show task data in json format
//	echo(json_encode($_SERVER));

//exit;





// instantiate database and task object
$database = new Database();
$dbConn = $database->getConnection();
  
// initialize object
$task = new Task($dbConn);

// check which assigned user we are getting
$assignedUserId = (isset($_GET["assignedUserId"]) && is_numeric($_GET["assignedUserId"])) ? $_GET["assignedUserId"] : false;

  
// query tasks
$stmt = $task->read($assignedUserId);
$num = $stmt->rowCount();

// check if more than 0 records found
if($num>0) {
	// tasks array
	$tasksArray = array();
	//$tasksArray['items'] = array();
	
	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

		$taskItem = array(
			'ID' => $row['ID'],
			'title' => $row['title'],
			'description' => $row['description'],
			'assignedUserNickname' => $row['assigned_user_nickname'],
			'creatorUserNickname' => $row['creator_user_nickname'],
			'projectName' => $row['project_name'],
			'companyName' => $row['company_name'],
			'dueDate' => $row['due_date'],
			'notesCount' => $row['notes_count']

		);
		
		array_push($tasksArray, $taskItem);
		
	}
	
	// set response code - 200 OK
	http_response_code(200);
	
	// show task data in json format
	echo(json_encode($tasksArray));

}
else {
	// set response code - 404 Not found
	//http_response_code(404);
	//TODO figure out if we should do 200 or 404 if none found
	http_response_code(200);

	// tell the user no tasks found
	echo json_encode(
		array("message" => "No tasks found.")
	);
}
