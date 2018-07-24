<?php
//I contain all the routes/functions used by administrators to manage rides

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;

$app->get('/admin/rides/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect("/app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");

//		$rides=DB::query("SELECT * FROM rides");
  //              Black Pool Pleasure Beach
		$rides=DB::query("SELECT * FROM rides WHERE park=%i",18);
		foreach ($rides as &$ride) {
			$ride['park']=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$ride['park']);
		}
		$smarty->assign('rides',$rides);
		$smarty->display('admin/rides/rides.tpl');
	};
});

$app->get('/admin/rides/{ride_id:[0-9]+}/edit/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect("/app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");
                web_require_permission("ADMIN_RIDES_EDIT");

		$ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$args['ride_id']);
		if (!$ride) {
			$smarty->assign('error_code',"RIDE_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('ride',$ride);

		$parks=DB::query("SELECT * FROM parks");
		$smarty->assign('parks',$parks);
		$smarty->display('admin/rides/ride_edit.tpl');
	};
});

$app->post('/admin/rides/{ride_id:[0-9]+}/edit/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $s3client;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect("/app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");
                web_require_permission("ADMIN_RIDES_EDIT");

		$park=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$_POST['park']);
		if (!$park) {
			$smarty->assign('error_code',"park_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};


		if ($_POST['logo_url']) {
			$image_content=file_get_contents($_POST['logo_url']);
	                $logo_hash=sha1($image_content);

	                $file_info = new finfo(FILEINFO_MIME_TYPE);
	                $mime_type = $file_info->buffer($image_content);

	                $s3client->putObject(array(
	                        'Bucket' => $config['aws_bucket'],
	                        'Key'    => "images/rides/".$args['ride_id'],
	                        'Body'   => $image_content,
	                        'ACL'        => 'public-read',
	                        'ContentType'        => $mime_type
	                ));

			$logo_url=$config['s3_assets_url']."/images/rides/".$args['ride_id'];
		} else {
			$logo_url="";
			$logo_hash="";
		};

		DB::update('rides', array(
			'name' => $_POST['name'],
			'slogan' => $_POST['slogan'],
			'park' => $_POST['park'],
			'logo_url' => $logo_url,
                        'logo_hash' => $logo_hash,
                        'queuetimes' => $_POST['queuetimes'],
                        'ridecount' => $_POST['ridecount'],
                        'queuetimes_ride_id' => $_POST['queuetimes_ride_id'],
                        'disabled' => $_POST['disabled']
		), "id=%i", $args['ride_id']);

//		header("Location: /admin/rides/".$args['ride_id']);
		header("Location: /admin/rides/");
		die();
	};
});

$app->get('/admin/rides/{ride_id:[0-9]+}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect("/app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");
                web_require_permission("ADMIN_RIDES_DELETE");

		$ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$args['ride_id']);
		if (!$ride) {
			$smarty->assign('error_code',"RIDE_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('ride',$ride);
		$smarty->display('admin/rides/ride_delete.tpl');
	};
});

$app->post('/admin/rides/{ride_id:[0-9]+}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect("/app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");
                web_require_permission("ADMIN_RIDES_DELETE");

		$ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$args['ride_id']);
		if (!$ride) {
			$smarty->assign('error_code',"RIDE_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		DB::delete('rides', "id=%i", $args['ride_id']);

		header("Location: /admin/rides/");
		die();
	};
});


$app->get('/admin/rides/add/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");
                web_require_permission("ADMIN_RIDES_ADD");

//		$parks=DB::query("SELECT * FROM parks");
//              Blackpool Pleasure Beach
		$parks=DB::query("SELECT * FROM parks WHERE ID=%i",18);
		$smarty->assign('parks',$parks);
		$smarty->display('admin/rides/ride_add.tpl');
	};
});

