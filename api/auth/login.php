<?php

require_once '../config/config.php';

require_once 'php-jwt/BeforeValidException.php';
require_once 'php-jwt/ExpiredException.php';
require_once 'php-jwt/SignatureInvalidException.php';
require_once 'php-jwt/JWT.php';
require_once 'php-jwt/JWK.php';

use \Firebase\JWT\JWT;


// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$database = new Database();
$dbConn = $database->getConnection();


$username = '';
$password = '';


//$requestData = json_decode(file_get_contents("php://input"));

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    http_response_code(401);
    echo (json_encode(array("message" => "No username or password provied.")));
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];


$query = "SELECT * FROM users WHERE username=:username AND password=:password AND active='Y' LIMIT 0,1";

$stmt = $dbConn->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);
$stmt->execute();

$numRows = $stmt->rowCount();

if ($numRows > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ID = $row['ID'];
    $nickname = $row['nickname'];
    $firstName = $row['first_name'];
    $lastName = $row['last_name'];
    $email = $row['email'];
    $mobile = $row['mobile'];
    $userType = $row['user_type'];


    $secretKey = JWT_SECRET;
    $issuerClaim = "tempo-server"; // this can be the servername
    $audienceClaim = "tempo-vue-frontend";
    $issuedatClaim = time(); // issued at
    $notbeforeClaim = $issuedatClaim + 10; //not before in seconds
    $expireClaim = $issuedatClaim + 172800; // expire time in seconds
    $token = array(
        "iss" => $issuerClaim,
        "aud" => $audienceClaim,
        "iat" => $issuedatClaim,
        "nbf" => $notbeforeClaim,
        "exp" => $expireClaim,
        "data" => array(
            "ID" => $ID,
            "nickname" => $nickname,
            "firstName" => $firstName,
            "lastName" => $lastName,
            "email" => $email,
            "mobile" => $mobile,
            "userType" => $userType
        )
    );

    http_response_code(200);

    $jwt = JWT::encode($token, $secretKey);
    echo json_encode(
        array(
            "message" => "Successful login.",
            "jwt" => $jwt,
            "email" => $email,
            "expireAt" => $expireClaim
        )
    );
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Login failed."));
}













//$key = "example_key";
//$token = array(
//   "iss" => "http://example.org",
//   "aud" => "http://example.com",
//   "iat" => 1356999524,
//   "nbf" => 1357000000
//);

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */
//$jwt = JWT::encode($token, $key);
//$decoded = JWT::decode($jwt, $key, array('HS256'));

//print_r($decoded);

/*
 NOTE: This will now be an object instead of an associative array. To get
 an associative array, you will need to cast it as such:
*/

//$decoded_array = (array) $decoded;

/**
 * You can add a leeway to account for when there is a clock skew times   between
 * the signing and verifying servers. It is recommended that this leeway should
 * not be bigger than a few minutes.
 *
 * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
 */
//JWT::$leeway = 60; // $leeway in seconds
//$decoded = JWT::decode($jwt, $key, array('HS256'));
