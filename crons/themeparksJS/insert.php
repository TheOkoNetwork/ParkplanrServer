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

	$js_ride_id=$argv[1];
	$queuetime=$argv[2];
	if (!is_numeric($queuetime)) {
		$queuetime=0;
	};

	$open=$argv[3];
	if ($open==1) {
		$open_human="Open";
	} else {
		$open_human="Closed";
	};
	$timestamp=$argv[4];
	$name=$argv[5];

	$ride=DB::queryFirstRow("SELECT * FROM rides WHERE queuetimes_ride_id = %s",$js_ride_id);
	if ($ride){
		echo "Updated Ride $name ID: $js_ride_id is currently:$open_human with a queue time of $queuetime minutes";
		queuetimes_update($ride,$open,$queuetime,$timestamp);
	} else {
		echo "*** UNKNOWN *** \n";
		echo "Ride $name ID: $js_ride_id is currently:$open_human with a queue time of $queuetime minutes \n";
		echo "*** UNKNOWN *** \n";
	};
?>
