<?php

$vidPath = $argv[1];
if (! file_exists($vidPath)) die("Video $vidPath not found.\n");

if (file_exists($vidPath."-metadata-self")) {
	rename($vidPath, $vidPath."-dup");
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
$videoCategory='Education';
$videoRecorded("$vyear-$vmonth-$vday");

// Set keywords. Please note that this must be a comma-separated string
// and that individual keywords cannot contain whitespace
$videoTags='UWM,SFS,Freshwater,Sciences,webcam,Milwaukee,harbor,ships,birds,waterfowl,boats,boatnerd';

