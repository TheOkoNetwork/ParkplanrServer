<?php

//1799=last for 74
//I contain functions for handling/updating sessions and the routes for the authentication handling for the mobile api

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

function issue_jwt_for_user($user_id,$old_jti="0") {
	global $config;
	$issued=time();
	//in 12 months
	$expiry=time()+31556926;

	if (isset($_GET['appversion'])) {
		$appversion=$_GET['appversion'];
	} else {
		$appversion="";
	};

	DB::insert('tokens', array(
		'user' => $user_id,
		'issued' => $issued,
        	'expiry' => $expiry,
		'replaces' => $old_jti,
		'appversion' => $appversion
        ));
	$jti=DB::insertId();

	$token = array(
		"iss" => $config['domain'],
		"aud" => $config['domain'],
		"jti" => $jti,
		"iat" => $issued,
		"exp" => $expiry
	);
	$jwt=JWT::encode($token, $config['jwt_private_key'], 'RS256');
	if ($old_jti) {
		$oldtoken = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $old_jti);
		DB::delete('tokens', "id=%i", $oldtoken['replaces']);
	};
	return $jwt;
};

$app->post('/mobileapi/signin/', function (Request $request, Response $response, array $args) {
	global $config, $firebase;
		$user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $_POST['email']);
//		if ($user) {
//			bugsnag_report_user($user);
//
//			if (!password_verify($_POST['password'],$user['password'])) {
//				//password wrong
//				$error=true;
//			};
//			unset($user['password']);
//		} else {
//			//invalid user
//			$error=true;
//		};

                $auth = $firebase->getAuth();

		if ($user) {
			bugsnag_report_user($user);
		        if ($user['firebase_uid']) {
		                try {
		                        $firebase_user = $auth->getUserByEmailAndPassword($_POST['email'], $_POST['password']);
		                } catch (Exception $e) {
		                        //User is invalid according to firebase auth
		                        $error=true;
		                }
		        } else {
		                if (!password_verify($_POST['password'],$user['password'])) {
		                        //password wrong
		                        $error=true;
		                };
		        };
		} else {
		        //user with that email does NOT exist
		        $error=true;
		};



		if (isset($error)) {
			$result['status']=false;
		} else {
			$result['status']=true;
			if (empty($user['name'])) {
                        	$user['name']=$user['email'];
	                };
			$result['user_id']=$user['id'];
			$result['user_email']=$user['email'];
			$result['user_name']=$user['name'];
			$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
			$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
			$result['jwt']=issue_jwt_for_user($user['id']);

			if ($user['firebase_uid']) {
                                //should change users password here
                        } else {
                                $firebaseuser=$auth->createUserWithEmailAndPassword($_POST['email'],$_POST['password']);
                                $firebase_uid=$firebaseuser->getUid();
                                DB::update('users', array(
                                        'firebase_uid' => $firebase_uid
                                ), "id=%i", $user['id']);
                        };

		};
		return $response->withJson($result);
});

