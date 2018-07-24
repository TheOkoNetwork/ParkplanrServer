#!/bin/bash

cd /var/www/parkplanr.okonetwork.org.uk/crons/queuetimes_outdated/

while [ 1 ];do
	php queuetimes_outdated.php
	echo " "
	#yes 3 hours is correct for this particular script
	sleep 10800
done
