<?php
set_time_limit(0);

require '../../vendor/autoload.php';
require '../../src/picsolvefunctions.php';

use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

require '../../config.php';
require '../../commonincludes.php';

//setup smarty template engine
$smarty = new Smarty;
$smarty->setTemplateDir('../../templates')->setCompileDir('../../templates_c');

function is_cli()
{
    return (php_sapi_name() === 'cli');
};

$cli=is_cli();
if (!$cli) {
	die("This script MUST be run via the CLI");
};

$processor_id=$argv[1];

echo PHP_EOL."*******************************************".PHP_EOL;
$account = DB::queryFirstRow("SELECT * FROM picsolvedownloader_accounts WHERE provider=%s AND process=%i", 'GOOGLEDRIVE', 1);

if (!$account) {
	echo "No accounts awaiting processing".PHP_EOL;
	die();
};

//should be 2
DB::update('picsolvedownloader_accounts', array(
	'process' => 2,
	'processor' => $processor_id
), "id=%i", $account['id']);

$account = DB::queryFirstRow("SELECT * FROM picsolvedownloader_accounts WHERE id=%i AND processor=%i", $account['id'], $processor_id);

if (!$account) {
	echo "Account claimed by another bot. Skipping.".PHP_EOL;
	die();
};


$proxycontext = array(
    'http' => array(
        'proxy' => 'tcp://'.$config['proxyhost'].':'.$config['proxyport'],
        'request_fulluri' => true,
    ),
);
$proxystreamcontext = stream_context_create($proxycontext);



$start_time=time();

echo "Account awaiting processing found".PHP_EOL;

