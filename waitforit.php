<?php
if (count($argv) < 2) {
	echo "usage: ".$argv[0]." (file to wait for change of)\n";
	exit(1);
}
$fmtime = @filemtime($argv[1]);
if ($fmtime === FALSE) {
	echo "file does not exist.\n";
	exit(1);
}

while (TRUE) {
	clearstatcache(true,$argv[1]);
	$newtime = @filemtime($argv[1]);
	if ($newtime != $fmtime) exit(0);
	usleep(100000);
}

?>
