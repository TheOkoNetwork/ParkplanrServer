<?php
set_time_limit(0);

use Kunnu\OneDrive\Client;
use GuzzleHttp\Client as Guzzle;

require '../../vendor/autoload.php';
require '../../src/picsolvefunctions.php';

use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

require '../../config.php';
require '../../commonincludes.php';

//setup smarty template engine
$smarty = new Smarty;
$smarty->setTemplateDir('../../templates')->setCompileDir('../../templates_c');

function is_cli()
{
    return (php_sapi_name() === 'cli');
};

$cli=is_cli();
if (!$cli) {
	die("This script MUST be run via the CLI");
};

DB::update('picsolvedownloader_accounts', array(
	'process' => 1
), "process=%i AND autoprocess=%i", 0, 1);

//DB::update('picsolvedownloader_accounts', array(
//	'process' => 1
//),"1=1");
