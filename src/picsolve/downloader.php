<?php
	//I handle the web UI side of the new picsolve downloader

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;


	use Kunnu\Dropbox\Dropbox;
        use Kunnu\Dropbox\DropboxApp;

	$app->get('/picsolve/downloader/', function (Request $request, Response $response, array $args) {
	        global $smarty;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);

			$picsolvedownloader_accounts=DB::queryFirstColumn("SELECT provider FROM picsolvedownloader_accounts WHERE user=%i AND autoprocess=%i", $_SESSION['parkplanr']['user']['id'], 1);
	                $smarty->assign('picsolvedownloader_accounts',$picsolvedownloader_accounts);

	                $smarty->display('picsolvedownloader/index_new.tpl');
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});

	$app->get('/picsolve/downloader/googledrive/', function (Request $request, Response $response, array $args) {
	        global $smarty, $config;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);


			if (isset($_GET['auto'])) {
				$_SESSION['parkplanr']['AUTO_GOOGLEDRIVE']=true;
			} else {
				$_SESSION['parkplanr']['AUTO_GOOGLEDRIVE']=false;
			};

			if (isset($_GET['cancelauto'])) {
				DB::update('picsolvedownloader_accounts', array(
					'autoprocess' => false,
					'process' => true
                               	), "user=%i AND provider=%s", $_SESSION['parkplanr']['user']['id'],"GOOGLEDRIVE");
				redirect("/picsolve/downloader");
			};

			$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/picsolve/downloader/googledrive/callback';
			$client = new Google_Client();
		        $client->setAuthConfig($config['google_oauth_creds']);
			$client->setAccessType('offline');
			$client->setApprovalPrompt('force');
			$client->setRedirectUri($redirect_uri);
			$client->addScope("https://www.googleapis.com/auth/drive");
			$service = new Google_Service_Drive($client);
			redirect($client->createAuthUrl());

	        } else {
	                header("Location: /signin");
	                die();
	        };
	});





	$app->get('/picsolve/downloader/googledrive/callback/', function (Request $request, Response $response, array $args) {
	        global $smarty, $config, $mg;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);

			$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/picsolve/downloader/googledrive/callback';
			$client = new Google_Client();
		        $client->setAuthConfig($config['google_oauth_creds']);
			$client->setAccessType('offline');
			$client->setApprovalPrompt('force');
			$client->setRedirectUri($redirect_uri);
			$client->addScope("https://www.googleapis.com/auth/drive");
			$service = new Google_Service_Drive($client);

			$tokens=$client->fetchAccessTokenWithAuthCode($_GET['code']);
			if ($tokens['refresh_token']) {
				$refreshtoken=$tokens['refresh_token'];
				$accesstoken=$tokens['access_token'];
			} else {
				redirect('/picsolve/downloader/googledrive');
			};

			$picsolvedownloader_account=DB::queryFirstRow("SELECT * FROM picsolvedownloader_accounts WHERE user=%i AND provider=%s", $_SESSION['parkplanr']['user']['id'],'GOOGLEDRIVE');
			if ($picsolvedownloader_account) {
				if ($picsolvedownloader_account['process']) {
					redirect("/picsolve/downloader/googledrive/queued");
				} else {

					if ($picsolvedownloader_account['autoprocess']) {
						$autoprocess=true;
					} else {
						if ($_SESSION['parkplanr']['AUTO_GOOGLE']) {
							$autoprocess=true;
						} else {
							$autoprocess=false;
						};
					};
					DB::update('picsolvedownloader_accounts', array(
						'accesstoken' => $accesstoken,
						'refreshtoken' => $refreshtoken,
						'requested' => time(),
						'process' => true,
						'manual' => true,
						'autoprocess' => $autoprocess
                                	), "id=%i", $picsolvedownloader_account['id']);
				};
			} else {
				DB::insert('picsolvedownloader_accounts', array(
					'user' => $_SESSION['parkplanr']['user']['id'],
					'provider' => "GOOGLEDRIVE",
					'accesstoken' => $accesstoken,
					'refreshtoken' => $refreshtoken,
					'requested' => time(),
					'process' => true,
					'manual' => true,
					'autoprocess' =>  $_SESSION['parkplanr']['AUTO_GOOGLEDRIVE']
                                ));
			};


		        $smarty->assign("user_uid_hash",hash('ripemd160', $_SESSION['parkplanr']['user']['id']));
			$smarty->assign('user',$_SESSION['parkplanr']['user']);
			$smarty->assign('autoprocess',$_SESSION['parkplanr']['AUTO_GOOGLEDRIVE']);
			$email_text=$smarty->fetch('emails/text/picsolvedownloader/queued_google.tpl');
			$email_html=$smarty->fetch('emails/picsolvedownloader/queued_google.tpl');

			$mg->messages()->send($config['email_domain'], [
				'from'    => $config['app_support_email'],
				'to'      => $_SESSION['parkplanr']['user']['email'],
				'subject' => "We'll soon be saving your Picsolve photos to your Google Drive",
				'text'    => $email_text,
				'html'    => $email_html
                        ]);


			redirect("/picsolve/downloader/googledrive/queued");
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});

	$app->get('/picsolve/downloader/googledrive/queued/', function (Request $request, Response $response, array $args) {
	        global $smarty;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);
	                $smarty->display('picsolvedownloader/queued_googledrive.tpl');
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});


	$app->get('/picsolve/downloader/dropbox/', function (Request $request, Response $response, array $args) {
	        global $smarty, $config;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);

			$dropbox_app = new DropboxApp($config['dropbox_client_id'],$config['dropbox_client_secret']);
        		$dropbox = new Dropbox($dropbox_app);

			if (isset($_GET['auto'])) {
				$_SESSION['parkplanr']['AUTO_DROPBOX']=true;
			} else {
				$_SESSION['parkplanr']['AUTO_DROPBOX']=false;
			};

			if (isset($_GET['cancelauto'])) {
				DB::update('picsolvedownloader_accounts', array(
					'autoprocess' => false,
					'process' => true
                               	), "user=%i AND provider=%s", $_SESSION['parkplanr']['user']['id'],"DROPBOX");
				redirect("/picsolve/downloader");
			};

			//DropboxAuthHelper
			$authHelper = $dropbox->getAuthHelper();
			//Callback URL
			$callbackUrl = "https://" . $_SERVER['HTTP_HOST'] . "/picsolve/downloader/dropbox/callback";
			redirect($authHelper->getAuthUrl($callbackUrl));
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});


	$app->get('/picsolve/downloader/dropbox/callback/', function (Request $request, Response $response, array $args) {
	        global $smarty, $mg, $smarty, $config;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);

			$dropbox_app = new DropboxApp($config['dropbox_client_id'],$config['dropbox_client_secret']);
        		$dropbox = new Dropbox($dropbox_app);

			//DropboxAuthHelper
			$authHelper = $dropbox->getAuthHelper();
			//Callback URL
			$callbackUrl = "https://" . $_SERVER['HTTP_HOST'] . "/picsolve/downloader/dropbox/callback";


			$code = $_GET['code'];
			$state = $_GET['state'];
			try {
				$accesstoken = $authHelper->getAccessToken($code, $state, $callbackUrl)->getToken();

				$picsolvedownloader_account=DB::queryFirstRow("SELECT * FROM picsolvedownloader_accounts WHERE user=%i AND provider=%s", $_SESSION['parkplanr']['user']['id'],'DROPBOX');
				if ($picsolvedownloader_account) {
					if ($picsolvedownloader_account['process']) {
						redirect("/picsolve/downloader/dropbox/queued");
					} else {
						//dropbox does not use refresh tokens, access tokens live until manually removed.

						if ($picsolvedownloader_account['autoprocess']) {
							$autoprocess=true;
						} else {
							if ($_SESSION['parkplanr']['AUTO_DROPBOX']) {
								$autoprocess=true;
							} else {
								$autoprocess=false;
							};
						};

						DB::update('picsolvedownloader_accounts', array(
							'accesstoken' => $accesstoken,
							'refreshtoken' => "",
							'requested' => time(),
							'process' => true,
							'manual' => true,
							'autoprocess' => $autoprocess
	                                	), "id=%i", $picsolvedownloader_account['id']);
					};
				} else {
					//dropbox does not use refresh tokens, access tokens live until manually removed.
					DB::insert('picsolvedownloader_accounts', array(
						'user' => $_SESSION['parkplanr']['user']['id'],
						'provider' => "DROPBOX",
						'accesstoken' => $accesstoken,
						'refreshtoken' => "",
						'requested' => time(),
						'process' => true,
						'manual' => true,
						'autoprocess' => $_SESSION['parkplanr']['AUTO_DROPBOX']
	                                ));
				};


			        $smarty->assign("user_uid_hash",hash('ripemd160', $_SESSION['parkplanr']['user']['id']));
				$smarty->assign('user',$_SESSION['parkplanr']['user']);
				$smarty->assign('autoprocess',$_SESSION['parkplanr']['AUTO_DROPBOX']);
				$email_text=$smarty->fetch('emails/text/picsolvedownloader/queued_dropbox.tpl');
				$email_html=$smarty->fetch('emails/picsolvedownloader/queued_dropbox.tpl');

				$mg->messages()->send($config['email_domain'], [
					'from'    => $config['app_support_email'],
					'to'      => $_SESSION['parkplanr']['user']['email'],
					'subject' => "We'll soon be saving your Picsolve photos to your Dropbox",
					'text'    => $email_text,
					'html'    => $email_html
	                        ]);


				redirect("/picsolve/downloader/dropbox/queued");

			} catch (Exception $e) {
				redirect("/picsolve/downloader/dropbox/failed");
			};
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});

	$app->get('/picsolve/downloader/dropbox/failed/', function (Request $request, Response $response, array $args) {
	        global $smarty;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);
	                $smarty->display('picsolvedownloader/failed_dropbox.tpl');
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});


	$app->get('/picsolve/downloader/dropbox/queued/', function (Request $request, Response $response, array $args) {
	        global $smarty;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);
	                $smarty->display('picsolvedownloader/queued_dropbox.tpl');
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});


	$app->get('/picsolve/downloader/onedrive/', function (Request $request, Response $response, array $args) {
		die();
	        global $smarty, $config;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);

			if (isset($_GET['auto'])) {
				$_SESSION['parkplanr']['AUTO_ONEDRIVE']=true;
			} else {
				$_SESSION['parkplanr']['AUTO_ONEDRIVE']=false;
			};
			if (isset($_GET['cancelauto'])) {
				DB::update('picsolvedownloader_accounts', array(
					'autoprocess' => false,
					'process' => true
                               	), "user=%i AND provider=%s", $_SESSION['parkplanr']['user']['id'],"ONEDRIVE");
				redirect("/picsolve/downloader");
			};

			$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/picsolve/downloader/onedrive/callback';

			$provider = new Stevenmaguire\OAuth2\Client\Provider\Microsoft([
			    // Required
			    'clientId'                  => $config['onedrive_client_id'],
			    'clientSecret'              => $config['onedrive_client_secret'],
			    'redirectUri'               => $redirect_uri
			]);

			$options = [
				'scope' => ['wl.basic', 'wl.signin', 'wl.skydrive_update','offline_access'],
			];
			$authUrl = $provider->getAuthorizationUrl($options);
			$_SESSION['parkplanr']['onedrive_oauth2state'] = $provider->getState();
			redirect($authUrl);
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});

	$app->get('/picsolve/downloader/onedrive/callback/', function (Request $request, Response $response, array $args) {
		die();
	        global $smarty, $config, $bugsnag, $mg;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);

			if (isset($_GET['error'])) {
				redirect("/picsolve/downloader/onedrive/failed/");
			};

			if (!isset($_GET['code'])) {
				redirect("/picsolve/downloader/onedrive");
			};
			$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/picsolve/downloader/onedrive/callback';
			$provider = new Stevenmaguire\OAuth2\Client\Provider\Microsoft([
			    // Required
			    'clientId'                  => $config['onedrive_client_id'],
			    'clientSecret'              => $config['onedrive_client_secret'],
			    'redirectUri'               => $redirect_uri
			]);
			try {
				$token = $provider->getAccessToken('authorization_code', [
					'code' => $_GET['code']
				]);

				$accesstoken=$token->getToken();
				$refreshtoken=$token->getRefreshToken();

				$picsolvedownloader_account=DB::queryFirstRow("SELECT * FROM picsolvedownloader_accounts WHERE user=%i AND provider=%s", $_SESSION['parkplanr']['user']['id'],'ONEDRIVE');
				if ($picsolvedownloader_account) {
					if ($picsolvedownloader_account['process']) {
						redirect("/picsolve/downloader/onedrive/queued");
					} else {
						if ($picsolvedownloader_account['autoprocess']) {
							$autoprocess=true;
						} else {
							if ($_SESSION['parkplanr']['AUTO_ONEDRIVE']) {
								$autoprocess=true;
							} else {
								$autoprocess=false;
							};
						};
						DB::update('picsolvedownloader_accounts', array(
							'accesstoken' => $accesstoken,
							'refreshtoken' => $refreshtoken,
							'requested' => time(),
							'process' => true,
							'manual' => true,
							'autoprocess' => $autoprocess
        	                        	), "id=%i", $picsolvedownloader_account['id']);
					};
				} else {
					DB::insert('picsolvedownloader_accounts', array(
						'user' => $_SESSION['parkplanr']['user']['id'],
						'provider' => "ONEDRIVE",
						'accesstoken' => $accesstoken,
						'refreshtoken' => $refreshtoken,
						'requested' => time(),
						'process' => true,
						'manual' => true,
						'autoprocess' =>  $_SESSION['parkplanr']['AUTO_ONEDRIVE']
        	                        ));
				};


			        $smarty->assign("user_uid_hash",hash('ripemd160', $_SESSION['parkplanr']['user']['id']));
				$smarty->assign('autoprocess',$_SESSION['parkplanr']['AUTO_ONEDRIVE']);
				$smarty->assign('user',$_SESSION['parkplanr']['user']);
				$email_text=$smarty->fetch('emails/text/picsolvedownloader/queued_onedrive.tpl');
				$email_html=$smarty->fetch('emails/picsolvedownloader/queued_onedrive.tpl');

				$mg->messages()->send($config['email_domain'], [
					'from'    => $config['app_support_email'],
					'to'      => $_SESSION['parkplanr']['user']['email'],
					'subject' => "We'll soon be saving your Picsolve photos to your Onedrive",
					'text'    => $email_text,
					'html'    => $email_html
	                        ]);
				redirect("/picsolve/downloader/onedrive/queued");
			} catch (Exception $exception) {
				$bugsnag->notifyException($exception);
				if ($_SESSION['parkplanr']['AUTO_ONEDRIVE']) {
					redirect("/picsolve/downloader/onedrive?auto=auto");
				} else {
					redirect("/picsolve/downloader/onedrive/");
				};
			};

	        } else {
	                header("Location: /signin");
	                die();
	        };
	});

	$app->get('/picsolve/downloader/onedrive/failed/', function (Request $request, Response $response, array $args) {
		die();
	        global $smarty;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);
	                $smarty->display('picsolvedownloader/failed_onedrive.tpl');
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});

	$app->get('/picsolve/downloader/onedrive/queued/', function (Request $request, Response $response, array $args) {
		die();
	        global $smarty;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);
	                $smarty->display('picsolvedownloader/queued_onedrive.tpl');
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});

?>
