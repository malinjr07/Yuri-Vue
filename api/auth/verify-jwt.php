<?php


require_once 'php-jwt/BeforeValidException.php';
require_once 'php-jwt/ExpiredException.php';
require_once 'php-jwt/SignatureInvalidException.php';
require_once 'php-jwt/JWT.php';
require_once 'php-jwt/JWK.php';

use \Firebase\JWT\JWT;


$secretKey = JWT_SECRET;
$jwt = null;

//$data = json_decode(file_get_contents("php://input"));

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];


$arr = explode(" ", $authHeader);




$jwt = $arr[1];

if ($jwt) {

	try {
		$decoded = JWT::decode($jwt, $secretKey, array('HS256'));

		// Access is granted, so we allow the script to proceed.

		//echo json_encode(array(
		//    "message" => "Access granted:",
		//    "error" => $e->getMessage()
		//));
		//exit;

	} catch (Exception $e) {
		if ($e === "Expired token") {
			//
		}

		http_response_code(401);

		echo json_encode(array(
			"message" => "Access denied.",
			"secret" => $secretKey,
			"jwt" => $jwt,
			"error" => $e->getMessage()
		));
		exit;
	}
} else {
	echo ("No JWT found.");
	exit;
}






//check authentication
//if(!(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_USER'] == 'excitemedia' && $_SERVER['PHP_AUTH_PW'] == 'af9F4244sfljv4724FDkjsdfj' )) {
	
//	echo("Not Authorised!!");
	
	// set response code - 401 Unauthorized
	//http_response_code(401);

	// tell the user no tasks found
	//echo json_encode(
	//	array("message" => "API User Not Authorised.")
	//);
//	exit;
//}
//else {
	//echo("YESS auth");
	//exit;
//}
