<?php
//I contain functions for handling email/password routes for authentication flow

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

$app->get('/signin_legacy/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		if (isset($_GET['inapp'])) {
			$smarty->assign('inapp',true);
		} else {
			$smarty->assign('inapp',false);
		};
		$smarty->display('signin_legacy.tpl');
	};
});

$app->post('/signin_legacy/', function (Request $request, Response $response, array $args) {
	global $smarty, $firebase;
	if (isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {

		if (isset($_GET['inapp'])) {
			$smarty->assign('inapp',true);
		} else {
			$smarty->assign('inapp',false);
		};

		$user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $_POST['email']);
		if ($user) {
			if ($user['firebase_uid']) {
				$smarty->display('signin_legacy_migrated.tpl');
				die();
			} else {
				if (!password_verify($_POST['password'],$user['password'])) {
					//password wrong
					$error=true;
				};
			};
			unset($user['password']);
		} else {
			//invalid user
			$error=true;
		};


		if (isset($error)) {
			$smarty->assign('error',true);
			$smarty->display('signin_legacy.tpl');
			die();
		} else {

			$auth = $firebase->getAuth();
			if ($user['firebase_uid']) {
				$smarty->display('signin_legacy_migrated.tpl');
			} else {
				$firebaseuser=$auth->createUserWithEmailAndPassword($_POST['email'],$_POST['password']);
				$firebase_uid=$firebaseuser->getUid();
				DB::update('users', array(
					'firebase_uid' => $firebase_uid
				), "id=%i", $user['id']);
				$smarty->display('signin_legacy_migrated.tpl');
			};
		};
	};
});
