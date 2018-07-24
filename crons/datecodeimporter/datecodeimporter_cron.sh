#!/bin/bash

processor_id=1
if [ "$1" != "" ];then
	processor_id=$1
fi

cd /var/www/parkplanr.okonetwork.org.uk/crons/datecodeimporter/

echo "I am processor:$processor_id"
date
php claimcodeimporter_cron.php $processor_id
php datecodeimporter_cron.php $processor_id
