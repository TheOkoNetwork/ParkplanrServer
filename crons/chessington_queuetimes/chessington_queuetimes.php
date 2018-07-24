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

 	$chessington_queuetimes_url="https://legacy-api.attractions.io/apps/command/chessington/queue-times";
	$ignored_ride_ids=array();

	$headers = array('User-Agent' => 'parkplanr.okonetwork.org.uk');

	$apikey="edqXyMWFtuqGY6BZ9Epkzg4ptqe6v3c7tdqa97VbXjvrgZHC";

	$data=array(
		'session' => 'bf0d975fdca836625b09192844341836'
	);
        $chessington_queuetimes_request=Requests::get($chessington_queuetimes_url, $headers,$data);

	switch ($chessington_queuetimes_request->status_code) {
		case 200:
		        $chessington_queuetimes_response = json_decode($chessington_queuetimes_request->body,true);
			$challenge=$chessington_queuetimes_response['challenge'];
			break;
		default:
			echo "*** Failed to get challenge ***";
			die();
		};
	$response=md5($challenge.$apikey);

	$data=array(
		'session' => 'bf0d975fdca836625b09192844341836',
		'resort' => '44',
		'challenge' => $challenge,
		'response' => $response
	);
        $chessington_queuetimes_request=Requests::post($chessington_queuetimes_url, $headers,$data);



	switch ($chessington_queuetimes_request->status_code) {
		case 200:
		        $chessington_queuetimes_response = json_decode($chessington_queuetimes_request->body,true);

			if (!isset($chessington_queuetimes_response['queue-times'])) {
				echo "****** INVALID RESPONSE ******".PHP_EOL;
				die();
			};
			foreach ($chessington_queuetimes_response['queue-times'] as $ride_status) {
				if (in_array($ride_status['id'],$ignored_ride_ids)) {
					echo "On ignored ride list. \n";
					continue;
				};

	        	        $ride=DB::queryFirstRow("SELECT * FROM rides WHERE queuetimes_ride_id=%s","ChessingtonWorldOfAdventures_".$ride_status['id']);
				if ($ride){
					if (!isset($ride_status['wait_time'])) {
						$time=0;
					} else {
						$time=$ride_status['wait_time'];
					};
					switch ($ride_status['is_operational']) {
						case false:
							$open=false;
							break;
						case true:
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
//					print_r($ride_status);
//					echo "Ride:".$ride_status['id']." not found\n";
//					die();
				};
			};
			break;
		default:
			echo "Something went wrong.\n";
			break;
	};
?>
