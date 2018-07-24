<?php
set_time_limit(0);

require '../../vendor/autoload.php';
require '../../src/picsolvefunctions.php';

use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;

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
$account = DB::queryFirstRow("SELECT * FROM picsolvedownloader_accounts WHERE provider=%s AND process=%i", 'DROPBOX', 1);

if (!$account) {
        echo "No accounts awaiting processing..".PHP_EOL;
        die();
};

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

	$app = new DropboxApp($config['dropbox_client_id'],$config['dropbox_client_secret'],$account['accesstoken']);
	$dropbox = new Dropbox($app);


//	$listFolderContents = $dropbox->listFolder("/testing/");
	$listFolderContents = $dropbox->listFolder("/");
	$dropbox_items = $listFolderContents->getItems()->all();
	$existing_filenames=[];
	foreach ($dropbox_items as $item) {
		$existing_filenames[]=$item->getName();
	};

	$headers = array('Content-Type' => 'application/json', 'User-Agent' => \Campo\UserAgent::random());
	$picsolve_request=Requests::get('https://www.picsolve.com/parks',$headers, $config['requestoptions']);
	$picsolve_response = json_decode($picsolve_request->body,true);

	if (!$picsolve_response['success']) {
		DB::update('picsolvedownloader_accounts', array(
                        'process' => 1
                ), "id=%i", $account['id']);
		echo PHP_EOL;
		echo $picsolve_request->body;
	        die("Unable to get list of parks from Picsolve for account:".$account['id'].PHP_EOL);
	};

	$picsolve_parks=[];
	foreach ($picsolve_response['data'] as $park) {
	        $picsolve_parks[$park['parkCode']]=$park['name'];
	};




	$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
	$picsolve_request=Requests::get('https://www.picsolve.com/albums', $headers,$config['requestoptions']);
	$picsolve_response = json_decode($picsolve_request->body,true);


	if (!$picsolve_response['success']) {
                DB::update('picsolvedownloader_accounts', array(
                        'process' => 1
                ), "id=%i", $account['id']);
                die("Unable to get list of albums from Picsolve".PHP_EOL);
        };

	$master_album=[];
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
		sleep (1);
	};
	$master_album_count=count($master_album);
	$done_count=0;
	$new_count=0;
	$failed_image_ids=[];
	foreach ($master_album as $photo) {
		$photo['parkData']['name']=$picsolve_parks[$photo['parkData']['parkCode']];
		echo "Downloading image:".$photo['imageId'].PHP_EOL;
                $filename=date("Y_m_d",strtotime($photo['dateTaken']))."_".$photo['parkData']['name']."_".$photo['rideData']['name']."_".$photo['imageId'].".jpeg";
                $filename=preg_replace('@[^0-9a-zA-Z/_\.]+@i', '', $filename);

		if (in_array($filename,$existing_filenames)) {
			echo "Image exists in dropbox already. Skipping".PHP_EOL;
			$done_count=$done_count+1;
			$remaining_count=$master_album_count-$done_count;
			continue;
		};

		$result=picsolve_photo_getfull($photo['imageId'],$user);
		if (!$result) {
			echo "*** Failed to get full image url for:".$photo['imageId'].PHP_EOL;
			$failed_image_ids[]=$photo['imageId'];
		} else {
			echo "Trying to save ".$result['filename'].PHP_EOL;
			print_r($result);
	                $content=file_get_contents($result['full_image'], False, $proxystreamcontext);

			$dropbox_timestamp=substr_replace($photo['dateTaken'],"T",10,1)."Z";
			$dropboxFile = DropboxFile::createByStream("/".$result['filename'], $content);
			$writemode['.tag']="overwrite";
			$file = $dropbox->upload($dropboxFile, "/".$result['filename'], ['autorename' => true, 'client_modified' => $dropbox_timestamp, 'mode' => $writemode, 'mute' => true]);
			$new_count=$new_count+1;
		};
		$done_count=$done_count+1;
		$remaining_count=$master_album_count-$done_count;
		echo "Done $done_count/$master_album_count ".$remaining_count." Remaining".PHP_EOL;
	};



        if ($account['manual'] || ($new_count>0 && $account['last_notified']<=time()-1800)) {
                echo "Sending email and notification".PHP_EOL;

                pushnotification($user['id'],"DROPBOX","Dropbox","Finished saving $new_count photo's to Dropbox");
		$account['last_notified']=time();

		$smarty->assign("user_uid_hash",hash('ripemd160', $user['id']));
		$smarty->assign('user',$user);

		$smarty->assign('failed_image_count',count($failed_image_ids));
		$smarty->assign('new_count',$new_count);

		$email_text=$smarty->fetch('emails/text/picsolvedownloader/completed_dropbox.tpl');
		$email_html=$smarty->fetch('emails/picsolvedownloader/completed_dropbox.tpl');

		if ($user['email']) {
			$mg->messages()->send($config['email_domain'], [
				'from'    => $config['app_support_email'],
				'to'      => $user['email'],
				'subject' => "Finished saving your Picsolve photos to Dropbox",
				'text'    => $email_text,
				'html'    => $email_html
		       	]);
	       	};
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
		'provider' => "DROPBOX"
	));
} else {
	echo "Invalid user".PHP_EOL;
};
