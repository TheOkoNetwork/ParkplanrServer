// include the Themeparks library
var runner = require("child_process");
var Themeparks = require("themeparks");
var cacheManager = require('cache-manager');
Themeparks.Settings.Cache = cacheManager.caching({
    store: require('cache-manager-fs-binary'),
    options: {
        reviveBuffers: false,
        binaryAsStream: true,
        ttl: 60 * 60,
        maxsize: 1000 * 1000 * 1000,
        path: 'diskcache',
      preventfill: false
    }
});

// list all the parks supported by the library
for (var park in Themeparks.Parks) {
	Themeparks.Parks[park]= new Themeparks.Parks[park]();

	park_name=Themeparks.Parks[park].Name;
	switch (park_name) {
		//themeparks js thorpe park dont show correct times.
		// (queuetimes_ride_id been removed so legacy script can handle them)
//		case "Thorpe Park":
//			console.log("Loading queue times for Thorpe Park");
//			break;
//		case "Chessington World Of Adventures":
//			console.log("Loading queue times for Chessington World Of Adventures");
//			break;
//		case "Alton Towers":
//			console.log("Loading queue times for Alton Towers");
//			break;
		case "Efteling":
			console.log("Loading queue times for Efteling");
			break;
		default:
//			console.log(park_name);
			continue;
	};
	Themeparks.Parks[park].GetWaitTimes().then(function(rides) {

	    for(var i=0, ride; ride=rides[i++];) {
		timestamp = Math.round((new Date()).getTime() / 1000);
		switch(ride.status) {
			case "Operating":
				ride.status=1;
				break;
			default:
				ride.status=0;
		};
		var phpScriptPath = "insert.php";
		var argsString = ride.id+" "+ride.waitTime+" "+ride.status+" "+" "+timestamp+" "+JSON.stringify(ride.name);
		runner.exec("php " + phpScriptPath + " " +argsString, function(err, phpResponse, stderr) {
			if(err) console.log(err); /* log error */
			if (phpResponse!="") {
				console.log(phpResponse);
			};
		});
	    };
	}, console.error);
};
