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

 	$legoland_windsor_queuetimes_url="http://merlincms.com/llwqueuetime.php";

        $headers = array('User-Agent' => 'parkplanr.okonetwork.org.uk');

	$options = array(
		'auth' => new Requests_Auth_Basic(array('queuetime', 'queuetime'))
//		'proxy' => $config['proxyhost'].":".$config['proxyport']
	);

        $legoland_windsor_queuetimes_request=Requests::get($legoland_windsor_queuetimes_url, $headers,$options);

	switch ($legoland_windsor_queuetimes_request->status_code) {
		case 200:
		        $legoland_windsor_queuetimes_response = json_decode($legoland_windsor_queuetimes_request->body,true);

			foreach ($legoland_windsor_queuetimes_response['response'] as $ride_status) {
		                $ride_id=DB::queryFirstField("SELECT DISTINCT ride FROM legoland_windsor_queuetimes WHERE ride_id = %i",$ride_status['cmsId']);
				if ($ride_id){
		        	        $ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$ride_id);
					$time=$ride_status['time'];
					if (!$time) {
						$time=0;
					};
					switch ($ride_status['open']) {
						case "close":
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
					echo "Ride ".$ride['name']." found $ride_id it is currently $open_human with a queue of $time minutes\n";
                                        queuetimes_update($ride,$open,$time,time());
				} else {
					echo "Ride not found\n";
					print_r($ride_status);
				};
			};
			break;
		default:
			echo "Something went wrong.\n";
			echo $legoland_windsor_queuetimes_request->body;
			echo "\n";
			break;
	};
?>
