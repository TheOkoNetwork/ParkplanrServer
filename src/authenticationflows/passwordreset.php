<?php
//I contain functions for handling password reset and their routes

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

function request_user_password_reset($user_id) {
	global $smarty, $config, $mg;
	//sends the user a password reset email

	$reset_user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $user_id);
	unset($reset_user['password']);
	if ($reset_user) {
		$reset_user['password_reset_counter']=$reset_user['password_reset_counter']+1;
		DB::update('users', array(
			'password_reset_counter' => $reset_user['password_reset_counter'],
		), "id=%i", $user_id);


		$token = array(
			"resetuserid" => $user_id,
			"resetcounter" =>$reset_user['password_reset_counter'],
			"exp" => time()+86400
		);

		$jwt = JWT::encode($token, $config['jwt_private_key'], 'RS256');

		$smarty->assign('password_reset_token',urlencode($jwt));

		$smarty->assign("user_uid_hash",hash('ripemd160', $reset_user['id']));
		$smarty->assign('user',$reset_user);
		$email_text=$smarty->fetch('emails/text/password_reset_request.tpl');
		$email_html=$smarty->fetch('emails/password_reset_request.tpl');

		$mg->messages()->send($config['email_domain'], [
			'from'    => $config['app_support_email'],
			'to'      => $reset_user['email'],
			'subject' => 'Password reset requested for '.$config['app_full_name'],
			'text'    => $email_text,
			'html'    => $email_html
		]);
	} else {
		return false;
	};
};

$app->get('/forgotpassword/', function (Request $request, Response $response, array $args) {
	global $smarty;

	$smarty->display('forgot_password.tpl');
	die();
});

$app->post('/forgotpassword/', function (Request $request, Response $response, array $args) {
	global $smarty;

	$reset_user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $_POST['email']);
	if ($reset_user) {
		if ($reset_user['firebase_uid']) {
			$smarty->display('forgot_password_migrated.tpl');
			die();
		} else {
			request_user_password_reset($reset_user['id']);
			$smarty->display('forgot_password_sent.tpl');
			die();
		};
	} else {
		$smarty->assign('error',true);
		$smarty->display('forgot_password.tpl');
		die();
	};
});

$app->get('/resetpassword/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $mg;

	$decoded = JWT::decode($_GET['token'], $config['jwt_public_key'], array('RS256'));
	$decoded_array = (array) $decoded;

	$reset_user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i AND password_reset_counter=%i", $decoded_array['resetuserid'],$decoded_array['resetcounter']);
	if ($reset_user) {
		unset($reset_user['password']);
		$smarty->assign('user',$reset_user);
		$smarty->display('password_reset_new_password.tpl');
		die();
	};
});


$app->post('/resetpassword/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $mg, $firebase;

	$decoded = JWT::decode($_GET['token'], $config['jwt_public_key'], array('RS256'));
	$decoded_array = (array) $decoded;

	$reset_user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i AND password_reset_counter=%i", $decoded_array['resetuserid'],$decoded_array['resetcounter']);
	if ($reset_user) {
		unset($reset_user['password']);
		if (preg_match("/^.*(?=.{8,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/", $_POST["password"]) === 0) {
			//Password isnt strong enough.
			$error=true;
			$smarty->assign('error',true);
			$smarty->assign('password_error',"Password isn't strong enough.");
		};
		if (isset($error)) {
			$smarty->display('password_reset_new_password.tpl');
			die();
		} else {
			//If they have managed to get a valid password reset link then we know their email is valid.
			DB::update('users', array(
				'email_verified' => true,
				'email_verification_token' => "",
				'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
				'password_reset_counter' => $reset_user['password_reset_counter']+1
			), "id=%i", $reset_user['id']);

			if ($reset['firebase_uid']) {
                                $updatedUser = $auth->changeUserPassword($user['firebase_uid'], $_POST['password']);
                        } else {
                                $auth = $firebase->getAuth();
                                $firebaseuser=$auth->createUserWithEmailAndPassword($reset_user['email'],$_POST['password']);
                                $firebase_uid=$firebaseuser->getUid();
                                DB::update('users', array(
                                        'firebase_uid' => $firebase_uid
                                ), "id=%i", $reset_user['id']);
                        };


			$smarty->assign("user_uid_hash",hash('ripemd160', $reset_user['id']));
			$smarty->assign('user',$reset_user);
			$email_text=$smarty->fetch('emails/text/password_reset_success.tpl');
			$email_html=$smarty->fetch('emails/password_reset_success.tpl');

			$mg->messages()->send($config['email_domain'], [
				'from'    => $config['app_support_email'],
				'to'      => $reset_user['email'],
				'subject' => 'password has been reset for '.$config['app_full_name'],
				'text'    => $email_text,
				'html'    => $email_html
			]);

			$smarty->display('password_reset_success.tpl');
			die();
		};
	} else {
		$smarty->display('password_reset_fail.tpl');
		die();
	};
});
