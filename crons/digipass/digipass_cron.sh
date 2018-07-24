#!/bin/bash

cd /var/www/parkplanr.okonetwork.org.uk/crons/digipass/

count=0
while [ 1 ];do
#	echo "**************************************************"
	php digipass_cron.php
	count=$(($count+1))
#	echo "ran $count times"
	sleep 5
done
