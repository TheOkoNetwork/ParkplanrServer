<?php
set_time_limit(0);

require '../../vendor/autoload.php';
require '../../src/picsolvefunctions.php';
require '../../config.php';
require '../../commonincludes.php';

function is_cli()
{
    return (php_sapi_name() === 'cli');
};

$cli=is_cli();
if (!$cli) {
	die("This script MUST be run via the CLI");
};

$date_codes=DB::queryFirstColumn("SELECT DISTINCT(date) FROM `claim_codes`");
$existing_date_codes=DB::queryFirstColumn("SELECT date FROM date_codes");

foreach ($date_codes as $date_code) {
	$date_taken=DB::queryFirstField("SELECT date_taken FROM `claim_codes` WHERE date=%s",$date_code);

	echo $date_code." ".$date_taken.PHP_EOL;

	if (in_array($date_code,$existing_date_codes)) {
		echo "Existing date code. Skipping.".PHP_EOL;
		continue;
	};

	echo "New date code, Adding to DB.".PHP_EOL;
	DB::insert('date_codes', array(
		'date' => $date_code,
		'date_taken' => $date_taken
	));
};
