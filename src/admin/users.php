<?php
//I contain all the routes/functions used by administrators to manage users

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;

$app->get('/admin/users/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} {
		update_user_session();
		$smarty->assign('user',$_SESSION['parkplanr']['user']);

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_USERS");

		$edit_users=DB::query("SELECT * FROM users");

		foreach ($edit_users as &$edit_user) {
			$size=40;
			$edit_user['gravatar_url']="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $edit_user['email'] ) ) ) . "&s=" . $size;

			if (strtotime($edit_user['registered']) <= strtotime('-1 week')) {
				$edit_user['expired']=True;
			};
		}

		$smarty->assign('edit_users',$edit_users);
		$smarty->display('admin/users/users.tpl');
	};
});

$app->get('/admin/users/{user_id}/edit/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} {
		$smarty->assign('user',$_SESSION['parkplanr']['user']);
		update_user_session();

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_USERS");
                web_require_permission("ADMIN_USERS_EDIT");

		$user=DB::queryFirstRow("SELECT * FROM users WHERE id=%i",$args['user_id']);
		if (!$user) {
			$smarty->assign('error_code',"user_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('edit_user',$user);
		$smarty->display('admin/users/user_edit.tpl');
	};
});

$app->post('/admin/users/{user_id}/edit/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $mg;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} {
		update_user_session();

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_USERS");
                web_require_permission("ADMIN_USERS_EDIT");

		$user=DB::queryFirstRow("SELECT * FROM users WHERE id=%i",$args['user_id']);
		if (!$user) {
			$smarty->assign('error_code',"user_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
                $smarty->assign('edit_user',$user);


		//no checks done on name
                $smarty->assign('name',$_POST['name']);

                $email_valid=preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $_POST['email']) ? TRUE : FALSE;
                if (!$email_valid) {
                        //Email address provided is invalid
                        $error=true;
                        $smarty->assign('error',true);
                        $smarty->assign('email_error',"Invalid email address");
                } else {
                        //email address is valid
                        $smarty->assign('email',$_POST['email']);
                        $edit_user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $args['user_id']);
			if ($edit_user['email']!=$_POST['email']) {
				$random = new \chriskacerguis\Randomstring\Randomstring();
				$email_changed=true;
				$email_verification_token=hash('ripemd160', $_POST['email'].$random->generate(16));

	                        $user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $_POST['email']);
        	                if ($user) {
                	                //found a user with that same email
                	                $error=true;
                	                $smarty->assign('error',true);
                	                $smarty->assign('email_error',"There is an existing account with the provided email address");
                	        };
               	        } else {
				$email_verification_token=$edit_user['email_verification_token'];
			};
                };

		if ($error) {
			$smarty->assign('user',$_SESSION['parkplanr']['user']);
                        $smarty->display('admin/users/user_edit.tpl');
                        die();
		} else {
			DB::update('users', array(
				'name' => $_POST['name'],
				'email' => $_POST['email'],
				'email_verified' => !$email_changed,
				'email_verification_token' => $email_verification_token
			), "id=%i", $args['user_id']);

                        $edit_user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $args['user_id']);

			$smarty->assign("user_uid_hash",hash('ripemd160', $edit_user['id']));
        	        $smarty->assign('user',$edit_user);
        	        $email_text=$smarty->fetch('emails/text/new_account.tpl');
        	        $email_html=$smarty->fetch('emails/new_account.tpl');

        	        $mg->messages()->send($config['email_domain'], [
        	                'from'    => $config['app_support_email'],
        	                'to'      => $edit_user['email'],
        	                'subject' => 'Welcome to '.$config['app_full_name'],
        	                'text'    => $email_text,
        	                'html'    => $email_html
	                ]);


			header("Location: /admin/users/".$args['user_id']);
			die();
		};
	};
});

$app->get('/admin/users/{user_id}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} {
		$smarty->assign('user',$_SESSION['parkplanr']['user']);
		update_user_session();

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_USERS");
                web_require_permission("ADMIN_USERS_DELETE");

		$user=DB::queryFirstRow("SELECT * FROM users WHERE id=%i",$args['user_id']);
		if (!$user) {
			$smarty->assign('error_code',"user_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		if ($user['id']==$_SESSION['parkplanr']['user']['id']) {
			$smarty->assign('error_code',"ADMIN_SELF_DELETION");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('edit_user',$user);
		$smarty->display('admin/users/user_delete.tpl');
	};
});

$app->post('/admin/users/{user_id}/delete/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} {
		$smarty->assign('user',$_SESSION['parkplanr']['user']);
		update_user_session();

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_USERS");
                web_require_permission("ADMIN_USERS_DELETE");

		$user=DB::queryFirstRow("SELECT * FROM users WHERE id=%i",$args['user_id']);
		if (!$user) {
			$smarty->assign('error_code',"user_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		if ($user['id']==$_SESSION['parkplanr']['user']['id']) {
			$smarty->assign('error_code',"ADMIN_SELF_DELETION");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		DB::delete('users', "id=%i", $args['user_id']);

		header("Location: /admin/users/");
		die();
	};
});

$app->get('/admin/users/{user_id}/', function (Request $request, Response $response, array $args) {
	global $smarty;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} {
		$smarty->assign('user',$_SESSION['parkplanr']['user']);
		update_user_session();

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_USERS");

		$user=DB::queryFirstRow("SELECT * FROM users WHERE id=%i",$args['user_id']);
		if (!$user) {
			$smarty->assign('error_code',"user_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign('edit_user',$user);
		$smarty->display('admin/users/user_view.tpl');
	};
});

$app->get('/admin/users/{user_id}/resendemailverification/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $mg;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} {
		update_user_session();

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_USERS");
                web_require_permission("ADMIN_USERS_EDIT");

		$edit_user=DB::queryFirstRow("SELECT * FROM users WHERE id=%i",$args['user_id']);
		if (!$edit_user) {
			$smarty->assign('error_code',"user_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};
		$smarty->assign("user_uid_hash",hash('ripemd160', $edit_user['id']));
		$smarty->assign('user',$edit_user);
		$email_text=$smarty->fetch('emails/text/new_account.tpl');
		$email_html=$smarty->fetch('emails/new_account.tpl');

                $mg->messages()->send($config['email_domain'], [
                        'from'    => $config['app_support_email'],
                        'to'      => $edit_user['email'],
                        'subject' => 'Welcome to '.$config['app_full_name'],
                        'text'    => $email_text,
			'html'    => $email_html
		]);

		header("Location: /admin/users/".$args['user_id']);
		die();
	};
});





$app->get('/admin/users/{user_id}/passwordreset/', function (Request $request, Response $response, array $args) {
	global $smarty, $config, $mg;
	if (!isset($_SESSION['parkplanr']['user'])) {
		header("Location: /app");
		die();
	} {
		update_user_session();

                web_require_permission("ADMIN");
                web_require_permission("ADMIN_USERS");
                web_require_permission("ADMIN_USERS_EDIT");

		$edit_user=DB::queryFirstRow("SELECT * FROM users WHERE id=%i",$args['user_id']);
		if (!$edit_user) {
			$smarty->assign('error_code',"user_NOT_FOUND");
                        $smarty->display('error_authenticated.tpl');
                        die();
		};

		request_user_password_reset($args['user_id']);
		header("Location: /admin/users/".$args['user_id']);
		die();
	};
});
?>
