#!/bin/bash
USER=`whoami`
/usr/bin/lockfile-create /dev/shm/`/usr/bin/basename $0 .sh`
cd /home/$USER/projects/youtubewebcam
for f in /home/$USER/iphonepics/tl/20??/??/??/*/20*_{[0-9][0-9],fu}.mp4; do 
	php newuploadvideo.php $f
done

/usr/bin/lockfile-remove /dev/shm/`/usr/bin/basename $0 .sh`
