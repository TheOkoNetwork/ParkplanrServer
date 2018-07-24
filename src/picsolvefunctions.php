<?php
//I contain common functions for handling picsolve stuff, they will be used across both the website and app

function picsolve_photo_getfull($imageid,$user) {
	global $config;
	$headers = array('Content-Type' => 'application/json','x-psi-user-auth-token' => $user['picsolve_auth_token'], 'User-Agent' => \Campo\UserAgent::random());
	$picsolve_request=Requests::get('https://www.picsolve.com/photos/'.$imageid, $headers,$config['requestoptions']);
	$picsolve_response = json_decode($picsolve_request->body,true);
	if ($picsolve_response['success']) {
		$result['full_image']=$picsolve_response['data']['fullsizeUrl'];
		$result['share_name']=$picsolve_response['data']['rideData']['shareName'];
		$result['share_text']="Check out my photo from ".$result['share_name'];
		$result['image_id']=$imageid;
		$result['ride_name']=$picsolve_response['data']['rideData']['name'];
		$result['park_name']=$picsolve_response['data']['parkData']['name'];
		$result['filename']=date("Y_m_d",strtotime($picsolve_response['data']['dateTaken']))."_".$result['park_name']."_".$result['ride_name']."_".$result['image_id'].".jpeg";
		$result['filename']=preg_replace('@[^0-9a-zA-Z/_\.]+@i', '', $result['filename']);
		$result['highestquality']=true;
		$result['quality']=100;
		$result['quality_text']="FULL";
		return $result;
	} else {
		return false;
	};
};


function picsolve_photo_getmedium($photo,$user) {
	global $config;
	$headers = array('User-Agent' => \Campo\UserAgent::random());
	$picsolve_request=Requests::get($photo['mediumUrl'],$headers,$config['requestoptions']);

	if ($picsolve_request->status_code==200) {
		$result['full_image']=$photo['mediumUrl'];
		$result['share_name']=$photo['rideData']['shareName'];
		$result['share_text']="Check out my photo from ".$result['share_name'];
		$result['image_id']=$photo['imageId'];
		$result['ride_name']=$photo['rideData']['name'];
		$result['park_name']=$photo['parkData']['name'];
		$result['filename']=date("Y_m_d",strtotime($photo['dateTaken']))."_".$result['park_name']."_".$result['ride_name']."_".$result['image_id'].".jpeg";
		$result['filename']=preg_replace('@[^0-9a-zA-Z/_\.]+@i', '', $result['filename']);
		$result['highestquality']=false;
		$result['quality']=50;
		$result['quality_text']="MEDIUM";
		return $result;
	} else {
		return false;
	};
};

function picsolve_photo_getthumbnail($photo,$user) {
	global $config;
	$headers = array('User-Agent' => \Campo\UserAgent::random());
	$picsolve_request=Requests::get($photo['thumbnailUrl'],$headers,$config['requestoptions']);

	if ($picsolve_request->status_code==200) {
		$result['full_image']=$photo['thumbnailUrl'];
		$result['share_name']=$photo['rideData']['shareName'];
		$result['share_text']="Check out my photo from ".$result['share_name'];
		$result['image_id']=$photo['imageId'];
		$result['ride_name']=$photo['rideData']['name'];
		$result['park_name']=$photo['parkData']['name'];
		$result['filename']=date("Y_m_d",strtotime($photo['dateTaken']))."_".$result['park_name']."_".$result['ride_name']."_".$result['image_id'].".jpeg";
		$result['filename']=preg_replace('@[^0-9a-zA-Z/_\.]+@i', '', $result['filename']);
		$result['highestquality']=false;
		$result['quality']=1;
		$result['quality_text']="THUMBNAIL";
		return $result;
	} else {
		return false;
	};
};

function picsolve_photo_gethighest($photo,$user) {
	$full=picsolve_photo_getfull($photo['imageId'],$user);
	if ($full) {
		return $full;
	};

	$medium=picsolve_photo_getmedium($photo,$user);
	if ($medium) {
		return $medium;
	};

	$thumbnail=picsolve_photo_getthumbnail($photo,$user);
	if ($thumbnail) {
		return $thumbnail;
	};

	return false;
};
