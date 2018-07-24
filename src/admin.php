<?php
//I contain all the routes/functions used by administrators

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;

require_once('admin/parks.php');
require_once('admin/rides.php');
require_once('admin/ridetags.php');
require_once('admin/users.php');
require_once('admin/queuescrapers.php');
require_once('admin/picsolvedownloader.php');
require_once('admin/firebasemigration.php');
?>
