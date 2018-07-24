<?php
//I contain functions for handling/updating sessions and the routes for the authentication flow (sign up/in and verification/reset emails)

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

function update_user_session() {
	$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $_SESSION['parkplanr']['user']['id']);
	if ($user) {
                bugsnag_report_user($user);

		unset($user['password']);
		if (empty($user['name'])) {
			$user['name']=$user['email'];
		};

		$size=40;
		$user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) ) . "&s=" . $size;

		$_SESSION['parkplanr']['user']=$user;
	} else {
		header("Location: /signout");
		die();
	};
};

function start_user_session($user_id) {
	global $smarty, $config, $mg;
	$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $user_id);
	if ($user) {
		unset($user['password']);
		if (empty($user['name'])) {
			$user['name']=$user['email'];
		};
		unset($_SESSION['parkplanr']);
		$_SESSION['parkplanr']['user']=$user;
		return true;
	} else {
		return false;
	};
};

$app->get('/signout/', function (Request $request, Response $response, array $args) {
	unset($_SESSION['parkplanr']);
	header("Location: /signin");
	die();
});

$app->get('/verifyemail/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $mg;

	$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i AND email_verification_token=%s", $_GET['uid'],$_GET['token']);
	if ($user) {
		DB::update('users', array(
			'email_verified' => true,
			'email_verification_token' => ""
		), "id=%i", $user['id']);

		unset($user['password']);
		if (empty($user['name'])) {
			$user['name']=$user['email'];
		};

		$smarty->assign("user_uid_hash",hash('ripemd160', $user['id']));
		$smarty->assign('user',$user);
		$email_text=$smarty->fetch('emails/text/email_verified.tpl');
		$email_html=$smarty->fetch('emails/email_verified.tpl');

		$mg->messages()->send($config['email_domain'], [
			'from'    => $config['app_support_email'],
			'to'      => $user['email'],
			'subject' => 'Email address verified',
			'text'    => $email_text,
			'html'    => $email_html
		]);


		$smarty->display('email_verified.tpl');
		die();
	} else {
		$smarty->display('email_verify_fail.tpl');
		die();
	};
});


require_once('src/authenticationflows/magiclink.php');
require_once('src/authenticationflows/emailpassword.php');
require_once('src/authenticationflows/passwordreset.php');
require_once('src/authenticationflows/firebase.php');
