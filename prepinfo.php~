<?php

$vidPath = $argv[1];
if (! file_exists($vidPath)) die("Video $vidPath not found.\n");

if (file_exists($vidPath."-metadata-self")) {
	rename($vidPath, $vidPath."-dup");
	die ("Video $vidPath already uploaded.\n");
}
sleep(3);
require_once 'info.php';


// echo $vidPath."...";

// Note that this example creates an unversioned service object.
// You do not need to specify a version number to upload content
// since the upload behavior is the same for all API versions.

// create a new VideoEntry object
$myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();


// create a new Zend_Gdata_App_MediaFileSource object
// /home/tomh/iphonepics/tl/2013/03/16/13/20130316_13.mp4
//$filesource = $yt->newMediaFileSource($vidPath);
//$filesource->setContentType('video/mpeg4');
// set slug header
$slug = basename($vidPath);
//$filesource->setSlug($slug);

$vyear = substr($slug,0,4);
$vmonth= substr($slug,4,2);
$vday  = substr($slug,6,2);
$vhour = substr($slug,9,2);

// add the filesource to the video entry
//$myVideoEntry->setMediaSource($filesource);

if ($vhour == "fu") {
	$myVideoEntry->setVideoTitle("HD Webcam Timelapse $vmonth-$vday-$vyear - day");
	$myVideoEntry->setVideoDescription("Timelapse Video from HD Webcam of Milwaukee Inner Harbor taken on $vmonth-$vday-$vyear [full day].\n\nhttp://www.freshwater.uwm.edu/");
} else {
	$myVideoEntry->setVideoTitle("HD Webcam Timelapse $vmonth-$vday-$vyear $vhour:00-$vhour:59");
	$myVideoEntry->setVideoDescription("Timelapse Video from HD Webcam of Milwaukee Inner Harbor taken on $vmonth-$vday-$vyear from $vhour:00 to $vhour:59.\n\nhttp://www.freshwater.uwm.edu/");
}
// The category must be a valid YouTube category!
$myVideoEntry->setVideoCategory('Education');
$myVideoEntry->setVideoRecorded("$vyear-$vmonth-$vday");

if ($vhour == "fu") {
	$myVideoEntry->setUpdated(new Zend_Gdata_App_Extension_Published("$vyear-$vmonth-$vday"."T"."23:59:30-05:00"));
} else {
	$myVideoEntry->setUpdated(new Zend_Gdata_App_Extension_Published("$vyear-$vmonth-$vday"."T"."$vhour:59:00-05:00"));
}

// Set keywords. Please note that this must be a comma-separated string
// and that individual keywords cannot contain whitespace
$myVideoEntry->SetVideoTags('UWM,SFS,Freshwater,Sciences,webcam,Milwaukee,harbor,ships,birds,waterfowl,boats,boatnerd');

// set some developer tags -- this is optional
// (see Searching by Developer Tags for more details)
//$myVideoEntry->setVideoDeveloperTags(array('mydevtag', 'anotherdevtag'));

// set the video's location -- this is also optional
$yt->registerPackage('Zend_Gdata_Geo');
$yt->registerPackage('Zend_Gdata_Geo_Extension');
$where = $yt->newGeoRssWhere();
$position = $yt->newGmlPos('43.0178034279542 -87.90360689163208');
$where->point = $yt->newGmlPoint($position);
$myVideoEntry->setWhere($where);

// upload URI for the currently authenticated user
$uploadUrl = 'https://uploads.gdata.youtube.com/resumable/feeds/api/users/default/uploads';
echo "trying to upload...\n";
// try to upload the video, catching a Zend_Gdata_App_HttpException, 
// if available, or just a regular Zend_Gdata_App_Exception otherwise
$uploadfailed = FALSE;
try {
  $newEntry = $yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry');
} catch (Zend_Gdata_App_HttpException $httpException) {
    echo "===\n";
  echo $vidPath.": ".$httpException->getRawResponseBody();
    print_r($httpException);
  $uploadfailed = TRUE;
    echo "===\n";
} catch (Zend_Gdata_App_Exception $e) {
    echo "===\n";
    echo $vidPath.": ".$e->getMessage();
    $uploadfailed = TRUE;
    echo "===\n";
}


if (!$uploadfailed) {
	//$newEntry->setUpdated(new Zend_Gdata_App_Extension_Published("$vyear-$vmonth-$vday"."T"."$vhour:59:00-05:00"));
	//$newEntry->save();
	// rename($vidPath,$vidPath."-saved");
	//file_put_contents($vidPath."-metadata",print_r($newEntry,TRUE));
	file_put_contents($vidPath."-metadata-self",$newEntry->getVideoWatchPageUrl());
	$thumbs = $newEntry->getVideoThumbnails();
	// wait until thumbnail URL works
	$retries = 600;
	while (!($imgsrc = @imagecreatefromjpeg($thumbs[0]['url']))) {
		$retries--;
		if ($retries == 0) break;
		sleep(5);
	}
	if (!$imgsrc) {
		// fouled up
		echo "$vidPath: youtube has not accepted the video after 5 minutes. bad news!\n";
	} else {
		file_put_contents($vidPath."-metadata-thumb",$thumbs[0]['url']);
		// remove local copy
		unlink($vidPath);
		// echo "done.\n";
	}
} else {
	echo "$vidPath: uploading error!\n";
	// leave it, retry next hour
	//rename($vidPath,$vidPath."-ulfailed");
	
}
