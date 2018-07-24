<?php
//I contain routes that provide JSON stats for the Picsolve downloader bots

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/admin/picsolvedownloaderbots/stats_json/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		web_require_permission("ADMIN");

		$result['status']=true;
		$result['timestamp']=time();
		$result['total_queued']=DB::queryFirstColumn("SELECT COUNT(id) as count FROM `picsolvedownloader_accounts` WHERE process=%i",1);
		$result['total_processing']=DB::queryFirstColumn("SELECT COUNT(id) as count FROM `picsolvedownloader_accounts` WHERE process=%i",2);

		$result['googledrive_queued']=DB::queryFirstColumn("SELECT COUNT(id) as count FROM `picsolvedownloader_accounts` WHERE provider=%s AND process=%i","GOOGLEDRIVE",1);
		$result['googledrive_processing']=DB::queryFirstColumn("SELECT COUNT(id) as count FROM `picsolvedownloader_accounts` WHERE provider=%s AND process=%i","GOOGLEDRIVE",2);

		$result['dropbox_queued']=DB::queryFirstColumn("SELECT COUNT(id) as count FROM `picsolvedownloader_accounts` WHERE provider=%s AND process=%i","DROPBOX",1);
		$result['dropbox_processing']=DB::queryFirstColumn("SELECT COUNT(id) as count FROM `picsolvedownloader_accounts` WHERE provider=%s AND process=%i","DROPBOX",2);
                return $response->withJson($result);
	};
});


$app->get('/admin/picsolvedownloaderbots/graphs/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		web_require_permission("ADMIN");
		$smarty->display('admin/picsolvedownloaderbots/graphs.tpl');
	};
});
