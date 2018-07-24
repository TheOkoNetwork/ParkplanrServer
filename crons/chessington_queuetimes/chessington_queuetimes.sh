#!/bin/bash

cd /var/www/parkplanr.okonetwork.org.uk/crons/chessington_queuetimes/

while [ 1 ];do
	date
	php chessington_queuetimes.php
	echo " "
	sleep 30
done
