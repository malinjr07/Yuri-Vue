<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
// include database and object files
include_once '../config/config.php';
include_once '../objects/note.php';
  
// get database connection
$database = new Database();
$dbConn = $database->getConnection();
  
// prepare task object
$note = new Note($dbConn);
  
// get id of task to be edited
$data = json_decode(file_get_contents("php://input"));

  
// set Task property values
$note->relatedTable = $data->relatedTable;
$note->relatedTableId = $data->relatedTableId;
$note->creatorUserId = $data->creatorUserId;
$note->noteText = $data->noteText;
$note->noteAdditionalText = $data->noteAdditionalText;
  
// create the note
if($note->create()){
  
	// set response code - 200 ok
	http_response_code(200);
  
	// tell the user
	echo(json_encode(array("message" => "Note was added.")));
}
// if unable to add the note, tell the user
else{
  
    // set response code - 503 service unavailable
    //http_response_code(503);
    http_response_code(200);
  
    // tell the user
    echo(json_encode(array("message" => "Unable to add note.")));
}
?>