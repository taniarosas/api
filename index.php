<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="main.css"/>
	<link href='http://fonts.googleapis.com/css?family=Nova+Flat' rel='stylesheet' type='text/css'>
	<title></title>
</head>
<div id="page">

<?php
//Configuration for our PHP Server
set_time_limit(0);
ini_set('default_socket_timeout', 300);

session_start();

//Make Constant using define.
define('clientID', '6dea88c7c85f453eb40b4293a5040ac6');
define('clientSecret', '117314e5478b4250a1454dc6ad726f2f');
define('redirectURI', 'http://localhost/myapi/index.php');
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
	$url = 'https://api.instagram.com/v1/users/search?q=' . $userName . '&client_id=' .clientID;
	$instagramInfo = connectToInstagram($url);
	$results = json_decode($instagramInfo, true);

	return $results['data']['0']['id'];
}

//Function to print images onto screen
function printImages($userID)
{
	$url = 'https://api.instagram.com/v1/users/' . $userID . '/media/recent?client_id='.clientID . '&count=5';
	$instagramInfo = connectToInstagram($url);
	$results = json_decode($instagramInfo, true);

	//Parse through thet information one by one
	foreach($results['data'] as $items)
	 {
	 	$image_url = $items['images']['low_resolution']['url']; //go through all of the results and give back the url of those pictures because we want to save it in the php server.
	 	echo '<img src=" '. $image_url .' "/><br/>';
	 	savePictures($image_url);
	 }
}

//Function to save image to server
function savePictures($image_url){
	return $image_url .'<br>';
	$filename = basename($image_url); //the filename is what we are storing. basename is the PHP built that we are using to store $image_url
	echo $filename. '<br>';
	$destination = ImageDirectory . $filename; //making sure that the image doesn't exist in the storage.
	file_put_contents($destination, file_get_contents($image_url)); //gets and grabs an image file and stores it into our server
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

//echo $userName;

$userID = getUserID($userName);
//echo $userID;

printImages($userID);
}
else{

?>
</div>

<body>

<div class="buttonContainer">
	<h1>Welcome..</h1>
	<a class="button" href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI; ?>&response_type=code">Login</a>
</div>
	<script type="js/main.js "></script>
</body>
</html>
<?php
}
?>
