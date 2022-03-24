<?

$max_results = 50;
for ($start_index = 1; $start_index < 500; $start_index += $max_results) {

	$feed = json_decode(file_get_contents("http://gdata.youtube.com/feeds/api/users/uwmfreshwaterwebcam/uploads?alt=json&start-index=$start_index&max-results=$max_results"));

	foreach ($feed->feed->entry as $entry) {
		// format: "HD Webcam Timelapse 03-27-2013 11:00-11:59"
		$title= $entry->title->{'$t'};
		$self = $entry->{'media$group'}->{'media$player'}[0]->url;
		$thumb = $entry->{'media$group'}->{'media$thumbnail'}[0]->url;

		$year = substr($title, 26,4);
		$month = substr($title, 20,2);
		$day  = substr($title,23,2);
		$hour = substr($title,31,2);

		$path = "/home/tomh/iphonepics/tl/$year/$month/$day/$hour/$year$month{$day}_$hour.mp4";
		if (!file_exists($path."-metadata-self")) {
			file_put_contents($path."-metadata-self", $self);
			file_put_contents($path."-metadata-thumb", $thumb);
		}
		echo "$path\n";
	}

}
