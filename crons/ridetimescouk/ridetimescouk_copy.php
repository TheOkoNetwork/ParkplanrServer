<?php
	require_once('../../vendor/autoload.php');
	require_once('../../config.php');
	require_once('../../commonincludes.php');

	function is_cli() {
	    return (php_sapi_name() === 'cli');
	};

	$cli=is_cli();
	if (!$cli) {
	        die("This script MUST be run via the CLI");
	};

	$rides=DB::query("SELECT * FROM rides WHERE park=%i",1);
	foreach ($rides as $ride) {
		if ($ride['queuetimes_ride_id']) {
			$rt_id=$ride['queuetimes_ride_id'];
			$rt_id=explode('_', $rt_id, 2)[1];
			$ride_id=$ride['id'];
			$ride_name=$ride['name'];
			echo ("Importing ride:$ride_name with ParkPlanr ride ID:$ride_id and ridetimescouk ID:$rt_id \n");

			DB::insert('queuescrapers_ridetimescouk_rides', array(
				'ride' => $ride_id,
				'ride_id' => $rt_id
			));

		};
	};
?>
