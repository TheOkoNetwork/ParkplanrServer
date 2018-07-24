<?php
//I contain all the routes/functions used by administrators to manage ride tags

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;

$app->get('/admin/ridetags/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDETAGS");

		$tags=DB::query("SELECT * FROM parkridetags");
		foreach ($tags as &$tag) {
			$tag['park']=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$tag['park']);
		}
		$smarty->assign('tags',$tags);
		$smarty->display('admin/ridetags/ridetags.tpl');
	};
});

$app->get('/admin/ridetags/{tag_id}/edit/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDETAGS");
                web_require_permission("ADMIN_RIDETAGS_EDIT");

		$tag=DB::queryFirstRow("SELECT * FROM parkridetags WHERE id=%i",$args['tag_id']);
		if (!$tag) {
			$smarty->assign('error_code',"TAG_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('tag',$tag);

		$parks=DB::query("SELECT * FROM parks");
		$smarty->assign('parks',$parks);
		$smarty->display('admin/ridetags/ridetag_edit.tpl');
	};
});

$app->post('/admin/ridetags/{tag_id}/edit/', function (Request $request, Response $response, array $args) {
	global $smarty, $config;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDETAGS");
                web_require_permission("ADMIN_RIDETAGS_EDIT");

		$tag=DB::queryFirstRow("SELECT * FROM parkridetags WHERE id=%i",$args['tag_id']);
		if (!$tag) {
			$smarty->assign('error_code',"TAG_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('tag',$tag);

		$park=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$_POST['park']);
		if (!$park) {
			$smarty->assign('error_code',"park_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		DB::update('parkridetags', array(
			'tag' => $_POST['tag'],
			'park' => $_POST['park'],
                        'queuetimes' => $_POST['queuetimes'],
                        'ridecount' => $_POST['ridecount'],
                        'area' => $_POST['area'],
                        'disabled' => $_POST['disabled']
		), "id=%i", $args['tag_id']);

		header("Location: /admin/ridetags/");
		die();
	};
});

$app->get('/admin/ridetags/{tag_id}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDETAGS");
                web_require_permission("ADMIN_RIDETAGS_DELETE");

		$tag=DB::queryFirstRow("SELECT * FROM parkridetags WHERE id=%i",$args['tag_id']);
		if (!$tag) {
			$smarty->assign('error_code',"TAG_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('tag',$tag);
		$smarty->display('admin/ridetags/ridetag_delete.tpl');
	};
});

$app->post('/admin/ridetags/{tag_id}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDETAGS");
                web_require_permission("ADMIN_RIDETAGS_DELETE");

		$tag=DB::queryFirstRow("SELECT * FROM parkridetags WHERE id=%i",$args['tag_id']);
		if (!$tag) {
			$smarty->assign('error_code',"TAG_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		DB::delete('parkridetags', "id=%i", $args['tag_id']);

		header("Location: /admin/ridetags/");
		die();
	};
});


$app->get('/admin/ridetags/add/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDETAGS");
                web_require_permission("ADMIN_RIDETAGS_ADD");

		$parks=DB::query("SELECT * FROM parks");
		$smarty->assign('parks',$parks);
		$smarty->display('admin/ridetags/ridetag_add.tpl');
	};
});

$app->post('/admin/ridetags/add/', function (Request $request, Response $response, array $args) {
	global $smarty, $config;

	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDETAGS");
                web_require_permission("ADMIN_RIDETAGS_ADD");

		$park=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$_POST['park']);
		if (!$park) {
			$smarty->assign('error_code',"park_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		DB::insert('parkridetags', array(
			'tag' => $_POST['tag'],
			'queuetimes' => $_POST['queuetimes'],
			'ridecount' => $_POST['ridecount'],
			'area' => $_POST['area'],
			'park' => $_POST['park'],
                        'disabled' => true
		));
		header("Location: /admin/ridetags/");
		die();
	};
});
?>
