{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Users | {$edit_user.name} | Profile</title>
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
        {if $edit_user.name == ""}{$edit_user.email}{else}{$edit_user.name}{/if}
        <small>Profile</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li><a href="/admin/users/"><i class="fa fa-users"></i> users</a></li>
	{if current_user_has_permission("ADMIN_USERS_EDIT")}
		<li class="active"><a href="/admin/users/{$edit_user.id}"><i class="fa fa-user"></i> {$edit_user.name} ({$edit_user.email})</a></li>
	{/if}
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="box">
            <!-- /.box-header -->
            <div class="box-header">
		{if current_user_has_permission("ADMIN_USERS_EDIT")}
			<a href="/admin/users/{$edit_user.id}/edit" class="btn btn-primary">Edit {if $edit_user.name == ""}{$edit_user.email}{else}{$edit_user.name}{/if}</a>
			<a href="/admin/users/{$edit_user.id}/passwordreset" class="btn btn-primary">Request password reset email</a>
		{/if}
	    </div>
            <div class="box-body">
		<p><b>Name:</b> {$edit_user.name}</p>
		<p><b>Email:</b> {$edit_user.email}</p>
                {if current_user_has_permission("ADMIN_USERS_EDIT")}
			{if !$edit_user.email_verified}<a href="/admin/users/{$edit_user.id}/resendemailverification" class="btn btn-primary" name="email_verification">Resend verification email</a>{/if}
		{else}
			{if !$edit_user.email_verified}<span>Email not verified</span>{/if}
		{/if}
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
</body>
</html>
