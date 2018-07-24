<?php
//I handle syncing of ride counts

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

function ridecount_sync_rides($trip_id,$rides) {
	DB::delete('ridecount_rides', "trip=%i", $trip_id);
	foreach ($rides as &$ride) {
		DB::insert('ridecount_rides', array(
			'trip' => $trip_id,
			'ride_id' => $ride['ride_id'],
			'ride_count' => $ride['ride_count']
		));
	};
};

$app->post('/mobileapi/ridecount/sync/', function (Request $request, Response $response, array $args) {
        global $config;
	$trips=file_get_contents('php://input');
        $jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
        try {
                $decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
                $token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
                if ($token) {
                        $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
                        if ($user) {
			        bugsnag_report_user($user);

				$trips=json_decode($trips,true);
				foreach ($trips as &$trip) {
					if (isset($trip['deleted'])) {
						$deleted=$trip['deleted'];
					} else {
						$deleted=false;
					};
					if (isset($trip['id'])) {
						$id=$trip['id'];
					} else {
						$id=false;
					};
					$date=$trip['date'];
					$last_changed=$trip['last_changed'];
			                $park = DB::queryFirstRow("SELECT * FROM parks WHERE id=%i",$trip['park']);

					if ($park) {
						if ($deleted) {
					                $server_trip = DB::queryFirstRow("SELECT * FROM ridecount_trips WHERE id=%i AND user=%i", $id,$token['user']);
							if ($server_trip) {
//								echo "Deleted trip exists server side. Deleting from server.\n";
								DB::delete('ridecount_trips', "id=%i", $id);
								DB::delete('ridecount_rides', "trip=%i", $id);
							} else {
//								echo "Deleted trip does not exist server side. Doing nothing so it wont be returned to the client. \n";
							};
						} else {
							if ($id) {
//								echo "Trip ID found. Checking if exists on the server.\n";
						                $server_trip = DB::queryFirstRow("SELECT * FROM ridecount_trips WHERE id=%i AND user=%i", $id,$token['user']);
								if ($server_trip) {
//									echo "Server Trip found. Checking if it has been updated. \n";
									if ($last_changed > $server_trip['last_changed']) {
//										echo "Trip has changed. Updating. \n";
										ridecount_sync_rides($id,$trip['rides']);
									} else {
//										echo "Trip has not changed. Doing nothing with it. \n";
									};
								} else {
//									echo "Server Trip NOT found. It has been deleted from another device. It will be removed from the app. \n";
								};
								//die();
							} else {
						                $server_trip = DB::queryFirstRow("SELECT * FROM ridecount_trips WHERE date=%i AND park=%i AND user=%i", $date, $park['id'],$token['user']);
								if (!$server_trip) {
									DB::insert('ridecount_trips', array(
										'user' => $user['id'],
										'park' => $park['id'],
										'date' => $date,
										'last_changed' => $last_changed,
									));
									$id=DB::insertId();
									ridecount_sync_rides($id,$trip['rides']);
								};
							};
						};
					} else {
                				$result['status']=false;
        			        	$result['status_reason']="Park does not exist";
			                	return $response->withJson($result);
					};
				};

	                        $ridecount_trips = DB::query("SELECT * FROM ridecount_trips WHERE user=%i ORDER BY date ASC", $token['user']);
				foreach ($ridecount_trips as &$ridecount_trip) {
					$ridecount_trip['date']=intval($ridecount_trip['date']);
					$ridecount_trip['last_changed']=intval($ridecount_trip['last_changed']);
					$ridecount_trip['ex']="A";
		                        $ridecount_trip['rides'] = DB::query("SELECT * FROM ridecount_rides WHERE trip=%i", $ridecount_trip['id']);
					foreach ($ridecount_trip['rides'] as &$ridecount_ride) {
						$ridecount_ride['ride_count']=intval($ridecount_ride['ride_count']);
						$ridecount_ride['ride_id']=intval($ridecount_ride['ride_id']);
						$ridecount_ride['trip']=intval($ridecount_ride['trip']);
					};
				};
        		        $result['ridecount_trips']=$ridecount_trips;

        		        $result['status']=true;
        		        $result['status_reason']="Sync'd rides";
		                return $response->withJson($result);

			};
		};
        } catch (Exception $e) {
                $result['status']=false;
                $result['status_reason']="Invalid jwt";
                return $response->withJson($result);
        };
});



$app->get('/mobileapi/ridecount/updated/', function (Request $request, Response $response, array $args) {
        global $config;
        $jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
        try {
                $decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
                $token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
                if ($token) {
                        $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
                        if ($user) {

				DB::update('users', array(
					'ridecount_upgraded' => true
				), "id=%i", $user['id']);

                		$result['status']=true;
        		        $result['status_reason']="Flagged as having the new ride count database format";
		                return $response->withJson($result);
			};
		};
        } catch (Exception $e) {
                $result['status']=false;
                $result['status_reason']="Invalid jwt";
                return $response->withJson($result);
        };
});
?>
