<?php
	//this file is confidential security sensitive, DO NOT CHECKIN GIT.

	$config['mysql_user'] = 'mysqluser';
	$config['mysql_password'] = 'mysqlpassword';
	$config['mysql_db'] = 'mysqldbname';
	$config['mysql_host'] = 'mysqldbhost';
	$config['email_domain']="example.com";
	$config['app_support_email']="contact@example.com";
	$config['app_full_name']="ParkPlanr";
	$config['mailgun_api_key']="mailgunapikey";
	$config['filesystem_root']="/var/www/parkplanrfolderhere";
	$config['domain']="example.com";
	$config['http_root']="https://".$config['domain'];
	$config['aws_id']="awsiamid";
	$config['aws_key']="awskey";
	$config['aws_bucket']="awss3bucketname";
	$config['s3_assets_url']="https://awss3bucketname.s3.amazonaws.com";
	$config['facebook_app_id']="facebookappid";
	$config['facebook_app_secret']="facebookappsecret";
	$config['google_client_id']="googleclientid";
	$config['dropbox_client_id']="dropboxclientid";
	$config['dropbox_client_secret']="dropboxclientsecret";
	//1=Alton Towers Resort
        $config['ridetimescouk_park']=1;
	$config['bugsnag_api_key']="bugsnag.comapikey";
	$config['server_version']="0.5.2";
	$config['fcm_server_key']="fcm_server_id";
	$config['picsolve_highest_quality']=100;

	//used for IP blockage avoidance for picsolve, needs to be standatd HTTP proxy. proxymesh.com is good
	$config['proxyhost']="hostnameofproxy";
	$config['proxyport']="31280";
?>
