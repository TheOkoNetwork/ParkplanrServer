#!/bin/bash

cd /var/www/parkplanr.okonetwork.org.uk/crons/themeparksJS/

counter=0
while [ 1 ];do
	nodejs index.js
	counter=$((counter+1))
	echo " "
	echo "Ran $counter times"
	sleep 19
done
