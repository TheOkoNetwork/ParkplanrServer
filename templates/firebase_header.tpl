<script src="https://www.gstatic.com/firebasejs/5.0.4/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.0.4/firebase-auth.js"></script>
<script>
  // Initialize Firebase
  var config = {
    apiKey: "{#firebase_apikey#}",
    authDomain: "{#firebase_authdomain#}",
    databaseURL: "{#firebase_databaseurl#}",
    projectId: "{#firebase_projectid#}",
    storageBucket: "{#firebase_storagebucket#}",
    messagingSenderId: "{#firebase_messagingid#}"
  };
  firebase.initializeApp(config);
</script>

<script src="https://cdn.firebase.com/libs/firebaseui/3.0.0/firebaseui.js"></script>
<link type="text/css" rel="stylesheet" href="https://cdn.firebase.com/libs/firebaseui/3.0.0/firebaseui.css" />


<script>
firebase.auth().onAuthStateChanged(function(user) {
  if (user) {
	console.log(user);
	profile_photo_url=user['photoURL'];
	console.log(profile_photo_url);

	if (!profile_photo_url) {
		user.providerData.forEach(function (profile) {
			console.log("  Photo URL: " + profile.photoURL);
			if (profile.photoURL) {
				profile_photo_url=profile.photoURL;
			};
		});
//		user.updateProfile({
//			photoURL: profile_photo_url
//		}).then(function() {
//		}).catch(function(error) {
//		});		
	};
	console.log("DONE");
	if (profile_photo_url) {
		$('.user_profile_image').attr('src',profile_photo_url);
	};
  }
});
</script>


<script>
        function signout() {
                firebase.auth().signOut().then(function() {
                        console.log('Signed Out');
                        location.href="/signout";
                }, function(error) {
                        console.error('Sign Out Error', error);
                        location.href="/signout";
                });
        };
</script>
