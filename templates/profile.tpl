{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Profile</title>
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
	Profile
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><a href="/profile"><i class="fa fa-user"></i> Profile</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="box">
            <!-- /.box-header -->
            <div class="box-body">
		<img id="profile_img" height="50px" /><br />

		<!-- Text input-->
		<div class="form-group">
			<label class="col-md-4 control-label" for="displayname">Display name</label>
			<div class="col-md-4">
				<input id="displayname" name="displayname" type="text" placeholder="" class="form-control input-md" value="" disabled>
			</div>
		</div>
		<br />
		<!-- Text input-->
		<div class="form-group">
			<label class="col-md-4 control-label" for="email">Email address</label>
			<div class="col-md-4">
				<input id="email" name="email" type="text" placeholder="" class="form-control input-md" value="" disabled>
			</div>
		</div>

            </div>
            <!-- /.box-body -->
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

<script>
	firebase.auth().onAuthStateChanged(function(user) {
		if (user) {
			console.log(user);
			$('#displayname').val(user.displayName);
			$('#email').val(user.email);

			$('#profile_img').attr('src',user.photoURL);
			$.each(user.providerData, function(index, provider){
				console.log(provider);
			});
		};
	});
</script>
</body>
</html>
