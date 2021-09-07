<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


if ($_SERVER["REQUEST_METHOD"] === "OPTIONS")
    http_response_code(200);
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(200);
    echo (json_encode(array("message" => "Request method not allowed.")));
    exit;
}


include_once '../config/config.php';
include_once '../objects/task.php';


if (!isset($_POST["ID"]) || !isset($_POST["dueDate"])) {
    http_response_code(422);
    echo (json_encode(array("message" => "Required values not found")));
    exit;
}

$database = new Database();
$dbConn = $database->getConnection();

$task = new Task($dbConn);
$task->ID = $_POST["ID"];
$task->dueDate = $_POST["dueDate"];

if ($task->update_dates()) {
    http_response_code(200);
    echo (json_encode(array("message" => "Task was updated.")));
} else {
    http_response_code(503);
    echo (json_encode(array("message" => "Unable to update task.")));
}
