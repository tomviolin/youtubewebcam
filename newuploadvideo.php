<?php

$USER=getenv('USER');
chdir("/home/tomh/microservices/youtubewebcam");
$vidPath = $argv[1];

echo "Processing '$vidPath'...\n";

if (! file_exists($vidPath)) die("Video $vidPath not found.\n");
$mypid = getmypid();
if (substr($vidPath,strlen($vidPath)-4,4) == ".mp4") {
	// renaming game
	$base = substr($vidPath,0,strlen($vidPath)-4);
	$newname = $base . "-uploading$mypid.mp4";
	rename ($vidPath,$newname);
	echo "renamed to $newname.\n";
	if (! file_exists($newname)) {
		echo "someone else beat us to $vidPath.\n";
		exit(0);
	}
} else {
	echo "file must have .mp4 extension.\n";
	exit(1);
}

if (file_exists($vidPath."-metadata-self")) {
	rename($newname, $vidPath."-dup");
	die ("Video $vidPath already uploaded.\n");
}
sleep(3);
$slug = basename($vidPath);
//$filesource->setSlug($slug);

$vyear = substr($slug,0,4);
$vmonth= substr($slug,4,2);
$vday  = substr($slug,6,2);
$vhour = substr($slug,9,2);

if ($vhour == "fu") {
	$videoTitle = "HD Webcam Timelapse $vmonth-$vday-$vyear - day";
	$videoDescription = "Timelapse Video from HD Webcam of Milwaukee Inner Harbor taken on $vmonth-$vday-$vyear [full day].\n\nhttp://www.freshwater.uwm.edu/";
} else {
	$videoTitle="HD Webcam Timelapse $vmonth-$vday-$vyear $vhour:00-$vhour:59";
	$videoDescription="Timelapse Video from HD Webcam of Milwaukee Inner Harbor taken on $vmonth-$vday-$vyear from $vhour:00 to $vhour:59.\n\nhttp://www.freshwater.uwm.edu/";
}
// The category must be a valid YouTube category!
$videoCategory='27';
//$videoRecorded("$vyear-$vmonth-$vday");

// Set keywords. Please note that this must be a comma-separated string
// and that individual keywords cannot contain whitespace
$videoTags=array('UWM','SFS','Freshwater','Sciences','webcam','Milwaukee','harbor','ships','birds','waterfowl','boats','boatnerd');

//{"access_token":"ya29.hgIDhv9SBfTGPaIhcHrQkw_3LKm0En6_gHIwRGZfYUK1EkyiLS1tWPjxrPwx9ZDGo3t_","token_type":"Bearer","expires_in":3600,"refresh_token":"1\/aNvuAcwJ75-7VIO9uWqpXkJdjr28wIAUbpVRpT9ZDThIgOrJDtdun6zK6XiATCKT","created":1455314029}
$key = file_get_contents('the_key.txt');
 
//set_include_path($_SERVER['DOCUMENT_ROOT'] . '/path-to-your-director/');
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/tomh/microservices/youtubewebcam/google-api-php-client/src');
require_once 'Google/autoload.php';
require_once 'Google/Client.php';
require_once 'Google/Service/YouTube.php';


//$client_id = "86690683607-uh7g2jr3ee1ktidvrc7chir0ftec5ebq.apps.googleusercontent.com";


$client_id = "501385715411-09nvqnq2d95pqta0ngnnq419gbq0hoom.apps.googleusercontent.com";
$client_secret = "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";



$application_name = 'Freshwater Webcams'; 
$scope = array('https://www.googleapis.com/auth/youtube.upload', 'https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/youtubepartner');
         
$videoPath = $newname;
 
 
try{
    // Client init
    $client = new Google_Client();
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
 
 
 
        // Create a snipet with title, description, tags and category id
        $snippet = new Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($videoTitle);
        $snippet->setDescription($videoDescription);
        $snippet->setCategoryId($videoCategory);
        $snippet->setTags($videoTags);
 
        // Create a video status with privacy status. Options are "public", "private" and "unlisted".
        $status = new Google_Service_YouTube_VideoStatus();
        $status->setPrivacyStatus('public');
 
        // Create a YouTube video with snippet and status
        $video = new Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);
 
        // Size of each chunk of data in bytes. Setting it higher leads faster upload (less chunks,
        // for reliable connections). Setting it lower leads better recovery (fine-grained chunks)
        $chunkSizeBytes = 1 * 1024 * 1024;
 
        // Setting the defer flag to true tells the client to return a request which can be called
        // with ->execute(); instead of making the API call immediately.
        $client->setDefer(true);
 
        // Create a request for the API's videos.insert method to create and upload the video.
        $insertRequest = $youtube->videos->insert("status,snippet", $video);
 
        // Create a MediaFileUpload object for resumable uploads.
        $media = new Google_Http_MediaFileUpload(
            $client,
            $insertRequest,
            'video/*',
            null,
            true,
            $chunkSizeBytes
        );
        $media->setFileSize(filesize($videoPath));
 
 
        // Read the media file and upload it chunk by chunk.
        $status = false;
        $handle = fopen($videoPath, "rb");
        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }
 
        fclose($handle);
 
        /**
         * Video has successfully been uploaded, now let's perform some cleanup functions for this video
         */
        if ($status->status['uploadStatus'] == 'uploaded') {
            // Actions to perform for a successful upload

	    $videoID = $status['id'];
	    $videoThumbnailURL = "http://img.youtube.com/vi/$videoID/1.jpg";

	    file_put_contents($vidPath."-metadata-self","http://www.youtube.com/watch?v=$videoID&feature=youtube_gdata_player&vq=hd720&autoplay=1&showsearch=0&rel=0&showinfo=0");
	    file_put_contents($vidPath."-metadata-thumb",$videoThumbnailURL);
	    // remove local copy
	    rename ($newname, "/opt/webcam/tl/trash/".basename($vidPath));
	    echo "renamed".$newname." to /opt/webcam/tl/trash/".basename($vidPath)."\n";

	    file_put_contents("./data/lastvidid.txt", $videoID);
	    //unlink($vidPath);
	    // echo "done.\n";
	} else {
	    rename($newname,$vidPath);
	    echo "Video $newname:\nstatus = ".$status['id']."\n";
	    echo "file renamed back for retry.\n";
	}
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

if (file_exists($newname)) rename($newname,$vidPath);
