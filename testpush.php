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

//pushnotification(22,"PHONEGAPBUILD","PHONEGAPBUILD","Heres the latest build","https://build.phonegap.com/apps/3120297/download/android");
pushnotification(22,"FIREBASEMIGRATION","Important information about your ParkPlanr account","Due to a system upgrade we are updating our user accounts system, please sign in again","https://parkplanr.okonetwork.org.uk/migration_required_notification");

echo "Sent".PHP_EOL;
