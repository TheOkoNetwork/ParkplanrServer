<?php
//I contain all the routes/functions used by the mobile api

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;

require_once('mobileapi/account_handling.php');
require_once('mobileapi/parks.php');
require_once('mobileapi/picsolve.php');
require_once('mobileapi/ridecount.php');
?>
