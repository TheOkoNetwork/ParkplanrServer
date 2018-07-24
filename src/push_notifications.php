<?php
	//I contain the functions that handle routing and sending of Push Notifications

	use paragraph1\phpFCM\Client;
	use paragraph1\phpFCM\Message;
	use paragraph1\phpFCM\Recipient\Device;
	use paragraph1\phpFCM\Notification;

	function pushnotification($user_id,$tag,$title,$body, $actionurl=null) {
		global $config;

		if ($user_id!=22) {
			return false;
		};

		$fcm_client = new Client();
		$fcm_client->setApiKey($config['fcm_server_key']);
		$fcm_client->injectHttpClient(new \GuzzleHttp\Client());


		$pushnotification_devices=DB::query("SELECT * FROM pushnotification_devices WHERE user=%i", $user_id);

		foreach ($pushnotification_devices as $pushnotification_device) {
			unset($notification);
			$notification['registration_ids'][]=$pushnotification_device['registration_id'];
			$notification['data']=array();
			$notification['data']['title']=$title;
			$notification['data']['body']=$body;
			$notification['data']['actionurl']=$actionurl;

			$headers = array('Content-Type' => 'application/json','Authorization' => "key=".$config['fcm_server_key']);
			$response = Requests::post('https://fcm.googleapis.com/fcm/send', $headers, json_encode($notification));
		};

	};
?>
