{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{#app_full_name#} | Sign up</title>
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

  <!-- Google Recaptcha-->
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="/site"><b>{#app_first_full_name#}</b>{#app_last_full_name#}</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign up to {#app_full_name#} to begin planning your trip!</p>
	{if isset($error)}
	    <p class="login-box-msg error_text">Sorry, but there was a problem with your sign up request.</p>
	    <p class="login-box-msg error_text">Please check your submission.</p>
	{/if}
    <form action="/signup" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control {if isset($name_error)}input_error{/if}" placeholder="Name" id="name" name="name" value="{$name}">
	{if isset($name_error)}
	    <p class="error_text">{$name_error}</p>
	{/if}
        <span class="fa fa-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="email" class="form-control {if isset($email_error)}input_error{/if}" placeholder="Email*" id="email" name="email" value="{$email}" required="">
	{if isset($email_error)}
	    <p class="error_text">{$email_error}</p>
	{/if}
        <span class="fa fa-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control {if isset($password_error)}input_error{/if}" placeholder="Password*" id="password" name="password" required="">
	<p>Your password must be at least 8 characters long, have at least 1 lower case letter, 1 UPPER case letter and 1 number.</p>
	{if isset($password_error)}
	    <p class="error_text">{$password_error}</p>
	{/if}
        <span class="fa fa-lock form-control-feedback"></span>
      </div>
      <div class="row">

        <!-- /.col -->
        <div class="col-xs-12">
	  <div class="g-recaptcha" data-sitekey="{#config_recaptcha_sitekey#}"></div>
 	  {if isset($captcha_error)}
	    <p class="error_text">{$captcha_error}</p>
	  {/if}
	</div>
	<!-- xs-4 when with the forgot password box -->
        <div class="col-xs-12">
          <button type="submit" class="btn btn-block btn-primary btn-block btn-flat">Sign up</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

	<!-- HIDDEN for now as no social auth is setup -->
    <div class="social-auth-links text-center" hidden>
      <p>- OR -</p>
      <a href="/signin/social/facebook" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using
        Facebook</a>
      <a href="/signin/social/google" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using
        Google+</a>
      <a href="/signin/social/twitter" class="btn btn-block btn-social btn-twitter btn-flat"><i class="fa fa-twitter"></i> Sign up using
        Twitter</a>
    </div>
    <!-- /.social-auth-links -->

    <a href="/signin" class="text-center">I have an account</a>

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
</body>
</html>
