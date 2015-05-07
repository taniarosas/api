<?php

//Configuration for our PHP Server
set_time_limit(0);
ini_set('default_socket_timeout', 300);

session_start();

//Make Constant using define.
define('clientID', '6dea88c7c85f453eb40b4293a5040ac6');
define('clientSecret', '117314e5478b4250a1454dc6ad726f2f');
define('redirectURI', 'http://localhost/api/index.php');
define('ImageDirectory', 'pics/');

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	
	<a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI; ?>&response_type=code">Login</a>
	
</body>
</html>
