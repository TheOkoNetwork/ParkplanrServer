<?php
//I contain routes that provide JSON stats for the firebase user migration

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/admin/firebasemigration/stats_json/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		web_require_permission("ADMIN");

		$result['status']=true;
		$result['timestamp']=time();
		$result['total']=DB::queryFirstColumn("SELECT COUNT(id) as count FROM `users`");
		$result['legacy']=DB::queryFirstColumn("SELECT COUNT(id) as count FROM `users` WHERE firebase_uid=%s OR firebase_uid IS NULL","");
		$result['migrated']=DB::queryFirstColumn("SELECT COUNT(id) as count FROM `users` WHERE firebase_uid!=%s AND firebase_uid IS NOT NULL","");

                return $response->withJson($result);
	};
});


$app->get('/admin/firebasemigration/graphs/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		web_require_permission("ADMIN");
		$smarty->display('admin/firebasemigration/graphs.tpl');
	};
});
