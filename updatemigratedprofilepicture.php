#!/usr/bin/php
<?php

require './vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

require './config.php';
require './commonincludes.php';

function is_cli()
{
    return (php_sapi_name() === 'cli');
};

$cli=is_cli();
if (!$cli) {
        die("This script MUST be run via the CLI");
};

$auth = $firebase->getAuth();

$users = $auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);

foreach ($users as $user) {
	$firebase_uid=$user['localId'];

	if (isset($user['photoUrl'])) {
		if (isset($user['displayName'])) {
			echo "Updating profile image for user user:".$user['displayName'].PHP_EOL;
		} else {
			echo "**** $firebase_uid".PHP_EOL;
		};
		$firebase_profile_image=$user['photoUrl'];
	} else {
		echo "No photo url, defaulting to Gravatar for user:".$user['email'].PHP_EOL;
		$firebase_profile_image="https://www.gravatar.com/avatar/" . md5( strtolower( trim( $user['email'] ) ) );
	};
	DB::update('users', array(
		'profile_image' => $firebase_profile_image
	), "firebase_uid=%s", $firebase_uid);
}
