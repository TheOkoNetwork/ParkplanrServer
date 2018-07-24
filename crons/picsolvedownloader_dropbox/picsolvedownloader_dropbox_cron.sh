#!/bin/bash

processor_id=1
if [ "$1" != "" ];then
        processor_id=$1
fi

cd /var/www/parkplanr.okonetwork.org.uk/crons/picsolvedownloader_dropbox/

while [ 1 ];do
	date
	php picsolvedownloader_dropbox_cron.php $processor_id
	sleep 5
done
