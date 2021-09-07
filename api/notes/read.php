<?php


// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Test-Header");

// include database and object files
include_once '../config/config.php';
include_once '../objects/note.php';


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
$note = new Note($dbConn);

// check which related table/id we are getting

$relatedTable = false;

if(isset($_GET["relatedTable"]) && ($_GET["relatedTable"] == 'tasks' || $_GET['relatedTable'] == 'projects' || $_GET['relatedTable'] == 'leads' || $_GET['relatedTable'] == 'companies')) {
	$relatedTable = $_GET["relatedTable"];
}

$relatedTableId = false;

if(isset($_GET["relatedTableId"]) && is_numeric($_GET["relatedTableId"])) {
	$relatedTableId =  $_GET["relatedTableId"];
}

if(!$relatedTable || !$relatedTableId) {
	// set response code - 404 Not found
	http_response_code(404);
	echo("Must specify related table and related table id");
	exit;
}



  
// query notes
$stmt = $note->read($relatedTable, $relatedTableId);
$num = $stmt->rowCount();

// check if more than 0 records found
if($num>0) {
	// notes array
	$notesArray = array();
	
	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

		$noteItem = array(
			'ID' => $row['ID'],
			'noteText' => $row['note_text'],
			'noteAdditionalText' => $row['note_additional_text'],
			'noteDateTime' => $row['note_datetime'],
			'relatedTable' => $row['related_table'],
			'relatedTableId' => $row['related_table_id'],
			'creatorUserId' => $row['creator_user_id'],
			'creatorUserNickname' => $row['creator_user_nickname']

		);
		
		array_push($notesArray, $noteItem);
		
	}
	
	// set response code - 200 OK
	http_response_code(200);
	
	// show task data in json format
	echo(json_encode($notesArray));

}
else {
	// set response code - 404 Not found
	//http_response_code(404);
	//TODO figure out if we should do 200 or 404 if none found
	http_response_code(200);

	// tell the user no tasks found
	echo json_encode(
		//array("message" => "No notes found.")
		array()
	);
}

?>