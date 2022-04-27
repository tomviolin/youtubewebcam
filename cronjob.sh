#!/bin/bash

MOVIEDIR=/opt/webcam/outbox


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
cd /home/tomh/microservices/youtubewebcam


shopt -s nullglob
for f in `ls -1r /opt/webcam/outbox/*.mp4 | head -3`; do
	php newuploadvideo.php "$f"
done

/usr/bin/lockfile-remove /dev/shm/`/usr/bin/basename $0 .sh`