$app->post('/mobileapi/signin/facebook/', function (Request $request, Response $response, array $args) {
	global $config, $facebookclient;
	$access_token=$_POST['access_token'];

	try {
		// Get the \Facebook\GraphNodes\GraphUser object for the current user.
		// If you provided a 'default_access_token', the '{access-token}' is optional.
		$fbresponse = $facebookclient->get('/me?fields=id,name,email', $access_token);
		$graphuser=$fbresponse->getGraphUser();

		$name=$graphuser->getName();
		$provider_uid=$graphuser->getId();
		$email=$graphuser->getEmail();
		$socialprovider = DB::queryFirstRow("SELECT * FROM social_providers WHERE name=%s", 'FACEBOOK');

		//lookup provider UID in the social_users table to look for a user
		$socialuser = DB::queryFirstRow("SELECT * FROM social_users WHERE provider=%i AND provider_uid=%s", $socialprovider['id'],$provider_uid);
		if ($socialuser) {
			//user is found
				//sign them in
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $socialuser['user']);

			if (!$user) {
				$result['status']=false;
			} else {
	                        bugsnag_report_user($user);
				$result['status']=true;
				$result['status_human']="EXISTING_SOCIAL";
				if (empty($user['name'])) {
                        		$user['name']=$user['email'];
	                	};
				$result['user_id']=$user['id'];
				$result['user_email']=$user['email'];
				$result['user_name']=$user['name'];
				$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
				$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
				$result['jwt']=issue_jwt_for_user($user['id']);
			};
			return $response->withJson($result);
		} else {
			//user not found
				//lookup email in the users table
			$user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $email);
			if ($user) {
	                        bugsnag_report_user($user);
				//user found
					//create a record in social_users to link the provider_uid to their account
					//sign them in
				DB::insert('social_users', array(
					'user' => $user['id'],
					'provider' => $socialprovider['id'],
					'provider_uid' => $provider_uid
				));
				$result['status']=true;
				$result['status_human']="EXISTING_EMAIL";
				if (empty($user['name'])) {
                        		$user['name']=$user['email'];
	                	};
				$result['user_id']=$user['id'];
				$result['user_email']=$user['email'];
				$result['user_name']=$user['name'];
				$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
				$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
				$result['jwt']=issue_jwt_for_user($user['id']);
				return $response->withJson($result);
			} else {
				//user is not found
					//create user (password field blank)
					DB::insert('users', array(
                                		'name' => $name,
                                		'email' => $email,
                                		'password' => "SOCIAL_USER",
                                		'email_verified'=> true
                        		));
					$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", DB::insertId());
		                        bugsnag_report_user($user);

					DB::insert('social_users', array(
						'user' => $user['id'],
						'provider' => $socialprovider['id'],
						'provider_uid' => $provider_uid
					));
					$result['status']=true;
					$result['status_human']="NEW_SOCIAL";
					if (empty($user['name'])) {
                        			$user['name']=$user['email'];
	                		};
					$result['user_id']=$user['id'];
					$result['user_email']=$user['email'];
					$result['user_name']=$user['name'];
					$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
					$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
					$result['jwt']=issue_jwt_for_user($user['id']);
					return $response->withJson($result);
			};
		};

	} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
//		echo 'Graph returned an error: ' . $e->getMessage();
//		exit;
		$result['status']=false;
		return $response->withJson($result);
	} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
//		echo 'Facebook SDK returned an error: ' . $e->getMessage();
//		exit;
		$result['status']=false;
		return $response->withJson($result);
	}

});



