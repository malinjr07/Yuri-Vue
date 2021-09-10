<?php

require_once 'config/config.php';

//ensure user is authenticated
require_once 'auth/verify-jwt.php';


// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//check if it was GET, POST (insert), PUT (update), DELETE
$requestMethod = $_SERVER['REQUEST_METHOD'];

//get body of request (use "true" to get as assoc array instead of object)
$requestBody = json_decode(file_get_contents("php://input"), true);

//check if the resourceId was passed in the URL. We do this so the endpoint URL follows proper REST protocol, however we should also include this ID in the request body.
$resourceId = false;

if (isset($_GET['ID']) && is_numeric($_GET['ID'])) {
    $resourceId = $_GET['ID'];
}


//check the request method. GET = select, POST = insert, PUT = update, DELETE = delete
switch ($requestMethod) {
    case 'GET': {
            echo ("GET request");

            $users = Users::getUsers($requestBody);

            http_response_code(200);
            echo (json_encode($users));
            exit;
        }
    case 'POST': {
            echo ("POST request");
            exit;
        }
    case 'PUT': {



            if ($updateResult = Users::updateUser($requestBody)) {

                http_response_code(200);

                //TODO: change to json response
                echo ("SUCCESS");
            } else {

                //TODO: check what the correct response code should be for an error.
                http_response_code(200);


                //TODO: change to json response
                echo ("ERROR");
            }

            echo ("PUT request");
            exit;
        }
    case 'DELETE': {
            echo ("DELETE request");
            exit;
        }
    default: {
            echo ("Invalid Request Method. Must be GET, POST, PUT, or DELETE.");
            exit;
        }
}






//if(isset($requestBody['operation']) && $requestBody['operation'] == 'getUsers') {
//    http_response_code(200);
//    echo("Get users");
//    exit;
//}
//else {
//    echo("Operation not found");
//    exit;
//}



class Users
{
    public static function getUsers($params)
    {

        // instantiate database and task object
        $database = new Database();
        $dbConn = $database->getConnection();

        $query = '';
        $query .= "SELECT * FROM users";

        //only return active users (unless we specifically specify we want to also return inactive users)
        if (!(isset($params['activeUsersOnly']) && $params['activeUsersOnly'] == 'N')) {
            $query .= " WHERE active='Y'";
        }

        $query .= " ORDER BY nickname ASC";

        // prepare query statement
        $stmt = $dbConn->prepare($query);

        // execute query
        $stmt->execute();

        $numRows = $stmt->rowCount();

        $usersArray = array();

        if ($numRows > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $userItem = $row;
                array_push($usersArray, $userItem);
            }
        }

        return $usersArray;
    }

    public static function updateUser($params)
    {

        //to this function we pass $params, which is from the $requestBody of the request.
        // this will only contain fields which need to be updated.

        //check that at least the ID is present
        if (!isset($params['ID']) && !is_numeric($params['ID'])) {
            echo ("Can't update: No user id found");
            exit;
        }

        $query = "";

        $query .= "UPDATE users SET ID=:ID";

        if (isset($params['first_name'])) {
            $query .= ", first_name=:first_name";
        }

        if (isset($params['last_name'])) {
            $query .= ", last_name=:last_name";
        }

        if (isset($params['nickname'])) {
            $query .= ", nickname=:nickname";
        }

        if (isset($params['mobile'])) {
            $query .= ", mobile=:mobile";
        }

        $query .= "WHERE ID=:ID";

        $stmt = $this->conn->prepare($query);

        // TODO: sanitise data


        // bind new values
        $stmt->bindParam(':ID', $this->ID);

        if (isset($params['first_name'])) {
            $stmt->bindParam(':first_name', $params['first_name']);
        }

        if (isset($params['last_name'])) {
            $stmt->bindParam(':last_name', $params['last_name']);
        }

        if (isset($params['nickname'])) {
            $stmt->bindParam(':nickname', $params['nickname']);
        }

        if (isset($params['mobile'])) {
            $stmt->bindParam(':mobile', $params['mobile']);
        }


        // execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
