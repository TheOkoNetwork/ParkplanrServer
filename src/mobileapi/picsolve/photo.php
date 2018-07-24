<?php
//I contain functions for handling Photoos

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

$app->get('/mobileapi/picsolve/preview/{claim_code}/', function (Request $request, Response $response, array $args) {
	global $config;
	$claim_code=strtoupper($args['claim_code']);

	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			if ($user) {
                                bugsnag_report_user($user);

				$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
                                $data = array('mediaSetId' => $claim_code);

				$picsolve_request=Requests::post('https://www.picsolve.com/verify', $headers, json_encode($data),$config['requestoptions']);
                                $picsolve_response = json_decode($picsolve_request->body,true);
				if ($picsolve_response['success']) {
					$media_count=$picsolve_response['data']['mediaCount'];

					$photo_image_url=$picsolve_response['data']['photos'][0]['watermarkPreviewUrl'];

					if ($media_count!=1) {
						$result['digipass']=true;
						$result['status']=true;
						$result['reason']="DIGIPASS";
						$result['preview_url']=$photo_image_url;
						$result['status_human']="The provided claim code is a Digipass";
					} else {
						$result['reason']="PHOTO";
						$result['preview_url']=$photo_image_url;
						$result['status_human']="The provided claim code is an individual photo.";
		                	        if (Requests::get($photo_image_url,$config['requestoptions'])->status_code==200) {
							$result['status']=true;
                			        } else {
							$result['status']=false;
							$result['reason']="MISSING_PHOTO";
							$result['status_human']="Picsolve recognises the photo code but does not have a photo ready.(NOT OK)";
			                        };
					};
				} else {
					switch($picsolve_request->status_code) {
						case 409:
							$result['status']=false;
							$result['reason']="EXISTS";
							$result['status_human']="The ParkPlanr user already has this Digipass added onto their ParkPlanr account.(NOT OK)";
							break;
						case 404:
							$result['status']=false;
							$result['status_human']="Picsolve does not recognise the provided claim code. Either it is invalid or the photo has not uploaded yet.";
							break;
						default:
							$result['status']=false;
							$result['status_human']="Something went wrong:".$picsolve_request->status_code;
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





$app->get('/mobileapi/picsolve/claim/{claim_code}/', function (Request $request, Response $response, array $args) {
	global $config;
	$claim_code=strtoupper($args['claim_code']);

	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			if ($user) {
                                bugsnag_report_user($user);

				$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
                                $data = array('mediaSetId' => $claim_code);

				$picsolve_request=Requests::post('https://www.picsolve.com/redeem', $headers, json_encode($data),$config['requestoptions']);
                                $picsolve_response = json_decode($picsolve_request->body,true);
				if ($picsolve_response['success']) {
					$result['status']=true;
					$result['status_human']="Successfully claimed photo.";
					return $response->withJson($result);
				} else {
					switch($picsolve_request->status_code) {
						case 409:
							$result['status']=false;
							$result['reason']="EXISTS";
							$result['status_human']="The ParkPlanr user already has this Digipass added onto their ParkPlanr account.(NOT OK)";
							break;
						case 404:
							$result['status']=false;
							$result['status_human']="Picsolve does not recognise the provided claim code. Either it is invalid or the photo has not uploaded yet.";
							break;
						default:
							$result['status']=false;
							$result['status_human']="Something went wrong:".$picsolve_request->status_code;
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

$app->get('/mobileapi/picsolve/albums/', function (Request $request, Response $response, array $args) {
	global $config;
	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			if ($user) {
                                bugsnag_report_user($user);

				$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
				$picsolve_request=Requests::get('https://www.picsolve.com/albums', $headers,$config['requestoptions']);
                                $picsolve_response = json_decode($picsolve_request->body,true);
				if ($picsolve_response['success']) {
					$result['status']=true;
					$result['status_human']="All ok. provided user with list of albums.";
					$result['albums']=$picsolve_response['data'];
				} else {
					switch($picsolve_request->status_code) {
						default:
							$result['status']=false;
							$result['status_human']="Something went wrong:".$picsolve_request->status_code;
							$result['picsolve_response']=$picsolve_response;
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


$app->get('/mobileapi/picsolve/albums/{album_id}/', function (Request $request, Response $response, array $args) {
	global $config;
	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			if ($user) {
                                bugsnag_report_user($user);

				$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());

				if ($args['album_id']==-1) {
					$picsolve_request=Requests::get('https://www.picsolve.com/albums', $headers,$config['requestoptions']);
					$picsolve_response = json_decode($picsolve_request->body,true);

					$master_album=[];
					foreach ($picsolve_response['data'] as &$picsolve_album) {
						$album_picsolve_request=Requests::get('https://www.picsolve.com/albums/'.$picsolve_album['albumId'], $headers,$config['requestoptions']);
						$album_picsolve_response = json_decode($album_picsolve_request->body,true);
						$master_album=array_merge($master_album,$album_picsolve_response['data']['photos']);
					}
					$result['photos']=$master_album;
					$result['status']=true;
					$result['status_human']="All ok. provided user with list of photos for all albums";
					$result['album_name']="All albums";

				} else {
					$picsolve_request=Requests::get('https://www.picsolve.com/albums/'.$args['album_id'], $headers,$config['requestoptions']);
        	                        $picsolve_response = json_decode($picsolve_request->body,true);
					if ($picsolve_response['success']) {
						$result['status']=true;
						$result['status_human']="All ok. provided user with list of photos for album.";
						$result['photos']=$picsolve_response['data']['photos'];
						$result['album_name']=$picsolve_response['data']['album']['albumName'];
						$result['picsolve_response']=$picsolve_response;
					} else {
						switch($picsolve_request->status_code) {
							default:
								$result['status']=false;
								$result['status_human']="Something went wrong:".$picsolve_request->status_code;
								$result['picsolve_response']=$picsolve_response;
						};
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





$app->get('/mobileapi/picsolve/photos/{image_id}/getfull/', function (Request $request, Response $response, array $args) {
	global $config;
	$jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
	try {
		$decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
		$token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
		if ($token) {
			$user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
			if ($user) {
                                bugsnag_report_user($user);

				$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token']);
				$picsolve_request=Requests::get('https://www.picsolve.com/photos/'.$args['image_id'], $headers,$config['requestoptions']);
                                $picsolve_response = json_decode($picsolve_request->body,true);
				if ($picsolve_response['success']) {
					$result['status']=true;
					$result['full_image']=$picsolve_response['data']['fullsizeUrl'];
					$result['share_name']=$picsolve_response['data']['rideData']['shareName'];
					$result['share_text']="Check out my photo from ".$result['share_name'];
					$result['image_id']=$args['image_id'];
					$result['ride_name']=$picsolve_response['data']['rideData']['name'];
					$result['park_name']=$picsolve_response['data']['parkData']['name'];
					$result['filename']=date("Y_m_d",strtotime($picsolve_response['data']['dateTaken']))."_".$result['park_name']."_".$result['ride_name']."_".$result['image_id'].".jpeg";
					$result['filename'] =preg_replace('@[^0-9a-zA-Z/_\.]+@i', '', $result['filename']);
					$result['status_human']="All ok. provided user with photo url.";
					$result['picsolve_response']=$picsolve_response;
				} else {
					switch($picsolve_request->status_code) {
						default:
							$result['status']=false;
							$result['status_human']="Something went wrong:".$picsolve_request->status_code;
							$result['picsolve_response']=$picsolve_response;
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
?>
