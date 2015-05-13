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


//Function that is going to connect to Instagram.
function connectToInstagram($url){
	$ch = curl_init();

	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => 2,
		));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
}

//Function to get UserID cause userName doesn't allow us to get pictures!
function getUserID($userName){
	$url = 'http://api.instagram.com/v1/users/search?q='.$userName. '&client_id='.clientID;
	$instagramInfo = connectToInstagram($url);
	$results = json_decode($instagramInfo, true);

	return $results['data']['0']['id'];
}

//Function to print images onto screen
function printImages($userID){
	$url = 'http://api.instagram.com/v1/users/'.$userID.'/media/recent?client_id='.clientID.'&count=5';
	$instagramInfo = connectToInstagram($url);
	$results = json_decode($instagramInfo, true);
	//Parse through the information one by one
	foreach ($results['data'] as $items) {
		$image_url = $items['images']['low_resolution']['url'];//going to go through all of my results and give myself back the URL of those pictures 
		//because we want to have it in the PHP Server.
		echo '<img src=" '.$image_url.'"/>br/>';
	}
}

if (isset($_GET['code'])){
	$code = ($_GET['code']);
	$url = 'https://api.instagram.com/oauth/access_token';
	$access_token_settings = array('client_id' => clientID,
		                           'client_secret' => clientSecret,
		                           'grant_type' => 'authorization_code',
		                           'redirect_uri' => redirectURI,
		                           'code' => $code
		                           );
//cURL is what we use in PHP, its a library calls to other API's.
$curl =  curl_init($url);//Setting a cURL session and we put in $url because that's where we are getting the data from.
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings);//setting the POSTFIELDS to the array setup that we created.
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // setting it equal to 1 because we are getting strings back
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//but in live work-production we want to set this to true.

$result = curl_exec($curl);
curl_close($curl);

$results = json_decode($result, true);
$userName =  $results['user']['username'];

$userID = getUserID($username);

printImages($userID);
}
else{

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	
	<a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI; ?>&response_type=code">Login</a>
	<script type="js/main.js "></script>
	
</body>
</html>
<?php
}
?>
