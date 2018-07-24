<?php
	//used for JWT handling
	use \Firebase\JWT\JWT;

	//used for AWS S3 storage (image/map assets)
	use Aws\S3\S3Client;

	//used for dropbox (picsolve saver)
	use Kunnu\Dropbox\Dropbox;
	use Kunnu\Dropbox\DropboxApp;

	//used for firebase
	use Kreait\Firebase\Factory;
	use Kreait\Firebase\ServiceAccount;

	$bugsnag = Bugsnag\Client::make($config['bugsnag_api_key']);
	Bugsnag\Handler::register($bugsnag);
//	Bugsnag\Handler::registerWithPrevious($bugsnag);



	$mg = \Mailgun\Mailgun::create($config['mailgun_api_key']);
	$bugsnag->setAppVersion($config['server_version']);

        DB::$user = $config['mysql_user'];
        DB::$password = $config['mysql_password'];
        DB::$dbName = $config['mysql_db'];
        DB::$host = $config['mysql_host'];
	DB::$throw_exception_on_error = true;
	DB::$throw_exception_on_nonsql_error = true;

	$config['jwt_public_key']=file_get_contents($config['filesystem_root'].'/.ht_jwtpub');
	$config['jwt_private_key']=file_get_contents($config['filesystem_root'].'/.ht_jwtkey');
	$config['google_oauth_creds'] = $config['filesystem_root'].'/.ht_google_oauth-credentials.json';

	$s3client = S3Client::factory(array(
		'credentials' => array(
	        	'key'    => $config['aws_id'],
	        	'secret' => $config['aws_key']
		 ),
		'region' => "eu-west-2",
		'version' => "latest"
	));

	$facebookclient = new \Facebook\Facebook([
		'app_id' => $config['facebook_app_id'],
		'app_secret' => $config['facebook_app_secret'],
		'default_graph_version' => 'v2.10',
		'http_client_handler' => 'stream'
	]);

	$googleclient = new Google_Client(['client_id' => $config['google_client_id']]);  // Specify the CLIENT_ID of the app that accesses the backend

	function queuetimes_update($ride,$open,$queuetime) {
		$timestamp=time();

		$changed=false;
		if ($ride['open']!=$open) {
			$changed=true;
		};
		if ($ride['queuetime']!=$queuetime) {
			$changed=true;
		};

		DB::update('rides', array(
			'open' => $open,
			'queuetime' => $queuetime,
			'queuetimes' => true,
			'queuetime_updated' => $timestamp,
			'queuetime_outdated' => false,
			'queuetimes' => true
		), "id=%i", $ride['id']);

		if ($changed) {
			DB::insert('ride_queuetime_log', array(
				'ride' => $ride['id'],
				'open' => $open,
				'queuetime' => $queuetime,
				'timestamp' => $timestamp
			));
		};
	};

	function bugsnag_report_user($user) {
		global $bugsnag;
		$bugsnag->registerCallback(function ($report) use ($user) {
			if (isset($user['email'])) {
				$report->setUser([
					'id' => $user['id'],
					'name' => $user['name'],
					'email' => $user['email']
				]);
			} else {
				$report->setUser([
					'id' => $user['id'],
					'name' => $user['name']
				]);
			};
		});
	};

	require_once($config['filesystem_root'].'/src/push_notifications.php');



	$serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/.ht_firebase_credentials.json');
	$firebase = (new Factory)->withServiceAccount($serviceAccount)->create();

	$config['requestoptions'] = array(
// 		'proxy' => '127.0.0.1:8118'
		'proxy' => $config['proxyhost'].':'.$config['proxyport']
	);
?>
