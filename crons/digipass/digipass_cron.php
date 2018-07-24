<?php
set_time_limit(0);

require '../../vendor/autoload.php';

use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

require '../../config.php';
require '../../commonincludes.php';

function is_cli()
{
    return (php_sapi_name() === 'cli');
};

$cli=is_cli();
if (!$cli) {
	die("This script MUST be run via the CLI");
};




//look for Gamma mode Digipass
//echo "Looking for Mode Gamma Digipass".PHP_EOL;
$digipass = DB::queryFirstRow("SELECT * FROM digipass WHERE mode=2 ORDER BY last_imported ASC");
if (!$digipass || $digipass['last_imported']>=(time()-300) ) {
//		echo "No luck :( Trying for Mode Beta".PHP_EOL;

	//look for Beta mode Digipass
//	echo "Looking for Mode Beta Digipass".PHP_EOL;
	$digipass = DB::queryFirstRow("SELECT * FROM digipass WHERE mode=1 ORDER BY last_imported ASC");
	if (!$digipass || $digipass['last_imported']>=(time()-900)) {
//		echo "No luck :( Trying for Mode Alpha".PHP_EOL;

		//look for Alpha mode Digipass
//		echo "Looking for Mode Alpha Digipass".PHP_EOL;
		$digipass = DB::queryFirstRow("SELECT * FROM digipass WHERE mode=0 ORDER BY last_imported ASC");
		if (!$digipass || $digipass['last_imported']>=(time()-3600)) {
//			echo "No luck :( Sleeping".PHP_EOL;
			die();
		};
	};
};

echo "********************".PHP_EOL;

$headers = array('Content-Type' => 'application/json', 'User-Agent' => \Campo\UserAgent::random());
$testurls=['https://www.picsolve.com/parks'];
$testurl=$testurls[array_rand($testurls)];
echo "Selected test url:".$testurl.PHP_EOL;

$picsolve_request=Requests::get($testurl,$headers, $config['requestoptions']);
$picsolve_response = json_decode($picsolve_request->body,true);

if (!$picsolve_response['success']) {
	die("Unable to get list of parks from Picsolve, IP Blockage?:".$digipass['barcode'].PHP_EOL);
};

echo "**************************************************".PHP_EOL;
echo "Claiming Digipass:".$digipass['barcode'].PHP_EOL;
$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $digipass['user']);
if ($user) {
	bugsnag_report_user($user);

	$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
	$data = array('mediaSetId' => $digipass['barcode']);
	$picsolve_request=Requests::post('https://www.picsolve.com/redeem', $headers, json_encode($data),$config['requestoptions']);
	$picsolve_response = json_decode($picsolve_request->body,true);
	if ($picsolve_response['success']) {
		echo "Digipass added ok. new photo's from the Digipass were added to the account(".$user['name']."-".$user['email'].")".PHP_EOL;

		if ($digipass['last_notified']<=time()-1800) {
			echo "Notifying user of new Digipass photos".PHP_EOL;
			pushnotification($user['id'],"DIGIPASS","Digipass","Sucessfully added photo's to your Digipass");
			$digipass['last_notified']=time();
		};

		DB::update('digipass', array(
			'last_imported' => time(),
			'last_import_status' => $picsolve_request->status_code,
			'last_notified' => $digipass['last_notified'],
			//Mode Beta
			'mode' => 1,
			'gammacount' => 0,
			'last_successful' => time()
		), "id=%i", $digipass['id']);

		DB::update('picsolvedownloader_accounts', array(
			'process' => 1
		), "process=%i AND autoprocess=%i AND user=%i", 0, 1, $digipass['id']);
	} else {
		$error=true;
		switch($picsolve_request->status_code) {
			case 409:
				echo "The photos have already been claimed on the account(".$user['name']."-".$user['email'].") \n";
				$error=false;
				break;
			case 404:
				echo "Picsolve does not recognise the provided barcode. Either it is invalid or there are no photos on the barcode. \n";
				break;
			case 403:
				echo "Picsolve reports an authentication issue with the users Picsolve account".PHP_EOL;
				echo $picsolve_request->body.PHP_EOL;
				break;
			case 200:
			         $bugsnag->notifyError('PicsolveAPIError', 'Error checking Digipass', function ($report) use ($picsolve_request,$picsolve_response){
					$report->setSeverity('error');
					$report->setMetaData([
						'picsolveresponse' => array(
							'code' => $picsolve_request->status_code,
							'body' => $picsolve_request->body
						)
					]);
				});
				echo "***** 200 photo response ***** \n";

				break;
			default:
			         $bugsnag->notifyError('PicsolveAPIError', 'Error checking Digipass', function ($report) use ($picsolve_request,$picsolve_response){
					$report->setSeverity('error');
					$report->setMetaData([
						'picsolveresponse' => array(
							'code' => $picsolve_request->status_code,
							'body' => $picsolve_response
						)
					]);
				});
				echo "***** Unknown status Please check BugSnatch ***** \n";

		};

		if ($error) {
			echo "An error occured.".PHP_EOL;
			switch($digipass['mode']) {
				case 0:
				case 1:
					//Alpha (0) & Beta (1)
					//set to Gamma
					echo "Setting to Mode Gamma".PHP_EOL;
					$digipass['mode']=2;
					break;
				case 2:
					//Gamma
					echo "In mode Gamma already".PHP_EOL;
					$digipass['gammacount']=$digipass['gammacount']+1;
					if ($digipass['gammacount']>=4) {
						echo "Mode Gamma max reached. Reverting to Mode Alpha".PHP_EOL;
						$digipass['mode']=0;
						$digipass['gammacount']=0;
					};
					break;
			};
		} else {
			switch($digipass['mode']) {
				case 1:
					//Beta
					if ($digipass['last_added']<=(time()-7200)) {
						//Not had any new photos recently. Reverting to Mode Alpha
						echo "No new photo's recently. Reverting to Mode Alpha".PHP_EOL;
						$digipass['mode']=0;
					};
					break;
				case 2:
					//Gamma
					echo "Mode Gamma reset".PHP_EOL;
					$digipass['mode']=0;
					$digipass['gammacount']=0;
					break;
			};
		};

		if (!$error) {
			$digipass['last_successful']=time();
		};

		DB::update('digipass', array(
			'last_imported' => time(),
			'last_import_status' => $picsolve_request->status_code,
			'mode' => $digipass['mode'],
			'gammacount' => $digipass['gammacount'],
			'last_successful' => $digipass['last_successful']
		), "id=%i", $digipass['id']);
		echo $digipass['mode'].PHP_EOL;
		echo $digipass['id'].PHP_EOL;
	};
} else {
	echo "Invalid user \n";
};