$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $account['user']);
if ($user) {
        bugsnag_report_user($user);

	if (!$user['picsolve_auth_token']) {
		echo $user['id'] . " Picsolve auth token missing ***".PHP_EOL;
		DB::update('picsolvedownloader_accounts', array(
                        'process' => false,
                        'manual' => false,
                ), "id=%i", $account['id']);
                //should email here
		die();
	};

	$redirect_uri = 'https://parkplanr.okonetwork.org.uk/picsolvedownloader/googledrive/callback';
	$client = new Google_Client();
	$client->setAuthConfig($config['google_oauth_creds']);
	$client->setAccessType('offline');
	$client->setApprovalPrompt('force');
	$client->setRedirectUri($redirect_uri);
	$client->addScope("https://www.googleapis.com/auth/drive");
	$driveservice = new Google_Service_Drive($client);

	$tokens=$client->refreshToken($account['refreshtoken']);

	if ($tokens['refresh_token']) {
		$refreshtoken=$tokens['refresh_token'];
		$accesstoken=$tokens['access_token'];
	} else {
		print_r($tokens);
		echo ("*** Invalid google creds".PHP_EOL);
		die();
	};

	DB::update('picsolvedownloader_accounts', array(
		'accesstoken' => $accesstoken,
		'refreshtoken' => $refreshtoken
	), "id=%i", $account['id']);

	if ($account['folder']) {
		echo ("Using existing ParkPlanr folder, folder ID: ".$account['folder'].PHP_EOL);
	} else {
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
		    'name' => 'ParkPlanr',
		    'mimeType' => 'application/vnd.google-apps.folder'));
		$file = $driveservice->files->create($fileMetadata, array(
		    'fields' => 'id'));
		printf("Created ParkPlanr folder, folder ID: %s\n", $file->id);
		echo PHP_EOL;

		$account['folder']=$file->id;
		DB::update('picsolvedownloader_accounts', array(
			'folder' => $account['folder']
		), "id=%i", $account['id']);
	};
	$smarty->assign("folder",$account['folder']);


	$headers = array('Content-Type' => 'application/json', 'User-Agent' => \Campo\UserAgent::random());
        $picsolve_request=Requests::get('https://www.picsolve.com/parks',$headers, $config['requestoptions']);
        $picsolve_response = json_decode($picsolve_request->body,true);

	if (!$picsolve_response['success']) {
		DB::update('picsolvedownloader_accounts', array(
			'process' => 1
		), "id=%i", $account['id']);
		echo PHP_EOL;
                die("Unable to get list of parks from Picsolve".PHP_EOL);
		sleep (1);
        };

        $picsolve_parks=[];
        foreach ($picsolve_response['data'] as $park) {
                $picsolve_parks[$park['parkCode']]=$park['name'];
        };


	$existing_filenames=[];
	$pageToken = null;
	$query="'".$account['folder']."' in parents";
	do {
		$response = $driveservice->files->listFiles(array(
			'q' => $query,
			'spaces' => 'drive',
		        'pageToken' => $pageToken,
		        'fields' => 'nextPageToken, files(id, name, appProperties)',
		));
		foreach ($response->files as $file) {
//			$existing_filenames[]=$file->name;
			$existing_filenames[$file->name]['quality']=$file->appProperties['filequality'];
			$existing_filenames[$file->name]['id']=$file->id;
			//print_r($file);
			//die();
		};
		$pageToken = $response->nextPageToken;
	} while ($pageToken != null);

	$count=count($existing_filenames);
	echo "Found:$count existing photos".PHP_EOL;
	//print_r($existing_filenames);
	//die();
	$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
	$picsolve_request=Requests::get('https://www.picsolve.com/albums', $headers,$config['requestoptions']);
	$picsolve_response = json_decode($picsolve_request->body,true);
	$master_album=[];


	if (!$picsolve_response['success']) {
		DB::update('picsolvedownloader_accounts', array(
			'process' => 1
		), "id=%i", $account['id']);
                die("Unable to get list of albums from Picsolve");
        };
	sleep (rand(1,10));

	foreach ($picsolve_response['data'] as &$picsolve_album) {
		$album_picsolve_request=Requests::get('https://www.picsolve.com/albums/'.$picsolve_album['albumId'], $headers,$config['requestoptions']);
		$album_picsolve_response = json_decode($album_picsolve_request->body,true);
		if (!$album_picsolve_response['success']) {
                        DB::update('picsolvedownloader_accounts', array(
                                'process' => 1
                        ), "id=%i", $account['id']);
                        echo PHP_EOL;
                        echo $album_picsolve_request->body;
                        die("Unable to get list of photos in album:".$picsolve_album['albumId']." from Picsolve for account:".$account['id'].PHP_EOL);
                };

		$master_album=array_merge($master_album,$album_picsolve_response['data']['photos']);
		sleep (rand(1,10));
	};

	$blacklisted_filenames = DB::queryFirstColumn("SELECT filename FROM picsolvedownloader_blacklisted_photos WHERE user=%i", $account['user']);

	$master_album_count=count($master_album);
	$done_count=0;
	$new_count=0;
	$failed_image_ids=[];
	foreach ($master_album as $photo) {
		echo PHP_EOL;

                $photo['parkData']['name']=$picsolve_parks[$photo['parkData']['parkCode']];
		echo "Checking image:".$photo['imageId'].PHP_EOL;

		$filename=date("Y_m_d",strtotime($photo['dateTaken']))."_".$photo['parkData']['name']."_".$photo['rideData']['name']."_".$photo['imageId'].".jpeg";
                $filename=preg_replace('@[^0-9a-zA-Z/_\.]+@i', '', $filename);

                if (in_array($filename,$blacklisted_filenames)) {
                        echo "$filename, Image blacklisted by user. Skipping".PHP_EOL;
                        $done_count=$done_count+1;
                        $remaining_count=$master_album_count-$done_count;
                        continue;
                };

                if (isset($existing_filenames[$filename])) {
			echo "$filename, Image exists in Google Drive already. Checking quality.".PHP_EOL;
			if (($existing_filenames[$filename]['quality'])==$config['picsolve_highest_quality']) {
				echo "$filename, exists in highest quality. Skipping.".PHP_EOL;
				$done_count=$done_count+1;
				$remaining_count=$master_album_count-$done_count;
				continue;
                	} else {
				echo "Image exists but low quality. Trying for improvement.".PHP_EOL;
			};
               	};

		$result=picsolve_photo_gethighest($photo,$user);
                if (isset($existing_filenames[$filename])) {
			if ($result['quality']==$existing_filenames[$filename]['quality']) {
				echo "No quality improvement available. Skipping.".PHP_EOL;
				$done_count=$done_count+1;
				$remaining_count=$master_album_count-$done_count;
				continue;
			} else {
				echo "Quality improvement available  quality was " . $existing_filenames[$filename]['quality']. ",quality of ".$result['quality_text'].", now processing.".PHP_EOL;
			};
		};

		if (!$result) {
			echo "*** Failed to get full image url for:".$photo['imageId'].PHP_EOL;
			$failed_image_ids[]=$photo['imageId'];
		} else {
			//print_r($result);
			echo "File does NOT exist in folder, or low quality. Processing.".PHP_EOL;

			$appProperties['filequality']=$result['quality'];

	                if (isset($existing_filenames[$filename])) {
				echo "$filename, Image exists in Google Drive already. Updating.".PHP_EOL;
				try {
					$fileMetadata = new Google_Service_Drive_DriveFile(array(
						'appProperties' => $appProperties
					));

					$file = $driveservice->files->get($existing_filenames[$filename]['id']);
					$content=file_get_contents($result['full_image'], False, $proxystreamcontext);
					$file->setDescription($result['share_name']);
					$file->setAppProperties($appProperties);
					$additionalParams = array(
						'data' => $content
					);
					$updatedFile = $driveservice->files->update($existing_filenames[$filename]['id'], $fileMetadata, $additionalParams);
					$new_count=$new_count+1;
					$done_count=$done_count+1;
					$remaining_count=$master_album_count-$done_count;
					echo "Done $done_count/$master_album_count ".$remaining_count." Remaining".PHP_EOL;
				} catch (Exception $e) {
					print "An error occurred updating: " . $e->getMessage();
					echo 'Message: ' .$e->getMessage();
					$errorreason=json_decode($e->getMessage(),true)['error']['errors'][0]['reason'];

        	       	        	$smarty->assign('errorreason',$errorreason);

					$smarty->assign("user_uid_hash",hash('ripemd160', $user['id']));
        	       	        	$smarty->assign('user',$user);

        	       	        	$email_text=$smarty->fetch('emails/text/picsolvedownloader/failed_google.tpl');
        	       	        	$email_html=$smarty->fetch('emails/picsolvedownloader/failed_google.tpl');

        		                $mg->messages()->send($config['email_domain'], [
       			                        'from'    => $config['app_support_email'],
        	       		                'to'      => $user['email'],
        	       		                'subject' => "Whoops! Something went wrong saving to your Google Drive",
        	       		                'text'    => $email_text,
        	       		                'html'    => $email_html
        	       		        ]);

					DB::update('picsolvedownloader_accounts', array(
						'process' => 0
					), "id=%i", $account['id']);
					die();
				}
			} else {
				echo "Quality of ".$result['quality_text'].", processing.".PHP_EOL;

				$fileMetadata = new Google_Service_Drive_DriveFile(array(
					'name' => $result['filename'],
					'parents' => array($account['folder']),
					'appProperties' => $appProperties
				));

				$content=file_get_contents($result['full_image'], False, $proxystreamcontext);
				try {
					$file = $driveservice->files->create($fileMetadata, array(
						'data' => $content,
						'mimeType' => 'image/jpeg',
						'uploadType' => 'multipart',
						'fields' => 'id'
					));
					printf("File ID: %s\n", $file->id);
					$new_count=$new_count+1;
					//catch exception
				} catch(Exception $e) {
					echo 'Message: ' .$e->getMessage();
					$errorreason=json_decode($e->getMessage(),true)['error']['errors'][0]['reason'];

        	       	        	$smarty->assign('errorreason',$errorreason);

					$smarty->assign("user_uid_hash",hash('ripemd160', $user['id']));
        	       	        	$smarty->assign('user',$user);

        	       	        	$email_text=$smarty->fetch('emails/text/picsolvedownloader/failed_google.tpl');
        	       	        	$email_html=$smarty->fetch('emails/picsolvedownloader/failed_google.tpl');

        		                $mg->messages()->send($config['email_domain'], [
       			                        'from'    => $config['app_support_email'],
        	       		                'to'      => $user['email'],
        	       		                'subject' => "Whoops! Something went wrong saving to your Google Drive",
        	       		                'text'    => $email_text,
        	       		                'html'    => $email_html
        	       		        ]);

					DB::update('picsolvedownloader_accounts', array(
						'process' => 0
					), "id=%i", $account['id']);
					die();
				};
				$done_count=$done_count+1;
				$remaining_count=$master_album_count-$done_count;
				echo "Done $done_count/$master_album_count ".$remaining_count." Remaining".PHP_EOL;
			};
		};
	};

	if ($account['manual'] || ($new_count>0 && $account['last_notified']<=time()-1800)) {
		echo "Sending email and notification".PHP_EOL;

                pushnotification($user['id'],"GOOGLEDRIVE","Google Drive","Finished saving $new_count photo's to Google Drive");

		$smarty->assign("user_uid_hash",hash('ripemd160', $user['id']));
		$smarty->assign('user',$user);

		$smarty->assign('new_count', $new_count);
		$smarty->assign('failed_image_count',count($failed_image_ids));

		$email_text=$smarty->fetch('emails/text/picsolvedownloader/completed_google.tpl');
		$email_html=$smarty->fetch('emails/picsolvedownloader/completed_google.tpl');

		$mg->messages()->send($config['email_domain'], [
			'from'    => $config['app_support_email'],
			'to'      => $user['email'],
			'subject' => "Finished saving your Picsolve photos to Google Drive",
			'text'    => $email_text,
			'html'    => $email_html
       		]);
		$account['last_notified']=time();

	};

	DB::update('picsolvedownloader_accounts', array(
		'process' => false,
		'manual' => false,
		'last_notified' => $account['last_notified']
	), "id=%i", $account['id']);

	$end_time=time();
	DB::insert('picsolvedownloader_logs', array(
		'user' => $user['id'],
		'timetaken' => $end_time-$start_time,
		'photosprocessed' => $master_album_count,
		'errors' => count($failed_image_ids),
		'provider' => "GOOGLEDRIVE"
	));
} else {
	echo "Invalid user".PHP_EOL;
};
