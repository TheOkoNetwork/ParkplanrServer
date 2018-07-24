#!/bin/bash

cd /var/www/parkplanr.okonetwork.org.uk/crons/thorpeparkqueuetimes/

while [ 1 ];do
	php thorpeparkqueuetimes.php
	echo " "
	sleep 30
done
