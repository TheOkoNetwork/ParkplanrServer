<?php
session_start();


require './vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

require './config.php';
require './commonincludes.php';

//setup slim framework
$c = new \Slim\Container();
$app = new \Slim\App($c);
unset($app->getContainer()['errorHandler']);
unset($app->getContainer()['phpErrorHandler']);

//makes a url like /picsolve/digipass get treated as /login/facebook/ as a result all routes need to end in /
//so routes match correctly(aka at all) to requests

use Psr7Middlewares\Middleware\TrailingSlash;
$app->add(new TrailingSlash(true));

//setup smarty template engine
$smarty = new Smarty;

function display_authenticated_error($error_code) {
	$smarty->assign('error_code',$error_code);
	$smarty->display('error_authenticated.tpl');
	die();
};
function redirect($to) {
	header("Location: ".$to);
	die();
};

if (isset($_SESSION['parkplanr']['user'])) {
	$bugsnag->registerCallback(function ($report) {
	    $report->setUser([
	        'id' => $_SESSION['parkplanr']['user']['id'],
	        'name' => $_SESSION['parkplanr']['user']['name'],
	        'email' => $_SESSION['parkplanr']['user']['email']
	    ]);
	});
};


$app->get('/', function (Request $request, Response $response, array $args) {
	redirect('/site');
});

$app->get('/picsolvelegaldispute/', function (Request $request, Response $response, array $args) {
	redirect('/site/2018/06/14/the-situation-so-far/');
});

$app->get('/app/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (isset($_SESSION['parkplanr']['user'])) {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);
		$smarty->display('index.tpl');
	} else {
		header("Location: /signin");
		die();
	};
});

$app->get('/migration_required_notification/', function (Request $request, Response $response, array $args) {
	global $smarty;
	$smarty->display('migration_required_notification.tpl');
});

$app->get('/picsolvesignin_notification/', function (Request $request, Response $response, array $args) {
	global $smarty;
	$smarty->display('picsolvesignin_notification.tpl');
});

$app->get('/profile/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (isset($_SESSION['parkplanr']['user'])) {
		update_user_session();
		unset($_SESSION['parkplanr']['user']['firebase_uid']);
		$smarty->assign('user',$_SESSION['parkplanr']['user']);
		$smarty->display('profile.tpl');
	} else {
		header("Location: /signin");
		die();
	};
});

$app->get('/emailverificationrequired/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (isset($_SESSION['parkplanr']['user'])) {
		header("Location: /signin");
		die();
	} else {
		$smarty->display('emailverificationrequired.tpl');
	};
});


$app->get('/ridecount/{trip_id}/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (isset($_SESSION['parkplanr']['user'])) {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);


	        $ridecount_trip = DB::queryFirstRow("SELECT * FROM ridecount_trips WHERE user=%i AND id=%i", $_SESSION['parkplanr']['user']['id'],$args['trip_id']);
		$ridecount_trip['park']=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$ridecount_trip['park']);
		$ridecount_trip['total_rides']=DB::queryFirstField("SELECT SUM(ride_count) FROM ridecount_rides WHERE trip=%i",$ridecount_trip['id']);
		if (!$ridecount_trip['total_rides']) {
			$ridecount_trip['total_rides']=0;
		};
		$smarty->assign('ridecount_trip',$ridecount_trip);

	        $ridecount_rides = DB::query("SELECT * FROM ridecount_rides WHERE trip=%i", $ridecount_trip['id']);
                foreach ($ridecount_rides as &$ride) {
                        $ride['ride']=DB::queryFirstRow("SELECT * FROM rides WHERE id=%i",$ride['ride_id']);
		};
		$smarty->assign('ridecount_rides',$ridecount_rides);

		$smarty->display('userweb/ridecount_rides.tpl');
		die();
	} else {
		header("Location: /signin");
		die();
	};
});

