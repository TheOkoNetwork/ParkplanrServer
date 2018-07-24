<?php
//I contain functions for handling Picsolve accounts

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

$app->post('/mobileapi/picsolve/signin/', function (Request $request, Response $response, array $args) {
	global $config;
        try {
	        $jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
                $decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
                $token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
                if ($token) {
                        $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
                        if (!$user) {
				$result['status']=false;
				$result['status_human']="INVALID_USER";
		                return $response->withJson($result);
			} else {
                                bugsnag_report_user($user);

				//now check the picsolve account
				$headers = array('Content-Type' => 'application/json', 'User-Agent' => \Campo\UserAgent::random());
				$data = array('email' => $_POST['email'], 'password' => $_POST['password']);
				$picsolve_request=Requests::post('https://www.picsolve.com/user/login', $headers, json_encode($data),$config['requestoptions']);
				$picsolve_response = json_decode($picsolve_request->body,true);
				if ($picsolve_response['success']) {
					DB::update('users', array(
                        			'picsolve_auth_token' => $picsolve_response['data']['authToken'],
                        			'picsolve_signedin' => time()
			                ), "id=%i", $user['id']);
					$result['status']=true;
					$result['status_human']="SUCCESS_PICSOLVE";
			                return $response->withJson($result);
				} else {
					$result['status']=false;
					$result['status_human']="INVALID_PICSOLVE";
			                return $response->withJson($result);
				};
			};
		} else {
				$result['status']=false;
				$result['status_human']="INVALID_JWT_".$jti;
		                return $response->withJson($result);
		};
	} catch (Exception $e) {
                $result['status']=false;
                $result['status_reason']="Invalid jwt";
                return $response->withJson($result);
        };

});



$app->post('/mobileapi/picsolve/signup/', function (Request $request, Response $response, array $args) {
	global $config;
        try {
	        $jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
                $decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
                $token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
                if ($token) {
                        $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
                        if (!$user) {
				$result['status']=false;
				$result['status_human']="INVALID_USER";
		                return $response->withJson($result);
			} else {
                                bugsnag_report_user($user);

				//now check the picsolve account
				$headers = array('Content-Type' => 'application/json', 'User-Agent' => \Campo\UserAgent::random());
				$marketing_optout=(bool) $_POST['marketing_optout'];
				$marketing=!$marketing_optout;
				$data = array('email' => $_POST['email'], 'marketing', $marketing, 'password' => $_POST['password']);
				$picsolve_request=Requests::post('https://www.picsolve.com/user', $headers, json_encode($data),$config['requestoptions']);
				$picsolve_response = json_decode($picsolve_request->body,true);
				if ($picsolve_response['success']) {
					DB::update('users', array(
                        			'picsolve_auth_token' => $picsolve_response['data']['authToken'],
                        			'picsolve_signedin' => time()
			                ), "id=%i", $user['id']);
					$result['status']=true;
					$result['status_human']="SUCCESS_PICSOLVE";
			                return $response->withJson($result);
				} else {

					switch($picsolve_request->status_code) {
                                                case 409:
							$result['status_human']="Picsolve email exists.";
							$result['status_reason']="PICSOLVE_EXISTS";
							break;
						default:
						$result['status_human']="INVALID_PICSOLVE";
					};

					$result['picsolve']=$picsolve_response;
					$result['status']=false;
			                return $response->withJson($result);
				};
			};
		} else {
				$result['status']=false;
				$result['status_human']="INVALID_JWT_".$jti;
		                return $response->withJson($result);
		};
	} catch (Exception $e) {
                $result['status']=false;
                $result['status_reason']="Invalid jwt";
                return $response->withJson($result);
        };

});

$app->post('/mobileapi/picsolve/signout/', function (Request $request, Response $response, array $args) {
	global $config;
        try {
	        $jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
                $decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
                $token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
                if ($token) {
                        $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
                        if (!$user) {
				$result['status']=false;
				$result['status_human']="INVALID_USER";
		                return $response->withJson($result);
			} else {
                                bugsnag_report_user($user);

				DB::update('users', array(
                       			'picsolve_auth_token' => ''
		                ), "id=%i", $user['id']);
				DB::delete('digipass', "user=%i", $user['id']);

				$result['status']=true;
				$result['status_human']="Removed Picsolve account auth token and removed Digipasses";
		                return $response->withJson($result);
			};
		} else {
				$result['status']=false;
				$result['status_human']="INVALID_JWT_".$jti;
		                return $response->withJson($result);
		};
	} catch (Exception $e) {
                $result['status']=false;
                $result['status_reason']="Invalid jwt";
                return $response->withJson($result);
        };

});

$app->get('/mobileapi/picsolve/account/', function (Request $request, Response $response, array $args) {
        global $config;
        $jwt=$request->getHeaders()['HTTP_AUTHORIZATION'][0];
        try {
                $decoded = (array) JWT::decode($jwt, $config['jwt_public_key'], array('RS256'));
                $token = DB::queryFirstRow("SELECT * FROM tokens WHERE id=%i", $decoded['jti']);
                if ($token) {
                        $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $token['user']);
                        if ($user) {
                                bugsnag_report_user($user);

				if ($user['picsolve_auth_token']) {
	                                $result['status']=true;
	                                $result['account']=true;
       		                        $result['status_reason']="User has Picsolve account";
				} else {
	                                $result['status']=true;
	                                $result['account']=false;
       		                        $result['status_reason']="Success. User does NOT have Picsolve account";
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
