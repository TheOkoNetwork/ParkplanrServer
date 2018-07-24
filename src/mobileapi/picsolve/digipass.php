<?php
//I contain functions for handling Digipasses

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

$app->post('/mobileapi/picsolve/digipass/add/', function (Request $request, Response $response, array $args) {
	global $config, $bugsnag;
	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	$result=[];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			if ($user) {
                                bugsnag_report_user($user);

				$_POST['barcode']=strtoupper($_POST['barcode']);
				$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
                                $data = array('mediaSetId' => $_POST['barcode']);

				$picsolve_request=Requests::post('https://www.picsolve.com/redeem', $headers, json_encode($data),$config['requestoptions']);
                                $picsolve_response = json_decode($picsolve_request->body,true);
				if ($picsolve_response['success']) {
					DB::insert('digipass', array(
						'user' => $user['id'],
						'barcode' => $_POST['barcode']
					));
					$result['status_human']="Digipass added ok. new photo's from the Digipass were added to the account(OK)";
					$result['status']=true;
				} else {
					switch($picsolve_request->status_code) {
						case 409:
						case 200:
							$digipass = DB::queryFirstRow("SELECT * FROM digipass WHERE user=%i AND barcode=%s", $user['id'], $_POST['barcode']);
							if ($digipass) {
								$result['status']=false;
								$result['reason']="EXISTS";
								$result['status_human']="The ParkPlanr user already has this Digipass added onto their ParkPlanr account.(NOT OK)";
							} else {
								DB::insert('digipass', array(
									'user' => $user['id'],
									'barcode' => $_POST['barcode']
								));
								$result['status_human']="This Digipass is new to the users ParkPlanr account but the photos have allready been claimed. (OK)";
								$result['status']=true;
							};
							break;
						case 404:
							$result['status']=false;
							$result['status_human']="Picsolve does not recognise the provided barcode. Either it is invalid or there are no photos on the barcode. If it is a new barcode it will need to be used and have the photos processed before it can be added. (NOT OK)";
							break;
						default:
							$bugsnag->notifyError('PicsolveAPIError', 'Error checking Digipass', function ($report) use ($picsolve_request,$picsolve_response){
								$report->setSeverity('error');
								$report->setMetaData([
									'picsolveresponse' => array(
										'code' => $picsolve_request->status_code,
										'body' => $picsolve_response
									)
								]);
							});
							$result['status']=false;
							$result['status_human']="An error occured with Picsolve";
							break;
					};
				};
				return $response->withJson($result);
			} else {
				$result['status']=false;
				$result['status_reason']="Could not find token. Possibly revoked.";
				return $response->withJson($result);
			};
		} else {
			$result['status']=false;
			$result['status_reason']="Could not find token. Possibly revoked.";
			return $response->withJson($result);
		};
	} catch (Exception $e) {
		$result['status']=false;
		$result['status_reason']="Invalid jwt";
		return $response->withJson($result);
	};
});




$app->get('/mobileapi/picsolve/digipass/', function (Request $request, Response $response, array $args) {
	global $config;
	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			if ($user) {
                                bugsnag_report_user($user);

				$result['digipasses'] = DB::query("SELECT * FROM digipass WHERE user=%i", $token['user']);
				$result['status']=true;
				$result['status_human']="All ok. provided user with list of digipasses.";
				return $response->withJson($result);
			} else {
				$result['status']=false;
				$result['status_reason']="Could not find token. Possibly revoked.";
				return $response->withJson($result);
			};
		} else {
			$result['status']=false;
			$result['status_reason']="Could not find token. Possibly revoked.";
			return $response->withJson($result);
		};
	} catch (Exception $e) {
		$result['status']=false;
		$result['status_reason']="Invalid jwt";
		return $response->withJson($result);
	};
});




$app->delete('/mobileapi/picsolve/digipass/{barcode}/', function (Request $request, Response $response, array $args) {
	global $config;
	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			if ($user) {
                                bugsnag_report_user($user);

				$digipass = DB::queryFirstRow("SELECT * FROM digipass WHERE user=%i AND barcode=%s", $token['user'],$args['barcode']);
				if ($digipass) {
					DB::delete('digipass', "id=%i", $digipass['id']);
					$result['status']=true;
					$result['status_reason']="Deleted barcode.";
				} else {
					$result['status']=false;
					$result['status_reason']="Digipass barcode does not exist. Already been deleted or invalid.";
				};
				return $response->withJson($result);
			} else {
				$result['status']=false;
				$result['status_reason']="Could not find token. Possibly revoked.";
				return $response->withJson($result);
			};
		} else {
			$result['status']=false;
			$result['status_reason']="Could not find token. Possibly revoked.";
			return $response->withJson($result);
		};
	} catch (Exception $e) {
		$result['status']=false;
		$result['status_reason']="Invalid jwt";
		return $response->withJson($result);
	};
});
?>
