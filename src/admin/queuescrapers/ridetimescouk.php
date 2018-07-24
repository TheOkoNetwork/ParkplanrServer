<?php
//I contain all the routes/functions used by administrators to manage the ridetimes.co.uk scraper

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->group('/admin/queuescrapers/ridetimescouk', function() {
	$this->get('/', function (Request $request, Response $response, array $args) {
		global $smarty;
		$smarty->assign('rides',DB::query("SELECT queuescrapers_ridetimescouk_rides.ride_id,rides.* FROM rides INNER JOIN queuescrapers_ridetimescouk_rides ON rides.id = queuescrapers_ridetimescouk_rides.ride"));
		return $response->getBody()->write($smarty->fetch('admin/queuescrapers/ridetimescouk/ridetimescouk_rides.tpl'));
	});

	$this->get('/{ride_id:[0-9]+}/edit/', function (Request $request, Response $response, array $args) {
		global $smarty, $config;
		$ride=DB::queryFirstRow("SELECT queuescrapers_ridetimescouk_rides.ride_id,rides.* FROM rides INNER JOIN queuescrapers_ridetimescouk_rides ON rides.id = queuescrapers_ridetimescouk_rides.ride WHERE rides.id=%i",$args['ride_id']);
		if (!$ride) {	display_authenticated_error("RIDE_NOT_FOUND");	};
		$smarty->assign('ride',$ride);

		$smarty->assign('rides',DB::query("SELECT * FROM rides WHERE park=%i",$config['ridetimescouk_park']));
		return $response->getBody()->write($smarty->fetch('admin/queuescrapers/ridetimescouk/ridetimescouk_rides_edit.tpl'));
	});

	$this->post('/{ride_id:[0-9]+}/edit/', function (Request $request, Response $response, array $args) {
		global $smarty, $config, $s3client;
		$ride=DB::queryFirstRow("SELECT queuescrapers_ridetimescouk_rides.ride_id,rides.* FROM rides INNER JOIN queuescrapers_ridetimescouk_rides ON rides.id = queuescrapers_ridetimescouk_rides.ride WHERE rides.id=%i",$args['ride_id']);
		if (!$ride) {	display_authenticated_error("RIDE_NOT_FOUND");	};

		DB::update('queuescrapers_ridetimescouk_rides', array(
			'ride' => $_POST['ride'],
			'ride_id' => $_POST['ride_id']
		),"ride=%i",$ride['id']);
		redirect("/admin/queuescrapers/ridetimescouk/");
	});

	$this->get('/{ride_id:[0-9]+}/delete/', function (Request $request, Response $response, array $args) {
		global $smarty;
		$ride=DB::queryFirstRow("SELECT queuescrapers_ridetimescouk_rides.ride_id,rides.* FROM rides INNER JOIN queuescrapers_ridetimescouk_rides ON rides.id = queuescrapers_ridetimescouk_rides.ride WHERE rides.id=%i",$args['ride_id']);
		if (!$ride) {	display_authenticated_error("RIDE_NOT_FOUND");	};
		$smarty->assign('ride',$ride);
		return $response->getBody()->write($smarty->fetch('admin/queuescrapers/ridetimescouk/ridetimescouk_rides_delete.tpl'));
	});

	$this->post('/{ride_id:[0-9]+}/delete/', function (Request $request, Response $response, array $args) {
		global $smarty;
		$ridetimescouk_ride=DB::queryFirstRow("SELECT queuescrapers_ridetimescouk_rides.id FROM rides INNER JOIN queuescrapers_ridetimescouk_rides ON rides.id = queuescrapers_ridetimescouk_rides.ride WHERE rides.id=%i",$args['ride_id']);
		if (!$ridetimescouk_ride) {	display_authenticated_error("RIDE_NOT_FOUND");	};
		DB::delete('queuescrapers_ridetimescouk_rides', "id=%i", $ridetimescouk_ride['id']);
		redirect("/");
	});


	$this->get('/add/', function (Request $request, Response $response, array $args) {
		global $smarty, $config;
		$rides=DB::query("SELECT * FROM rides WHERE park=%i",$config['ridetimescouk_park']);
		$smarty->assign('rides',$rides);
		return $response->getBody()->write($smarty->fetch('admin/queuescrapers/ridetimescouk/ridetimescouk_rides_add.tpl'));
	});

	$this->post('/add/', function (Request $request, Response $response, array $args) {
		global $smarty, $config;
		$ride=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$_POST['ride']);
		if (!$ride) {	display_authenticated_error("RIDE_NOT_FOUND");	};

		$existing_pairing=DB::queryFirstRow("SELECT * FROM queuescrapers_ridetimescouk_rides WHERE ride=%i OR ride_id=%i",$_POST['ride'],$_POST['ride_id']);
		if ($existing_pairing) {
			display_authenticated_error("EXISTING_PAIRING");
		};

		DB::insert('queuescrapers_ridetimescouk_rides', array(
			'ride' => $ride['id'],
			'ride_id' => $_POST['ride_id']
		));
		redirect("/");
	});


})->add(function ($request, $response, $next) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		redirect('/');
	} else {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);
                web_require_permission("ADMIN");
                web_require_permission("ADMIN_QUEUESCRAPERS");
                web_require_permission("ADMIN_QUEUESCRAPERS_RIDETIMESCOUK");
	};
	return $next($request, $response);
});
?>
