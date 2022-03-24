#!/bin/bash

function ctrlc() {
	/usr/bin/lockfile-remove /dev/shm/`/usr/bin/basename $0 .sh`
	>&2 echo "*TRAP*"
	exit
}

trap ctrlc INT
trap ctrlc TERM
trap ctrlc QUIT
trap ctrlc PIPE
trap ctrlc HUP

USER=`whoami`
/usr/bin/lockfile-create /dev/shm/`/usr/bin/basename $0 .sh`
cd /home/$USER/projects/youtubewebcam
# start in the present and go back
hoursago = 0
shopt -s nullglob
while true; do
	searchdir=`date -d "$hoursago hours ago" +%Y/%m/%d/%H`
	echo searching: "$searchdir"
	for f in /home/$USER/iphonepics/tl/$searchdir/20*_{[0-9][0-9],fu}.mp4; do 
		echo php newuploadvideo.php $f
		php newuploadvideo.php $f
	done
	hoursago=$(( hoursago + 1 ))
	if [ "$hoursago" -gt 30 ]; then
		break
	fi
	
done
/usr/bin/lockfile-remove /dev/shm/`/usr/bin/basename $0 .sh`
