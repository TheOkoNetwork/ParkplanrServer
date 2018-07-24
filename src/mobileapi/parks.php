<?php
//I contain functions for handling/updating sessions and the routes for the authentication handling for the mobile api

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Mailgun\Mailgun;
use \Firebase\JWT\JWT;

$app->get('/mobileapi/parks/', function (Request $request, Response $response, array $args) {
		$parks = DB::query("SELECT id,name,website,lat,lon,logo_hash,map_hash,queuetimes FROM parks WHERE disabled=0");
		foreach ($parks as &$park) {
	                $park['rides']=DB::query("SELECT id,name,slogan,open,queuetime,park,queuetimes,ridecount,disabled,logo_hash FROM rides WHERE park = %i AND disabled=0 AND logo_hash!=%s ORDER BY name ASC",$park['id'],"");
	                $park['rides']=array_merge($park['rides'],DB::query("SELECT id,name,slogan,open,queuetime,park,queuetimes,ridecount,disabled,logo_hash FROM rides WHERE park = %i AND disabled=0 AND logo_hash=%s ORDER BY name ASC",$park['id'],""));
 		};
		$result['status']=true;
		$result['parks']=$parks;
		return $response->withJson($result);

});

$app->get('/mobileapi/parks/{park_id}/rides/queuetimes/[{tag_id}/]', function (Request $request, Response $response, array $args) {
		if (isset($args['tag_id'])) {
	                $rides=DB::query("SELECT rides.id,rides.name,rides.slogan,rides.open,rides.queuetime,rides.park,rides.queuetimes,rides.ridecount,rides.disabled FROM ridetags INNER JOIN rides ON ridetags.ride = rides.id WHERE rides.park = %i AND ridetags.tag = %i AND rides.disabled=0 AND rides.queuetimes=1 AND logo_hash!=%s ORDER BY name ASC",$args['park_id'],$args['tag_id'],"");
	                $rides=array_merge($rides,DB::query("SELECT rides.id,rides.name,rides.slogan,rides.open,rides.queuetime,rides.park,rides.queuetimes,rides.ridecount,rides.disabled FROM ridetags INNER JOIN rides ON ridetags.ride = rides.id WHERE rides.park = %i AND ridetags.tag = %i AND rides.disabled=0 AND rides.queuetimes=1 AND logo_hash=%s ORDER BY name ASC",$args['park_id'],$args['tag_id'],""));
		} else {
	                $rides=DB::query("SELECT id,name,slogan,open,queuetime,park,queuetimes,ridecount,disabled FROM rides WHERE park=%i AND disabled=0 AND queuetimes=1 AND logo_hash!=%s ORDER BY name ASC",$args['park_id'],"");
	                $rides=array_merge($rides,DB::query("SELECT id,name,slogan,open,queuetime,park,queuetimes,ridecount,disabled FROM rides WHERE park=%i AND disabled=0 AND queuetimes=1 AND logo_hash=%s ORDER BY name ASC",$args['park_id'],""));
		};

		$result['status']=true;
		$result['rides']=$rides;
		return $response->withJson($result);

});

$app->get('/mobileapi/parks/{park_id}/rides/ridecount/[{tag_id}/]', function (Request $request, Response $response, array $args) {
		if (isset($args['tag_id'])) {
	                $rides=DB::query("SELECT rides.id,rides.name,rides.slogan,rides.open,rides.queuetime,rides.park,rides.queuetimes,rides.ridecount,rides.disabled FROM ridetags INNER JOIN rides ON ridetags.ride = rides.id WHERE rides.park = %i AND ridetags.tag = %i AND rides.disabled=0 AND rides.ridecount=1 AND logo_hash!=%s ORDER BY name ASC",$args['park_id'],$args['tag_id'],"");
	                $rides=array_merge($rides,DB::query("SELECT rides.id,rides.name,rides.slogan,rides.open,rides.queuetime,rides.park,rides.queuetimes,rides.ridecount,rides.disabled FROM ridetags INNER JOIN rides ON ridetags.ride = rides.id WHERE rides.park = %i AND ridetags.tag = %i AND rides.disabled=0 AND rides.ridecount=1 AND logo_hash=%s ORDER BY name ASC",$args['park_id'],$args['tag_id'],""));
		} else {
	                $rides=DB::query("SELECT id,name,slogan,open,queuetime,park,queuetimes,ridecount,disabled FROM rides WHERE park=%i AND disabled=0 AND ridecount=1 AND logo_hash!=%s ORDER BY name ASC",$args['park_id'],"");
	                $rides=array_merge($rides,DB::query("SELECT id,name,slogan,open,queuetime,park,queuetimes,ridecount,disabled FROM rides WHERE park=%i AND disabled=0 AND ridecount=1 AND logo_hash=%s ORDER BY name ASC",$args['park_id'],""));
		};

		$result['status']=true;
		$result['rides']=$rides;
		return $response->withJson($result);

});


$app->get('/mobileapi/parks/{park_id}/ridetags/queuetimes/', function (Request $request, Response $response, array $args) {
                $ridetags=DB::query("SELECT DISTINCT id,(tag),area FROM parkridetags WHERE queuetimes = 1 AND park = %i AND disabled=0",$args['park_id']);
		$result['status']=true;
		$result['ridetags']=$ridetags;
		return $response->withJson($result);
});

$app->get('/mobileapi/parks/{park_id}/ridetags/ridecount/', function (Request $request, Response $response, array $args) {
                $ridetags=DB::query("SELECT DISTINCT id,(tag),area FROM parkridetags WHERE ridecount = 1 AND park = %i AND disabled=0",$args['park_id']);
		$result['status']=true;
		$result['ridetags']=$ridetags;
		return $response->withJson($result);
});
?>
