<?php
set_time_limit(0);

require '../../vendor/autoload.php';
require '../../src/picsolvefunctions.php';
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


$existing_claim_codes = DB::queryFirstColumn("SELECT code FROM claim_codes");

$users = DB::query("SELECT * FROM users WHERE picsolve_auth_token!=%s", "");
$remaining_users = DB::queryFirstField("SELECT count(id) FROM users WHERE picsolve_auth_token!=%s ORDER BY id DESC", "");

$done_user_count=0;
foreach ($users as $user) {
	$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
	$picsolve_request=Requests::get('https://www.picsolve.com/albums', $headers, $config['requestoptions']);
	$picsolve_response = json_decode($picsolve_request->body,true);
        if (!$picsolve_response['success']) {
//		echo "*** Picsolve error on getting albums, skipping ***".PHP_EOL;
		$done_user_count=$done_user_count+1;
		$remaining_users=$remaining_users-1;
		continue;
	};

	$master_album=[];


	foreach ($picsolve_response['data'] as &$picsolve_album) {
		$album_picsolve_request=Requests::get('https://www.picsolve.com/albums/'.$picsolve_album['albumId'], $headers, $config['requestoptions']);
		$album_picsolve_response = json_decode($album_picsolve_request->body,true);
	        if (!$album_picsolve_response['success']) {
//			echo "*** Picsolve error on getting album photos, skipping ***".PHP_EOL;
			$done_user_count=$done_user_count+1;
			$remaining_users=$remaining_users-1;
			continue;
		};

		$master_album=array_merge($master_album,$album_picsolve_response['data']['photos']);
	};

	$master_album_count=count($master_album);
	foreach ($master_album as $photo) {
		$claim_code = substr(substr($photo['thumbnailUrl'], strrpos($photo['thumbnailUrl'], '/') + 1),0,12);
		$park=substr($claim_code,0,2);
		if (substr($claim_code,0,4)=="user") {
			echo "Claim code starting user detected. Skipping.".PHP_EOL;
			continue;
		};

		$ride=substr($claim_code,2,2);
		$mystery=substr($claim_code,4,1);
		$date=substr($claim_code,5,4);
		echo "Claim code:$claim_code Park:$park Ride:$ride Mystery:$mystery Date:$date Done $done_user_count users $remaining_users remaining, ";


                if (in_array($claim_code,$existing_claim_codes)) {
			echo "Current photo existing skipping".PHP_EOL;
                        continue;
                };

		echo "Current photo New, Adding to DB".PHP_EOL;
		DB::insert('claim_codes', array(
	                'code' => $claim_code,
			'park' => $park,
			'ride' => $ride,
			'mystery' => $mystery,
			'date' => $date,
			'date_taken' => $photo['dateTaken'],
			'date_claimed' => $photo['dateClaimed']
        	));
	};
	$done_user_count=$done_user_count+1;
	$remaining_users=$remaining_users-1;
};
