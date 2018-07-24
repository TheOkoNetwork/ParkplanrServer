{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{#app_full_name#} | Sign in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/css/adminlte/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="/adminlteplugins/iCheck/square/blue.css">

  <link rel="stylesheet" href="/css/global.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <link href="https://fonts.googleapis.com/css?family=Roboto:500" rel="stylesheet">

  <style>
	.social_signin_text {
		color:#FFFFFF;
		font-family: 'Roboto', sans-serif;background-color:#4285F4;
	}
  </style>
  {include file="firebase_header.tpl"}


<script>
firebase.auth().onAuthStateChanged(function(user) {
  if (user) {
	console.log("Signed in!");
	console.log(user);
	$('#div_signing_in_now').show();
	$('#div_login_methods').hide();
	if (!user['emailVerified']) {
		var user = firebase.auth().currentUser;
		user.sendEmailVerification().then(function() {
		}).catch(function(error) {
		});
	};

	firebase.auth().currentUser.getIdToken(true).then(function(idToken) {
	    $.post( "/signin/firebase", { idtoken: idToken }, function(data) {
		console.log(data);
		if (data.status) {
			location.assign("/app");
		} else {
			if (data.redirect) {
				location.assign(data.redirect);
			} else {
				alert("Failed to sign in :(");
				location.assign("/signin");
			};
		};
	    }).fail(function(error) {
		console.log(error);
		alert("Failed to sign in :(");
		location.assign("/signin");
	    });
	}).catch(function(error) {
		alert("Failed to sign in :(");
		location.assign("/signin");
	});



  }
});
</script>

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="/site"><b>{#app_first_full_name#}</b>{#app_last_full_name#}</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
	<div id="div_login_methods">
		<div id="firebaseui-auth-container"></div>
		</div>
		<div id="div_signing_in_now" hidden>Signing you in now!</div>	
    </div>

	<p>Not signed in recently?</p>
	<p>If you only used Email/Password sign in <a href="/signin_legacy">Click here to update your account</a><br>
    <a href="/privacy" class="text-center">Privacy policy</p>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="/js/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/adminlteplugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>


<script type="text/javascript">
      // FirebaseUI config.
      var uiConfig = {
        signInOptions: [
          // Leave the lines as is for the providers you want to offer your users.
          firebase.auth.GoogleAuthProvider.PROVIDER_ID,
          firebase.auth.FacebookAuthProvider.PROVIDER_ID,
//        firebase.auth.TwitterAuthProvider.PROVIDER_ID,
//        firebase.auth.GithubAuthProvider.PROVIDER_ID,
          firebase.auth.EmailAuthProvider.PROVIDER_ID
//		{
//			provider: firebase.auth.PhoneAuthProvider.PROVIDER_ID,
//			recaptchaParameters: {
//				type: 'image', // 'audio'
//				size: 'normal', // 'invisible' or 'compact'
//				badge: 'bottomleft' //' bottomright' or 'inline' applies to invisible.
//			},
//			defaultCountry: 'GB', // Set default
//		}
        ],
        // Terms of service url.
        tosUrl: '/terms'
      };
      // Initialize the FirebaseUI Widget using Firebase.
      var ui = new firebaseui.auth.AuthUI(firebase.auth());
      // The start method will wait until the DOM is loaded.
      ui.start('#firebaseui-auth-container', uiConfig);
    </script>
</body>
</html>
