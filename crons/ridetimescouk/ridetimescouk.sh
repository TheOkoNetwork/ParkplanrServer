#!/bin/bash

cd /var/www/parkplanr.okonetwork.org.uk/crons/ridetimescouk/

while [ 1 ];do
	php ridetimescouk.php
	echo " "
	sleep 30
done
