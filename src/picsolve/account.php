<?php
	//I handle the web UI side of picsolve account stuff
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app->get('/picsolve/account/', function (Request $request, Response $response, array $args) {
	        global $smarty, $config;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);
	                $smarty->display('picsolve/account.tpl');
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});


	$app->post('/picsolve/account/', function (Request $request, Response $response, array $args) {
	        global $smarty, $config;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();
	                $smarty->assign('user',$_SESSION['parkplanr']['user']);

			//now check the picsolve account
			$headers = array('Content-Type' => 'application/json', 'User-Agent' => \Campo\UserAgent::random());
			$data = array('email' => $_POST['picsolve_email'], 'password' => $_POST['picsolve_password']);
			$picsolve_request=Requests::post('https://www.picsolve.com/user/login', $headers, json_encode($data),$config['requestoptions']);
			$picsolve_response = json_decode($picsolve_request->body,true);
			if ($picsolve_response['success']) {
				DB::update('users', array(
					'picsolve_auth_token' => $picsolve_response['data']['authToken'],
					'picsolve_signedin' => time()
                                ), "id=%i", $_SESSION['parkplanr']['user']['id']);
				redirect("/picsolve/account");
			} else {
		                $smarty->assign('error',true);
		                $smarty->display('picsolve/account.tpl');
                        };
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});


	$app->get('/picsolve/account/unlink/', function (Request $request, Response $response, array $args) {
	        global $smarty, $config;
	        if (isset($_SESSION['parkplanr']['user'])) {
	                update_user_session();

			if (!$_SESSION['parkplanr']['user']['picsolve_auth_token']) {
                        	redirect('/picsolve/account');
	                };

	                $smarty->assign('user',$_SESSION['parkplanr']['user']);
	                $smarty->display('picsolve/account_unlink.tpl');
	        } else {
	                header("Location: /signin");
	                die();
	        };
	});

?>
