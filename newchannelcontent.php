<?php
$API_KEY='AIzaSyC2TSaXvxbu0GXKr_NwMz9P_Fvxy0xNBPA';
$USER=getenv('USER');
chdir("/home/tomh/microservices/youtubewebcam");

$key = file_get_contents('the_key.txt');
 
//set_include_path($_SERVER['DOCUMENT_ROOT'] . '/path-to-your-director/');
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/tomh/microservices/youtubewebcam/google-api-php-client/src');
require_once 'Google/autoload.php';
require_once 'Google/Client.php';
require_once 'Google/Service/YouTube.php';

$client_id = "501385715411-09nvqnq2d95pqta0ngnnq419gbq0hoom.apps.googleusercontent.com";
$client_secret = "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";



$application_name = 'Freshwater Webcams'; 
$scope = array('https://www.googleapis.com/auth/youtube.upload', 'https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/youtubepartner');
         
try{
    // Client init
    $client = new Google_Client();
    $client->setDeveloperKey($API_KEY);
    $client->setApplicationName($application_name);
    $client->setClientId($client_id);
    $client->setAccessType('offline');
    $client->setAccessToken($key);
    $client->setScopes($scope);
    $client->setClientSecret($client_secret);
 
    if ($client->getAccessToken()) {
 
        /**
         * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
         */
        if($client->isAccessTokenExpired()) {
            $newToken = json_decode($client->getAccessToken());
            $client->refreshToken($newToken->refresh_token);
            file_put_contents('the_key.txt', $client->getAccessToken());
        }
 
        $youtube = new Google_Service_YouTube($client);

	$queryParams = [
		'channelId' => 'UCUzdYii2yUMoBYVOUVsUVAA'
	];

	$response = $youtube->channelSections->listChannelSections(
		'contentDetails', $queryParams);
	print_r($response);

        $client->setDefer(true);
 
    } else{
        // @TODO Log error
        echo 'Problems creating the client';
    }
 
} catch(Google_Service_Exception $e) {
    print "Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage();
    print "Stack trace is ".$e->getTraceAsString();
}catch (Exception $e) {
    print "Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage();
    print "Stack trace is ".$e->getTraceAsString();
}

