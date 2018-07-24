<?php
	require_once('../../vendor/autoload.php');
	require_once('../../config.php');
	require_once('../../commonincludes.php');

	function is_cli() {
	    return (php_sapi_name() === 'cli');
	};

	$cli=is_cli();
	if (!$cli) {
	        die("This script MUST be run via the CLI");
	};

	//setup smarty template engine
	$smarty = new Smarty;
	$smarty->setTemplateDir('../../templates')->setCompileDir('../../templates_c');

	$outdated_time=time()-86400;
	$outdated_rides=DB::query("SELECT * FROM rides WHERE queuetimes=1 AND queuetime_updated<=%i AND disabled=0",$outdated_time);
	$not_updated_list="";
	foreach ($outdated_rides as $ride) {
		$not_updated_list=$not_updated_list.$ride['name']." has not had it's queue time updated in 24 hours. It's ParkPlanr id is:".$ride['id']."\n\n";
	};
	DB::update('rides', array(
		'queuetime_outdated' => true
	), "queuetimes=1 AND queuetime_updated<=%i",$outdated_time);

	if ($not_updated_list=="") {
		echo "All queue time enabled rides have been updated within the last 24 hours.";
	} else {
		echo $not_updated_list;

		$smarty->assign('not_updated_list',$not_updated_list);
	        $email_text=$smarty->fetch('emails/text/admin_queuetimes_outdated.tpl');

	        $mg->messages()->send($config['email_domain'], [
	                'from'    => $config['app_support_email'],
	                'to'      => $config['app_support_email'],
	                'subject' => 'Outdated queue times detected',
	                'text'    => $email_text
	        ]);

	};
?>
