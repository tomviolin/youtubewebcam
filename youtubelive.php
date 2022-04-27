<?php
$API_KEY='AIzaSyC2TSaXvxbu0GXKr_NwMz9P_Fvxy0xNBPA';
$USER=getenv('USER');
chdir("/home/tomh/microservices/youtubewebcam");

$key = file_get_contents('the_key.txt');
 
//set_include_path($_SERVER['DOCUMENT_ROOT'] . '/path-to-your-director/');
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/tomh/microservices/youtubewebcam/google-api-php-client/');
require_once 'vendor/autoload.php';
require_once 'src/Client.php';
//require_once 'Google/Service/YouTube.php';
require_once 'vendor/google/apiclient-services/src/YouTube.php';


//$client_id = "86690683607-uh7g2jr3ee1ktidvrc7chir0ftec5ebq.apps.googleusercontent.com";


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
 
	// =====  START OF VIDEO STREAM CODE ===== 
 
    // Create an object for the liveBroadcast resource's snippet. Specify values
    // for the snippet's title, scheduled start time, and scheduled end time.
    $broadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
    $broadcastSnippet->setTitle('New Broadcast');
    $broadcastSnippet->setScheduledStartTime('2022-04-29T00:00:00.000Z');
    $broadcastSnippet->setScheduledEndTime('2022-04-30T00:00:00.000Z');

    // Create an object for the liveBroadcast resource's status, and set the
    // broadcast's status to "private".
    $status = new Google_Service_YouTube_LiveBroadcastStatus();
    $status->setPrivacyStatus('private');

    // Create the API request that inserts the liveBroadcast resource.
    $broadcastInsert = new Google_Service_YouTube_LiveBroadcast();
    $broadcastInsert->setSnippet($broadcastSnippet);
    $broadcastInsert->setStatus($status);
    $broadcastInsert->setKind('youtube#liveBroadcast');

    // Execute the request and return an object that contains information
    // about the new broadcast.
    $broadcastsResponse = $youtube->liveBroadcasts->insert('snippet,status',
        $broadcastInsert, array());

    // Create an object for the liveStream resource's snippet. Specify a value
    // for the snippet's title.
    $streamSnippet = new Google_Service_YouTube_LiveStreamSnippet();
    $streamSnippet->setTitle('New Stream');

    // Create an object for content distribution network details for the live
    // stream and specify the stream's format and ingestion type.
    $cdn = new Google_Service_YouTube_CdnSettings();
    $cdn->setFormat("1080p");
    $cdn->setResolution("variable");
    $cdn->setFrameRate("variable");
    $cdn->setIngestionType('rtmp');

    // Create the API request that inserts the liveStream resource.
    $streamInsert = new Google_Service_YouTube_LiveStream();
    $streamInsert->setSnippet($streamSnippet);
    $streamInsert->setCdn($cdn);
    $streamInsert->setKind('youtube#liveStream');

    // Execute the request and return an object that contains information
    // about the new stream.
    $streamsResponse = $youtube->liveStreams->insert('snippet,cdn',
        $streamInsert, array());

    // Bind the broadcast to the live stream.
    $bindBroadcastResponse = $youtube->liveBroadcasts->bind(
        $broadcastsResponse['id'],'id,contentDetails',
        array(
            'streamId' => $streamsResponse['id'],
        ));

    $htmlBody = "";
    $htmlBody .= "<h3>Added Broadcast</h3><ul>";
    $htmlBody .= sprintf('<li>%s published at %s (%s)</li>',
        $broadcastsResponse['snippet']['title'],
        $broadcastsResponse['snippet']['publishedAt'],
        $broadcastsResponse['id']);
    $htmlBody .= '</ul>';

    $htmlBody .= "<h3>Added Stream</h3><ul>";
    $htmlBody .= sprintf('<li>%s (%s)</li>',
        $streamsResponse['snippet']['title'],
        $streamsResponse['id']);
    $htmlBody .= '</ul>';

    $htmlBody .= "<h3>Bound Broadcast</h3><ul>";
    $htmlBody .= sprintf('<li>Broadcast (%s) was bound to stream (%s).</li>',
        $bindBroadcastResponse['id'],
        $bindBroadcastResponse['contentDetails']['boundStreamId']);
    $htmlBody .= '</ul>';

    	echo "<html><body>";
	echo $htmlBody;
	echo "</body></html>";



        // If you want to make other calls after the file upload, set setDefer back to false
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
