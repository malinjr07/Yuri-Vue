<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../config/config.php';
include_once '../objects/task.php';


$database = new Database();
$dbConn = $database->getConnection();

$task = new Task($dbConn);
$data = json_decode(file_get_contents("php://input"));

$task->ID = $data->ID;
$task->title = $data->title;
$task->description = $data->description;

if ($task->update()) {
    http_response_code(200);
    echo (json_encode(array("message" => "Task was updated.")));
} else {
    http_response_code(503);
    echo (json_encode(array("message" => "Unable to update task.")));
}
