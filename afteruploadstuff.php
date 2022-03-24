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
