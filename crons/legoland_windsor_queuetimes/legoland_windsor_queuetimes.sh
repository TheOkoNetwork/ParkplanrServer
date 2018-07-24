#!/bin/bash

cd /var/www/parkplanr.okonetwork.org.uk/crons/legoland_windsor_queuetimes/

while [ 1 ];do
	php legoland_windsor_queuetimes.php
	echo " "
	sleep 30
done
