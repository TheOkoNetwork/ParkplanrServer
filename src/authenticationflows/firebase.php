<?php
//I contain functions for handling firebase based sign in on the web

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Exception\Auth\InvalidIdToken;

$app->get('/signin/', function (Request $request, Response $response, array $args) {
        global $smarty;
        if (isset($_SESSION['parkplanr']['user'])) {
                header("Location: /app");
                die();
        } else {
                $smarty->display('signin.tpl');
        };
});

$app->post('/signin/firebase/', function (Request $request, Response $response, array $args) {
	global $config, $firebase;
	if (isset($_SESSION['parkplanr']['user'])) {
		$result['status']=false;
		$result['reason']="EXISTING_SESSION";
                return $response->withJson($result);
		die();
	} else {
		try {

			$auth = $firebase->getAuth();
			$verifiedIdToken = $auth->verifyIdToken($_POST['idtoken']);
			$firebase_uid = $verifiedIdToken->getClaim('sub');
			$userInfo = $auth->getUserInfo($firebase_uid);
			$provider_info=$userInfo['providerUserInfo'][0];

			switch ($provider_info['providerId']) {
				case "facebook.com":
					$provider_tag="FACEBOOK";
					$provider_uid=$provider_info['federatedId'];
					break;
				case "google.com":
					$provider_tag="GOOGLE";
					$provider_uid=$provider_info['federatedId'];
					break;
				default:
					$provider_tag="FIREBASE_PASSWORD";
					$provider_uid=$firebase_uid;
					break;
			};
			$email=$provider_info['email'];
			if (isset($provider_info['displayName'])) {
				$name=$provider_info['displayName'];
			} else {
				$name="";
			};

                } catch(Exception $e) {
			$result['status']=false;
        	        return $response->withJson($result);
			die();
		};

		$socialprovider = DB::queryFirstRow("SELECT * FROM social_providers WHERE name=%s", $provider_tag);
                //lookup provider UID in the social_users table to look for a user
                $socialuser = DB::queryFirstRow("SELECT * FROM social_users WHERE provider=%i AND provider_uid=%s", $socialprovider['id'],$provider_uid);
                if ($socialuser) {
                        //user is found
                        //sign them in
			start_user_session($socialuser['user']);

			DB::update('users', array(
				'firebase_uid' => $firebase_uid,
				'password' => "FIREBASE_USER"
			  ), "id=%i", $socialuser['user']);

			$result['status']=true;
        	        return $response->withJson($result);
			die();
                } else {
                        //user not found
                                //lookup email in the users table
                        $user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $email);
                        if ($user) {
				if (!$userInfo['emailVerified']) {
					$result['status']=false;
					$result['redirect']="/emailverificationrequired";
		        	        return $response->withJson($result);
				};

                                //user found
                                        //create a record in social_users to link the provider_uid to their account
                                        //sign them in
                                DB::insert('social_users', array(
                                        'user' => $user['id'],
                                        'provider' => $socialprovider['id'],
                                        'provider_uid' => $provider_uid
                                ));

				start_user_session($user['id']);

				DB::update('users', array(
					'firebase_uid' => $firebase_uid,
					'password' => "FIREBASE_USER"
				  ), "id=%i", $user['id']);

				$result['status']=true;
	        	        return $response->withJson($result);
				die();
                        } else {
                                //user is not found
                                        //create user, FIREBASE_USER is just being stuck in the hash field, not being set as password
					//(im not an idiot)

                                        DB::insert('users', array(
                                                'name' => $name,
                                                'email' => $email,
                                                'password' => "FIREBASE_USER",
                                                'email_verified' => true,
						'firebase_uid' => $firebase_uid
                                        ));
                                        $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", DB::insertId());

                                        DB::insert('social_users', array(
                                                'user' => $user['id'],
                                                'provider' => $socialprovider['id'],
                                                'provider_uid' => $provider_uid
                                        ));

					start_user_session($user['id']);
					$result['status']=true;
        		        	return $response->withJson($result);
					die();
                        };
		};
	};
});
