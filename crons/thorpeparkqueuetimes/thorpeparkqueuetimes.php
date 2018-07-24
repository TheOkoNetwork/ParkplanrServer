<?php
	require_once('../../vendor/autoload.php');
	require_once('../../config.php');
        require_once('../../commonincludes.php');

	use PHPHtmlParser\Dom;

	function is_cli() {
	    return (php_sapi_name() === 'cli');
	};

	$cli=is_cli();
//	if (!$cli) {
//	        die("This script MUST be run via the CLI");
//	};

// 	$thorpeparkqueuetimes_url="https://www.thorpepark.com/Umbraco/Api/RideTimes/GetXml?excluded=18,26,27,28,25,29,30,31,32,33";
 	$thorpeparkqueuetimes_url="https://www.thorpepark.com/Umbraco/Api/RideTimes/GetXml?excluded=26,27,28,25,29,30,31,32,33";

        $headers = array('User-Agent' => 'parkplanr.okonetwork.org.uk');
        $thorpeparkqueuetimes_request=Requests::get($thorpeparkqueuetimes_url, $headers);

	switch ($thorpeparkqueuetimes_request->status_code) {
		case 200:
			$thorpeparkqueuetimes_response=$thorpeparkqueuetimes_request->body;
			$thorpeparkqueuetimes_response=str_replace('"\r\n', "", $thorpeparkqueuetimes_response);
			$thorpeparkqueuetimes_response=str_replace('\r\n"', "", $thorpeparkqueuetimes_response);
			$thorpeparkqueuetimes_response=str_replace('\r\n', "", $thorpeparkqueuetimes_response);
			$thorpeparkqueuetimes_response=str_replace('\"', '"', $thorpeparkqueuetimes_response);
			$thorpeparkqueuetimes_response="<xml>".trim($thorpeparkqueuetimes_response)."</xml>";
			$dom = new Dom;
			$dom->load($thorpeparkqueuetimes_response);

			foreach (range(0,count($dom->find('li'))-1,1) as $counter) {
				$time=$dom->find('span')[$counter]->text;
				$time=str_replace('mins', '', $time);
				$ride_name=$dom->find('li')[$counter]->text;
				//get actual status if possible
				$ride_status="open";

		                $ride_id=DB::queryFirstField("SELECT DISTINCT ride FROM thorpeparkqueuetimes WHERE ride_name = %s",$ride_name);
				if ($ride_id){
		        	        $ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$ride_id);

					switch ($ride_status) {
						case "open":
							$open=true;
							break;
						default:
							$open=false;
							echo "Unknown ride status:".$ride_status['status']." Assuming closed. \n";
					};
					if ($time=="Unavailable") {
						$open=false;
						$time=0;
					};

					if ($open) {
						$open_human="Open";
					} else {
						$open_human="Closed";
					};
					echo "Ride ".$ride['name']." found $ride_id it is currently $open_human with a queue of $time minutes\n";
					queuetimes_update($ride,$open,$time);
				} else {
					echo "Ride not found\n";
					echo $ride_name;
					echo "\n";
				};
			};
				break;
		default:
			echo "Something went wrong.\n";
			break;
	};
?>
