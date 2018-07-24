<?php
//I contain all the routes/functions used by administrators to manage parks

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use Aws\S3\S3Client;

$app->get('/admin/parks/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);


		web_require_permission("ADMIN");
		web_require_permission("ADMIN_PARKS");

		$parks=DB::query("SELECT * FROM parks");
		$smarty->assign('parks',$parks);
		$smarty->display('admin/parks/parks.tpl');
	};
});

$app->get('/admin/parks/{park_id}/edit/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {

		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		web_require_permission("ADMIN");
		web_require_permission("ADMIN_PARKS");
		web_require_permission("ADMIN_PARKS_EDIT");

		$park=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$args['park_id']);
		if (!$park) {
			$smarty->assign('error_code',"park_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('park',$park);
		$smarty->display('admin/parks/park_edit.tpl');
	};
});

$app->post('/admin/parks/{park_id}/edit/', function (Request $request, Response $response, array $args) {
	global $smarty,$config,$s3client;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		web_require_permission("ADMIN");
		web_require_permission("ADMIN_PARKS");
		web_require_permission("ADMIN_PARKS_EDIT");

		$park=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$args['park_id']);
		if (!$park) {
			$smarty->assign('error_code',"park_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$image_content=file_get_contents($_POST['logo_url']);
		$logo_hash=sha1($image_content);

		$file_info = new finfo(FILEINFO_MIME_TYPE);
		$mime_type = $file_info->buffer($image_content);

		$s3client->putObject(array(
			'Bucket' => $config['aws_bucket'],
			'Key'    => "images/parks/".$args['park_id'],
			'Body'   => $image_content,
			'ACL'        => 'public-read',
			'ContentType'        => $mime_type
		));


		if ($_POST['map_url']) {
			$map_content=file_get_contents($_POST['map_url']);
			$map_hash=sha1($map_content);

			$file_info = new finfo(FILEINFO_MIME_TYPE);
			$mime_type = $file_info->buffer($map_content);

			$s3client->putObject(array(
				'Bucket' => $config['aws_bucket'],
				'Key'    => "maps/parks/".$args['park_id'].".pdf",
				'Body'   => $map_content,
				'ACL'        => 'public-read',
				'ContentType'        => $mime_type
			));
		} else {
			$map_hash="";
		};

		DB::update('parks', array(
			'name' => $_POST['name'],
			'slogan' => $_POST['slogan'],
			'website' => $_POST['website'],
			'logo_url' => $config['s3_assets_url']."/images/parks/".$args['park_id'],
			'logo_hash' => $logo_hash,
			'map_url' => $config['s3_assets_url']."/maps/parks/".$args['park_id'].".pdf",
			'map_hash' => $map_hash,
			'lat' => $_POST['lat'],
			'lon' => $_POST['lon'],
			'queuetimes' => $_POST['queuetimes'],
			'disabled' => $_POST['disabled']

		), "id=%i", $args['park_id']);
		header("Location: /admin/parks/");
		die();
	};
});

$app->get('/admin/parks/{park_id}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		web_require_permission("ADMIN");
		web_require_permission("ADMIN_PARKS");
		web_require_permission("ADMIN_PARKS_DELETE");

		$park=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$args['park_id']);
		if (!$park) {
			$smarty->assign('error_code',"park_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('park',$park);
		$smarty->display('admin/parks/park_delete.tpl');
	};
});

$app->post('/admin/parks/{park_id}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		web_require_permission("ADMIN");
		web_require_permission("ADMIN_PARKS");
		web_require_permission("ADMIN_PARKS_DELETE");

		$park=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$args['park_id']);
		if (!$park) {
			$smarty->assign('error_code',"park_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		DB::delete('parks', "id=%i", $args['park_id']);

		header("Location: /admin/parks/");
		die();
	};
});


$app->get('/admin/parks/add/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

		web_require_permission("ADMIN");
		web_require_permission("ADMIN_PARKS");
		web_require_permission("ADMIN_PARKS_ADD");

		$smarty->display('admin/parks/park_add.tpl');
	};
});

$app->post('/admin/parks/add/', function (Request $request, Response $response, array $args) {
	global $smarty,$config,$s3client;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);


		web_require_permission("ADMIN");
		web_require_permission("ADMIN_PARKS");
		web_require_permission("ADMIN_PARKS_ADD");

		DB::insert('parks', array(
			'name' => $_POST['name'],
			'slogan' => $_POST['slogan'],
			'website' => $_POST['website'],
			'logo_url' => $_POST['logo_url'],
			'lat' => $_POST['lat'],
			'lon' => $_POST['lon'],
			'queuetimes' => $_POST['queuetimes'],
			'disabled' => 1
		));

		$args['park_id']=DB::insertId();

		$image_content=file_get_contents($_POST['logo_url']);
		$logo_hash=sha1($image_content);

		$file_info = new finfo(FILEINFO_MIME_TYPE);
		$mime_type = $file_info->buffer($image_content);

		$s3client->putObject(array(
			'Bucket' => $config['aws_bucket'],
			'Key'    => "images/parks/".$args['park_id'],
			'Body'   => $image_content,
			'ACL'        => 'public-read',
			'ContentType'        => $mime_type
		));
		DB::update('parks', array(
			'logo_url' => $config['s3_assets_url']."/images/parks/".$args['park_id'],
			'logo_hash' => $logo_hash
		), "id=%i", $args['park_id']);

		header("Location: /admin/parks/".$args['park_id']);
		die();
	};
});
?>