$app->get('/ridecount/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (isset($_SESSION['parkplanr']['user'])) {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);


	        $ridecount_trips = DB::query("SELECT * FROM ridecount_trips WHERE user=%i", $_SESSION['parkplanr']['user']['id']);
                foreach ($ridecount_trips as &$trip) {
                        $trip['park']=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$trip['park']);
                        $trip['total_rides']=DB::queryFirstField("SELECT SUM(ride_count) FROM ridecount_rides WHERE trip=%i",$trip['id']);
			if (!$trip['total_rides']) {
				$trip['total_rides']=0;
			};
                }


		$smarty->assign('ridecount_trips',$ridecount_trips);

		$smarty->display('userweb/ridecount_trips.tpl');
		die();
	} else {
		header("Location: /signin");
		die();
	};
});




$app->get('/admin/ridecount/{user_id}/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (isset($_SESSION['parkplanr']['user'])) {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);


	        $ridecount_trips = DB::query("SELECT * FROM ridecount_trips WHERE user=%i", $args['user_id']);
                foreach ($ridecount_trips as &$trip) {
                        $trip['park']=DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$trip['park']);
                        $trip['total_rides']=DB::queryFirstField("SELECT SUM(ride_count) FROM ridecount_rides WHERE trip=%i",$trip['id']);
			if (!$trip['total_rides']) {
				$trip['total_rides']=0;
			};
                }


		$smarty->assign('ridecount_trips',$ridecount_trips);

		$smarty->display('admin/ridecount_trips.tpl');
		die();
	} else {
		header("Location: /signin");
		die();
	};
});

$app->get('/admin/ridecountcopy/{trip_id}/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (isset($_SESSION['parkplanr']['user'])) {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);


	        $ridecount_trip = DB::queryFirstRow("SELECT * FROM ridecount_trips WHERE id=%i", $args['trip_id']);
		$smarty->assign('ridecount_trip',$ridecount_trip);

	        $users = DB::queryFirstRow("SELECT id,name,email FROM users");
		$smarty->assign('users',$users);

		$smarty->display('admin/ridecount_copy.tpl');
		die();
	} else {
		header("Location: /signin");
		die();
	};
});

$app->get('/admin/ridecountcopy/{from_trip}/{to_user}/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (isset($_SESSION['parkplanr']['user'])) {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);


                if (!$_SESSION['parkplanr']['user']['admin']) {
                        $smarty->assign('error_code',"NO_ADMIN_PERMS");
                        $smarty->display('error_authenticated.tpl');
                        die();
                };

	        $ridecount_from_trip = DB::queryFirstRow("SELECT * FROM ridecount_trips WHERE id=%i", $args['from_trip']);

		DB::insert('ridecount_trips', array(
                       	'user' => $args['to_user'],
               	        'park' => $ridecount_from_trip['park'],
               	        'date' => $ridecount_from_trip['date'],
               	        'last_changed' => $ridecount_from_trip['last_changed']
                ));

		$ridecount_to_trip_id=DB::insertId();
	        $ridecount_rides = DB::query("SELECT * FROM ridecount_rides WHERE trip=%i", $args['from_trip']);
                foreach ($ridecount_rides as &$rides) {
			//ride_id,ride_count
			DB::insert('ridecount_rides', array(
                        	'trip' => $ridecount_to_trip_id,
                	        'ride_id' => $rides['ride_id'],
                	        'ride_count' => $rides['ride_count']
	                ));
                }

		echo "Done!";
	} else {
		header("Location: /signin");
		die();
	};
});


$app->get('/privacy/', function (Request $request, Response $response, array $args) {
	global $smarty;
	$smarty->display('privacy.tpl');
});

$app->get('/terms/', function (Request $request, Response $response, array $args) {
	global $smarty;
	$smarty->display('terms.tpl');
});


require_once('./src/picsolvefunctions.php');

require_once('./src/account_handling.php');
require_once('./src/roles.php');

// In Emergency comment out to disable admin functions
require_once('./src/admin.php');

// In Emergency comment out to disable mobile api
require_once('./src/mobileapi.php');
require_once('./src/picsolve.php');


$app->run();
?>