$app->post('/mobileapi/signin/google/', function (Request $request, Response $response, array $args) {
	global $config, $googleclient;
	$id_token=$_POST['id_token'];
	error_log($id_token);
	$payload = $googleclient->verifyIdToken($id_token);
	if ($payload) {
		$name=$payload['name'];
		$provider_uid=$payload['sub'];
		$email=$payload['email'];

		$socialprovider = DB::queryFirstRow("SELECT * FROM social_providers WHERE name=%s", 'GOOGLE');

		//lookup provider UID in the social_users table to look for a user
		$socialuser = DB::queryFirstRow("SELECT * FROM social_users WHERE provider=%i AND provider_uid=%s", $socialprovider['id'],$provider_uid);
		if ($socialuser) {
			//user is found
				//sign them in
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $socialuser['user']);
			if (!$user) {
				$result['status']=false;
			} else {
	                        bugsnag_report_user($user);
				$result['status']=true;
				$result['status_human']="EXISTING_SOCIAL";
				if (empty($user['name'])) {
                        		$user['name']=$user['email'];
	                	};
				$result['user_id']=$user['id'];
				$result['user_email']=$user['email'];
				$result['user_name']=$user['name'];
				$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
				$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
				$result['jwt']=issue_jwt_for_user($user['id']);
			};
			error_log(json_encode($result));
			return $response->withJson($result);
		} else {
			//user not found
				//lookup email in the users table
			$user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $email);
			if ($user) {
	                        bugsnag_report_user($user);

				//user found
					//create a record in social_users to link the provider_uid to their account
					//sign them in
				DB::insert('social_users', array(
					'user' => $user['id'],
					'provider' => $socialprovider['id'],
					'provider_uid' => $provider_uid
				));
				$result['status']=true;
				$result['status_human']="EXISTING_EMAIL";
				if (empty($user['name'])) {
                        		$user['name']=$user['email'];
	                	};
				$result['user_id']=$user['id'];
				$result['user_email']=$user['email'];
				$result['user_name']=$user['name'];
				$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
				$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
				$result['jwt']=issue_jwt_for_user($user['id']);
				error_log(json_encode($result));
				return $response->withJson($result);
			} else {
				//user is not found
					//create user (password field blank)
					DB::insert('users', array(
                                		'name' => $name,
                                		'email' => $email,
                                		'password' => "SOCIAL_USER",
                                		'email_verified'=> true
                        		));
					$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", DB::insertId());
		                        bugsnag_report_user($user);

					DB::insert('social_users', array(
						'user' => $user['id'],
						'provider' => $socialprovider['id'],
						'provider_uid' => $provider_uid
					));
					$result['status']=true;
					$result['status_human']="NEW_SOCIAL";
					if (empty($user['name'])) {
                        			$user['name']=$user['email'];
	                		};
					$result['user_id']=$user['id'];
					$result['user_email']=$user['email'];
					$result['user_name']=$user['name'];
					$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
					$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
					$result['jwt']=issue_jwt_for_user($user['id']);
					error_log(json_encode($result));
					return $response->withJson($result);
			};
		};
	} else {
		$result['status']=false;
		error_log(json_encode($result));
		return $response->withJson($result);
	}

});

$app->get('/mobileapi/refreshtoken/', function (Request $request, Response $response, array $args) {
	global $config;
	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			unset($user['password']);
			unset($user['picsolve_auth_token']);
			if ($user) {
	                        bugsnag_report_user($user);

				$result['jwt']=issue_jwt_for_user($user['id'],$decoded['jti']);
				$result['user_id']=$user['id'];
				$result['user_email']=$user['email'];
				$result['user_name']=$user['name'];


				if ($user['profile_image']) {
					$user['gravatar_url']=$user['profile_image'];
					$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
				} else {
					$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
					$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
				};

				$result['status']=true;
				$result['status_reason']="Success. Token is valid and user exists";
				return $response->withJson($result);
			} else {
				$result['status']=false;
				$result['status_reason']="Could not find token. Possibly revoked.";
				return $response->withJson($result);
			};
		} else {
			$result['status']=false;
			$result['status_reason']="Could not find token. Possibly revoked.";
			return $response->withJson($result);
		};
	} catch (Exception $e) {
		$result['status']=false;
		$result['status_reason']="Invalid jwt";
		return $response->withJson($result);
	};
});

