<?php

require_once 'config/config.php';


// ensure user is authenticated
require_once 'auth/verify-jwt.php';


// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Test-Header");


$operation = false;
$params = array();

if (isset($_REQUEST['operation'])) {
    $operation = $_REQUEST['operation'];
} else {
    echo ("No operation set");
    exit;
}

if (isset($_REQUEST['params'])) {
    $params = json_decode($_REQUEST['params'], true);

    if (!is_array($params)) {
        echo ("Invalid params");
        exit;
    }
} else {
    // all good to continue with params as empty array if it wasn't provided
    $params = array();
}


if ($operation == 'getTasks') {
    $tasks = Tasks::getTasks($params);

    http_response_code(200);
    echo (json_encode($tasks));
} elseif ($operation == 'updateTask') {
    $tasks = Tasks::updateTask($params);

    http_response_code(200);
    echo (json_encode($tasks));
} else {
    echo ("Invalid operation");
    exit;
}

class Tasks
{
    public static function getTasks($params)
    {
        // instantiate database and task object
        $database = new Database();
        $dbConn = $database->getConnection();

        // select all query
        $query = '';

        $query .= "SELECT c.company_name,p.project_name, u1.nickname as assigned_user_nickname,u2.nickname as creator_user_nickname, COUNT(n.ID) as notes_count, t.* FROM tasks t LEFT JOIN users u1 ON u1.ID=t.assigned_user_id LEFT JOIN users u2 ON u2.ID=t.creator_user_id INNER JOIN projects p ON t.related_table_id=p.ID INNER JOIN companies c ON c.ID=p.company_id LEFT JOIN notes n ON t.ID=n.related_table_id AND n.related_table='tasks' WHERE t.related_table='projects' AND t.status = 'not_started'";

        if (isset($params['assignedUserId']) && is_numeric($params['assignedUserId']))
            $query .= " AND t.assigned_user_id=" . $params['assignedUserId'];

        $query .= " GROUP BY t.ID";
        $query .= " ORDER BY t.due_date ASC";
        //$query .= " LIMIT 1000, 500";

        // prepare query statement
        $stmt = $dbConn->prepare($query);

        // execute query
        $stmt->execute();

        $numRows = $stmt->rowCount();

        $tasksArray = array();

        if ($numRows > 0) {
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

    public static function updateTask($params)
    {
        if (!isset($params['ID']) && !is_numeric($params['ID'])) {
            echo ("Can't update: No task found");
            exit;
        }

        $query = "";
        $query .= "UPDATE tasks SET ID=:ID";

        if (isset($params['title']))
            $query .= ", title=:title";

        if (isset($params['description']))
            $query .= ", description=:description";

        if (isset($params['due_date']))
            $query .= ", due_date=:due_date";

        $query .= "WHERE ID=:ID";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $p)
            $p = htmlspecialchars(strip_tags($p));

        $stmt->bindParam(':ID', $this->ID);

        if (isset($params['title']))
            $stmt->bindParam(':title', $params['title']);

        if (isset($params['description']))
            $stmt->bindParam(':description', $params['description']);

        if (isset($params['due_date']))
            $stmt->bindParam(':due_date', $params['due_date']);

        if ($stmt->execute())
            return true;

        return false;
    }
}
