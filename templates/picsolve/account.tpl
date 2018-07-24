{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Picsolve | Picsolve account</title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  {include file="header.tpl"}
  {include file="left_sidebar.tpl"}

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Picsolve account
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li class=""><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li class=""><a href="/picsolve"<i class="fa fa-camera"></i> Picsolve</a></li>
        <li class="active"><a href="/picsolve/picsolveaccount"><i class="fa fa-user"></i> Picsolve account</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
		{if $user.picsolve_auth_token}
			Picsolve account
		{else}
			Picsolve sign in
		{/if}
	  </h3>
        </div>
        <div class="box-body">
		{if $user.picsolve_auth_token}
			<p>You are signed into Picsolve<p>
			<p>To save your photos goto the <a href="/picsolve/downloader">Picsolve Downloader</a></p>
			<p>To have your Digipass automagically added <a href="/picsolve/digipass">Manage your Digipasses</a></p>
			{if $user.admin}
				<br />
				<p>No longer using Picsolve with {#app_full_name#}? <a href="/picsolve/account/unlink">Unlink your Picsolve account</a></p>
			{/if}
		{else}
			<p>Q:Why do we need your picsolve email address and password?</p>
			<p>A:ParkPlanr connects to the picsolve.com website to download your photos/automagically add your digipass and your password is required to login to your account</p>
			<p>Your password is NOT saved</p>
			<br />
			<br />
			

			{if isset($error)}<p><b>Either your email address or username are incorrect, please try again ensuring you are entering your Picsolve.com email and password.<b></p>{/if}
			<form class="form-horizontal" METHOD="POST">
				<fieldset>

					<!-- Text input-->
					<div class="form-group">
						<label class="col-md-4 control-label" for="picsolve_email">Email address</label>  
						<div class="col-md-4">
							<input id="picsolve_email" name="picsolve_email" type="text" placeholder="" class="form-control input-md" required="">
						</div>
					</div>

					<!-- Password input-->
					<div class="form-group">
						<label class="col-md-4 control-label" for="picsolve_password">Password</label>
						<div class="col-md-4">
							<input id="picsolve_password" name="picsolve_password" type="password" placeholder="" class="form-control input-md" required="">
						</div>
					</div>

					<!-- Button (Double) -->
					<div class="form-group">
						<label class="col-md-4 control-label" for="submit"></label>
						<div class="col-md-4">
							<button id="Submit" name="Submit" class="btn btn-success">Sign in to Picsolve</button>
						</div>
					</div>
				</fieldset>
			</form>






		{/if}
        </div>
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  {include file="footer.tpl"}
</div>
<!-- ./wrapper -->

{include file="prebodyend_includes.tpl"}
</body>
</html>