$app->post('/mobileapi/signup/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $mg, $firebase;

	//no checks done on name
	$smarty->assign('name',$_POST['email']);

	$email_valid=preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $_POST['email']) ? TRUE : FALSE;
	if (!$email_valid) {
		$result['status']=false;
		$result['reason']="INVALID_EMAIL";
		$result['status_reason']="Invalid email address.";
		return $response->withJson($result);
	} else {
		//email address is valid
		$smarty->assign('email',$_POST['email']);
		$user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $_POST['email']);
		if ($user) {
			//found a user with that same email
			$result['status']=false;
			$result['reason']="EXISTS";
			$result['status_reason']="There is an existing user with this email address";
			return $response->withJson($result);
		};
	};

	if (preg_match("/^.*(?=.{8,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/", $_POST["password"]) === 0) {
		//Password isnt strong enough.
		$result['status']=false;
		$result['reason']="BAD_PASSWORD";
		$result['status_reason']="Password is not strong enough";
		return $response->withJson($result);
	};

	$random = new \chriskacerguis\Randomstring\Randomstring();
	DB::insert('users', array(
		'name' => $_POST['email'],
		'email' => $_POST['email'],
		'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
		'email_verification_token' => hash('ripemd160', $_POST['email'].$random->generate(16))
	));
	$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", DB::insertId());
        bugsnag_report_user($user);

	$auth = $firebase->getAuth();
	$firebaseuser=$auth->createUserWithEmailAndPassword($_POST['email'],$_POST['password']);
	$firebase_uid=$firebaseuser->getUid();
	DB::update('users', array(
		'firebase_uid' => $firebase_uid
	), "id=%i", $user['id']);

	$smarty->assign("user_uid_hash",hash('ripemd160', $user['id']));
	$smarty->assign('user',$user);
	$email_text=$smarty->fetch('emails/text/new_account.tpl');
	$email_html=$smarty->fetch('emails/new_account.tpl');

	$mg->messages()->send($config['email_domain'], [
		'from'    => $config['app_support_email'],
		'to'      => $user['email'],
		'subject' => 'Welcome to '.$config['app_full_name'],
		'text'    => $email_text,
		'html'    => $email_html
	]);

	$result['status']=true;
	$user['name']=$user['email'];
	$result['user_id']=$user['id'];
	$result['user_email']=$user['email'];
	$result['user_name']=$user['name'];
	$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
	$result['avatar_image']='data:image/jpeg' . ';base64,' . base64_encode(file_get_contents($user['gravatar_url']));
	$result['jwt']=issue_jwt_for_user($user['id']);
	return $response->withJson($result);
});










$app->post('/mobileapi/fcmregister/', function (Request $request, Response $response, array $args) {
	global $config;
	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			unset($user['password']);
			unset($user['picsolve_auth_token']);
			if ($user) {
	                        bugsnag_report_user($user);

				$registration_id=$_POST['registration_id'];
				$registration = DB::queryFirstRow("SELECT * FROM pushnotification_devices WHERE registration_id=%s", $registration_id);
				if ($registration) {
					DB::delete('pushnotification_devices', "id=%i", $registration['id']);
				};
				DB::insert('pushnotification_devices', array(
					'user' => $user['id'],
					'registration_id' => $registration_id,
					'last_seen' => time()
				));

				$result['status']=true;
				$result['status_reason']="Success. Stored FCM registration id.";
				$result['server_registration_id']=DB::insertId();
				return $response->withJson($result);
			} else {
				$result['status']=false;
				$result['status_reason']="Could not find token. Possibly revoked.";
				return $response->withJson($result);
			};
		} else {
			$result['status']=false;
			$result['status_reason']="Could not find token. Possibly revoked.";
			return $response->withJson($result);
		};
	} catch (Exception $e) {
		$result['status']=false;
		$result['status_reason']="Invalid jwt";
		return $response->withJson($result);
	};
});





$app->post('/mobileapi/magiclink/', function (Request $request, Response $response, array $args) {
	global $config;
	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			unset($user['password']);
			unset($user['picsolve_auth_token']);
			if ($user) {
	                        bugsnag_report_user($user);

				if (isset($_POST['post_url'])) {
					$post_url=$_POST['post_url'];
				} else {
					$post_url="";
				};
                		$jwt=magiclink_generate($user['id'],$post_url);
                		if (!$jwt) {
					$result['status']=false;
					$result['status_reason']="Failed to generate MagicLink JWT.";
					return $response->withJson($result);
		                };


				$magiclink=$config['http_root']."/magiclink/use?token=$jwt";
				$result['status']=true;
				$result['status_reason']="Success. Generated MagicLink.";
				$result['magiclink']=$magiclink;
				return $response->withJson($result);
			} else {
				$result['status']=false;
				$result['status_reason']="Could not find token. Possibly revoked.";
				return $response->withJson($result);
			};
		} else {
			$result['status']=false;
			$result['status_reason']="Could not find token. Possibly revoked.";
			return $response->withJson($result);
		};
	} catch (Exception $e) {
		$result['status']=false;
		$result['status_reason']="Invalid jwt";
		return $response->withJson($result);
	};
});
?>
