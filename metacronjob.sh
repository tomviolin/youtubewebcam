#!/bin/bash

cd `dirname $0`
/usr/bin/screen -d -m ./cronjob.sh