$app->post('/admin/rides/add/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $s3client;

	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");
                web_require_permission("ADMIN_RIDES_ADD");

		$park=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$_POST['park']);
		if (!$park) {
			$smarty->assign('error_code',"park_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		DB::insert('rides', array(
			'name' => $_POST['name'],
			'slogan' => $_POST['slogan'],
			'logo_url' => $_POST['logo_url'],
			'park' => $_POST['park'],
                        'queuetimes' => $_POST['queuetimes'],
                        'ridecount' => $_POST['ridecount'],
                        'disabled' => $_POST['disabled'],
                        'queuetimes_ride_id' => $_POST['queuetimes_ride_id'],
		));
		$args['ride_id']=DB::insertId();


		if ($_POST['logo_url']) {
			$image_content=file_get_contents($_POST['logo_url']);
	                $logo_hash=sha1($image_content);

	                $file_info = new finfo(FILEINFO_MIME_TYPE);
	                $mime_type = $file_info->buffer($image_content);

	                $s3client->putObject(array(
	                        'Bucket' => $config['aws_bucket'],
	                        'Key'    => "images/rides/".$args['ride_id'],
	                        'Body'   => $image_content,
	                        'ACL'        => 'public-read',
	                        'ContentType'        => $mime_type
	                ));

			$logo_url=$config['s3_assets_url']."/images/rides/".$args['ride_id'];
		} else {
			$logo_url="";
			$logo_hash="";
		};

		DB::update('rides', array(
			'logo_url' => $logo_url,
                        'logo_hash' => $logo_hash
		), "id=%i", $args['ride_id']);

//		redirect("/admin/rides/".$args['ride_id']);
		redirect("/admin/rides/add");
	};
});

$app->get('/admin/rides/{ride_id:[0-9]+}/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect("/app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");

		$ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$args['ride_id']);
		if (!$ride) {
			$smarty->assign('error_code',"RIDE_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$ride['park']=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$ride['park']);
		$ride['tags']=DB::query("SELECT ridetags.*, parkridetags.tag, parkridetags.queuetimes, parkridetags.ridecount, parkridetags.area FROM ridetags INNER JOIN parkridetags ON ridetags.tag = parkridetags.id WHERE ridetags.ride = %i",$ride['id']);
		$smarty->assign('ride',$ride);


		$parkridetags=DB::query("SELECT * FROM parkridetags WHERE park=%i",$ride['park']['id']);
		$smarty->assign('parkridetags',$parkridetags);
		$smarty->display('admin/rides/ride_view.tpl');
	};
});

$app->post('/admin/rides/{ride_id:[0-9]+}/tags/add/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect("/app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");
                web_require_permission("ADMIN_RIDES_EDIT");

		$ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$args['ride_id']);
		if (!$ride) {
			$smarty->assign('error_code',"RIDE_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		$parkridetagride=DB::queryFirstRow("SELECT * FROM parkridetags WHERE id=%i",$_POST['tag']);
		if (!$parkridetagride) {
			$smarty->assign('error_code',"TAG_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		DB::insert('ridetags', array(
			'ride' => $args['ride_id'],
			'tag' => $_POST['tag']
		));

		header("Location: /admin/rides/".$args['ride_id']);
		die();
	};
});


///

///


///



$app->get('/admin/rides/{ride_id:[0-9]+}/tags/{tag_id:[0-9]+}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect("/app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");
                web_require_permission("ADMIN_RIDES_EDIT");

		$ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$args['ride_id']);
		if (!$ride) {
			$smarty->assign('error_code',"RIDE_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$ride['tag']=DB::queryFirstRow("SELECT ridetags.*, parkridetags.tag, parkridetags.queuetimes, parkridetags.ridecount, parkridetags.area FROM ridetags INNER JOIN parkridetags ON ridetags.tag = parkridetags.id WHERE ridetags.id = %i",$args['tag_id']);
		$smarty->assign('ride',$ride);
		$smarty->display('admin/rides/ride_tag_delete.tpl');
	};
});

$app->post('/admin/rides/{ride_id:[0-9]+}/tags/{tag_id:[0-9]+}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect("/app");
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_RIDES");
                web_require_permission("ADMIN_RIDES_EDIT");

		$ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$args['ride_id']);
		if (!$ride) {
			$smarty->assign('error_code',"RIDE_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		DB::delete('rides', "id=%i", $args['ride_id']);

		header("Location: /admin/rides/");
		die();
	};
});
?>
