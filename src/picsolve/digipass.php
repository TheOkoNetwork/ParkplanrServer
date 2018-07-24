<?php
//I contain all the routes/functions used by to manage Digipasseson the web

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;

$app->get('/picsolve/digipass/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();

	        if (!$_SESSION['parkplanr']['user']['picsolve_auth_token']) {
			redirect('/picsolve/account');
	        };

		$smarty->assign('user',$_SESSION['parkplanr']['user']);
		$user_digipasses=DB::query("SELECT * FROM digipass WHERE user=%i",$_SESSION['parkplanr']['user']['id']);
		$smarty->assign('user_digipasses',$user_digipasses);
		$smarty->display('picsolve/digipass/digipasses.tpl');
	};
});

$app->get('/picsolve/digipass/listjson/', function (Request $request, Response $response, array $args) {
	global $smarty, $bugsnag;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();

	        if (!$_SESSION['parkplanr']['user']['picsolve_auth_token']) {
			die();
	        };
		$digipasses=DB::query("SELECT * FROM digipass WHERE user=%i",$_SESSION['parkplanr']['user']['id']);
                return $response->withJson($digipasses);
	};
});
$app->post('/picsolve/digipass/addjson/', function (Request $request, Response $response, array $args) {
	global $smarty, $bugsnag;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();

	        if (!$_SESSION['parkplanr']['user']['picsolve_auth_token']) {
			die();
	        };
		$user=DB::queryFirstRow("SELECT * FROM users WHERE id=%i",$_SESSION['parkplanr']['user']['id']);

		//picsolve's API is very picky about case.
		$barcode=strtoupper($_POST['barcode']);

		$name=$_POST['name'];

		if ($_POST['familymode'] === 'true'? true: false) {
			$familymode=1;
		} else {
			$familymode=0;
		};

		$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
		$data = array('mediaSetId' => $barcode);

		$picsolve_request=Requests::post('https://www.picsolve.com/redeem', $headers, json_encode($data),$config['requestoptions']);
		$picsolve_response = json_decode($picsolve_request->body,true);

		$result=[];
		if ($picsolve_response['success']) {
			DB::insert('digipass', array(
				'user' => $user['id'],
				'barcode' => $barcode,
				'name' => $name,
				'familymode' => $familymode
			));
			$result['status_human']="Digipass added successfully. new photo's from the Digipass were added to the account(OK)";
			$result['status_user']="Digipass added successfully.";
			$result['status']=true;
		} else {
			switch($picsolve_request->status_code) {
				case 409:
					$digipass = DB::queryFirstRow("SELECT * FROM digipass WHERE user=%i AND barcode=%s", $user['id'], $barcode);
					if ($digipass) {
						$result['status']=false;
						$result['reason']="EXISTS";
						$result['status_human']="The ParkPlanr user already has this Digipass added on to their ParkPlanr account.";
						$result['status_user']="You already have this Digipass added on your ParkPlanr account.";
					} else {
						DB::insert('digipass', array(
							'user' => $user['id'],
							'barcode' => $barcode,
							'name' => $name,
							'familymode' => $familymode
						));
						$result['status_human']="This Digipass is new to the users ParkPlanr account but the photos have allready been claimed. (OK)";
						$result['status_user']="Digipass added successfully.";
						$result['status']=true;
					};
					break;
				case 404:
					$result['status']=false;
					$result['status_human']="Picsolve does not recognise the provided barcode. Either it is invalid or there are no photos on the barcode. If it is a new barcode it will need to be used and have the photos processed before it can be added.";
					$result['status_user']="Picsolve does not recognise the provided barcode. Either it is invalid or there are no photos on the barcode. If it is a new barcode it will need to be used and have the photos processed before it can be added.";
					break;
				case 500:
					$result['status']=false;
					$result['status_human']="Error on picsolves side, either invalid barcode or server error.";
					$result['status_user']="Error on picsolves side, either invalid barcode or server error.";
					break;
				default:
					$bugsnag->notifyError('PicsolveAPIError', 'Error adding Digipass', function ($report) use ($picsolve_request,$picsolve_response){
						$report->setSeverity('error');
						$report->setMetaData([
							'picsolveresponse' => array(
								'code' => $picsolve_request->status_code,
								'body' => $picsolve_response
							)
						]);
					});

					$result['status']=false;
					$result['status_human']="Unknown response from Picsolve.";
					break;
				};
			};
		};

                return $response->withJson($result);
});
$app->post('/picsolve/digipass/deletejson/', function (Request $request, Response $response, array $args) {
	global $smarty, $bugsnag;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();

	        if (!$_SESSION['parkplanr']['user']['picsolve_auth_token']) {
			die();
	        };

		$digipass=DB::queryFirstRow("SELECT * FROM digipass WHERE id=%i AND user=%i",$_POST['digipass_id'],$_SESSION['parkplanr']['user']['id']);
		if ($digipass) {
			DB::delete('digipass', "id=%i", $digipass['id']);
			$result['status']=true;
	                return $response->withJson($result);
		} else {
			die();
		};

	};
});

$app->post('/picsolve/digipass/editjson/', function (Request $request, Response $response, array $args) {
	global $smarty, $bugsnag;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();

	        if (!$_SESSION['parkplanr']['user']['picsolve_auth_token']) {
			die();
	        };

		$digipass=DB::queryFirstRow("SELECT * FROM digipass WHERE id=%i AND user=%i",$_POST['id'],$_SESSION['parkplanr']['user']['id']);
		if ($digipass) {
			if ($_POST['familymode'] === 'true'? true: false) {
				$familymode=1;
			} else {
				$familymode=0;
			};

			DB::update('digipass', array(
				'name' => $_POST['name'],
				'familymode' => $familymode
			), "id=%i", $digipass['id']);
			$result['status']=true;
	                return $response->withJson($result);
		} else {
			$result['status']=true;
			$result['status_user']="invalid digipass, refresh the page and try again.";
			$result['status_human']="An invalid digipass ID was provided. It could be non existant or not assigned to this user.";
	                return $response->withJson($result);
		};

	};
});


$app->get('/picsolve/digipass/{digipass_id}/getjson/', function (Request $request, Response $response, array $args) {
	global $smarty, $bugsnag;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} else {
		update_user_session();

	        if (!$_SESSION['parkplanr']['user']['picsolve_auth_token']) {
			die();
	        };
		$digipass=DB::queryFirstRow("SELECT * FROM digipass WHERE id=%i AND user=%i",$args['digipass_id'],$_SESSION['parkplanr']['user']['id']);
		if ($digipass) {
			$result['status']=true;
			$result['digipass']=$digipass;
		} else {
			$result['status']=false;
			$result['status_user']="invalid digipass, refresh the page and try again.";
			$result['status_human']="An invalid digipass ID was provided. It could be non existant or not assigned to this user.";
		};
                return $response->withJson($result);
	};
});
?>
