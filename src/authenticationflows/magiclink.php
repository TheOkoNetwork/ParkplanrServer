<?php
//I contain functions for magiclink generation and usage

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

function magiclink_generate($magiclink_user_id,$post_url) {
	global $config;
	$magiclink_user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i",$magiclink_user_id);
	if ($magiclink_user) {
		DB::insert('magiclinks', array(
			'user' => $magiclink_user['id'],
			'created' => time(),
			'post_url' => $post_url
		));
		$token = array(
			"magiclinkuserid" => $magiclink_user['id'],
			"magiclinkid" => DB::insertId(),
			"exp" => time()+86400
		);
		$jwt = JWT::encode($token, $config['jwt_private_key'], 'RS256');
		return $jwt;
	} else {
		return false;
	};
};

$app->get('/magiclink/use/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $mg;


	if (!isset($_GET['token'])) {
		$smarty->display('magiclink_invalid.tpl');
		die();
	};
	try {
		$decoded = JWT::decode($_GET['token'], $config['jwt_public_key'], array('RS256'));
		$decoded_array = (array) $decoded;
        } catch(Exception $e) {
		$smarty->display('magiclink_invalid.tpl');
		die();
	};
	$magiclink = DB::queryFirstRow("SELECT * FROM magiclinks WHERE id=%i", $decoded_array['magiclinkid']);
	if ($magiclink) {
		DB::delete('magiclinks', "id=%i", $magiclink['id']);
		start_user_session($magiclink['user']);
		if ($magiclink['post_url']) {
			redirect($magiclink['post_url']);
		} else {
			redirect("/app");
		};
	} else {
		$smarty->display('magiclink_invalid.tpl');
		die();
	};
});
