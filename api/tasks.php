<?php

require_once 'config/config.php';


//ensure user is authenticated
require_once 'auth/verify-jwt.php';


// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



$operation = false;
$params = array();

if(isset($_REQUEST['operation'])) {
    $operation = $_REQUEST['operation'];
}
else {
    echo("No operation set");
    exit;
}

if(isset($_REQUEST['params'])) {
    $params = json_decode($_REQUEST['params'], true);

    if(!is_array($params)) {
        echo("Invalid params");
        exit;
    }
}
else {
    //all good to continue with params as empty array if it wasn't provided
    $params = array();
}


if($operation == 'getTasks') {

    $tasks = Tasks::getTasks($params);

    // set response code - 200 OK
	http_response_code(200);
	
	// show task data in json format
	echo(json_encode($tasks));



}
elseif($operation == 'updateTask') {

}
else {
    echo("Invalid operation");
    exit;

}

class Tasks {

    public static function getTasks($params) {

        

        // instantiate database and task object
        $database = new Database();
        $dbConn = $database->getConnection();




        // select all query
        $query = '';
        
        
        $query .= "SELECT c.company_name,p.project_name, u1.nickname as assigned_user_nickname,u2.nickname as creator_user_nickname, COUNT(n.ID) as notes_count, t.* FROM tasks t LEFT JOIN users u1 ON u1.ID=t.assigned_user_id LEFT JOIN users u2 ON u2.ID=t.creator_user_id INNER JOIN projects p ON t.related_table_id=p.ID INNER JOIN companies c ON c.ID=p.company_id LEFT JOIN notes n ON t.ID=n.related_table_id AND n.related_table='tasks' WHERE t.related_table='projects' AND t.status = 'not_started'";

        if(isset($params['assignedUserId']) && is_numeric($params['assignedUserId'])) {
            $query .= " AND t.assigned_user_id=" . $params['assignedUserId'];
        }

        $query .= " GROUP BY t.ID";
        $query .= " ORDER BY t.due_date ASC";
        //$query .= " LIMIT 1000, 500";
        
        // prepare query statement
        $stmt = $dbConn->prepare($query);
        
        // execute query
        $stmt->execute();

        $numRows = $stmt->rowCount();


        $tasksArray = array();

        if($numRows > 0) {
            
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
        
        }

        
        return $tasksArray;

    }

}

?>