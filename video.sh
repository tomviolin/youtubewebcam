#!/bin/bash

( while true; do 
	php waitforit.php ~/iphonepics/currentlink.jpg
	sleep 0.1
	convert ~/iphonepics/currentlink.jpg -thumbnail 1280x720 /dev/shm/cli.jpg
	mv /dev/shm/cli.jpg /dev/shm/cl.jpg
done ) &
proc=$!
trap "kill $proc"  EXIT
sleep 5
fc=0
(while true; do cat /dev/shm/cl.jpg; sleep 0.010; done) |  /usr/local/bin/ffmpeg -threads 4 -thread_queue_size 16 -re -ar 44100 -ac 2 -acodec pcm_s16le -f s16le -ac 2 -i /dev/zero -f mjpeg -thread_queue_size 32768 -i - -vcodec libx264 -q:v 0 -g 30 -acodec libmp3lame -ab 128k -strict experimental -crf 40 -f flv rtmp://a.rtmp.youtube.com/live2/qjek-kurh-jfb4-dp04 
