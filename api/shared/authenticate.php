<?php

//check authentication
if(!(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_USER'] == 'excitemedia' && $_SERVER['PHP_AUTH_PW'] == 'af9F4244sfljv4724FDkjsdfj' )) {
	
	echo("Not Authorised!!");
	
	// set response code - 401 Unauthorized
	//http_response_code(401);

	// tell the user no tasks found
	//echo json_encode(
	//	array("message" => "API User Not Authorised.")
	//);
	exit;
}
else {
	//echo("YESS auth");
	//exit;
}


?>