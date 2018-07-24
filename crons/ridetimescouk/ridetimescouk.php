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

 	$ridetimescouk_url="http://ridetimes.co.uk/queue-times-new.php";

        $headers = array('User-Agent' => 'parkplanr.okonetwork.org.uk');
        $ridetimescouk_request=Requests::get($ridetimescouk_url, $headers);

	switch ($ridetimescouk_request->status_code) {
		case 200:
		        $ridetimescouk_response = json_decode($ridetimescouk_request->body,true);

			foreach ($ridetimescouk_response as $ride_status) {
	        	        $ride=DB::queryFirstRow("SELECT rides.* FROM rides INNER JOIN queuescrapers_ridetimescouk_rides ON rides.id = queuescrapers_ridetimescouk_rides.ride WHERE queuescrapers_ridetimescouk_rides.ride_id =%i",$ride_status['ride_id']);
				if ($ride){
					$time=$ride_status['time'];
					if (!$time) {
						$time=0;
					};
					switch ($ride_status['status']) {
						case "Open 10:00":
							$open=false;
							break;
						case "Open 10:30":
							$open=false;
							break;
						case "Open 11:00":
							$open=false;
							break;
						case "closed":
							$open=false;
							break;
						case "open":
							$open=true;
							break;
						default:
							$open=false;
							echo "Unknown ride status:".$ride_status['status']." Assuming closed. \n";
					};

					if ($open) {
						$open_human="Open";
					} else {
						$open_human="Closed";
					};
					echo "Ride ".$ride['name']." found it is currently $open_human with a queue of $time minutes\n";
					queuetimes_update($ride,$open,$time);
				} else {
					echo "Ride not found\n";
				};
			};
			break;
		default:
			echo "Something went wrong.\n";
			break;
	};
?>
