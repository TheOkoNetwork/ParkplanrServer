{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Users | {$edit_user.name} | Editing</title>
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
        {$edit_user.name}
        <small>Editing</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li><a href="/admin/users/"><i class="fa fa-users"></i> users</a></li>
        <li><a href="/admin/users/{$edit_user.id}"><i class="fa fa-user"></i> {$edit_user.name} ({$edit_user.email})</a></li>
        <li class="active"><a href="/admin/users/{$edit_user.id}/edit"><i class="fa fa-pencil"></i> Edit</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="box">
            <!-- /.box-header -->
            <div class="box-body">


<form class="form-horizontal" method="post">
<fieldset>

<!-- Form Name -->
<legend></legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="name">Name</label>  
  <div class="col-md-4">
  <input id="name" name="name" type="text" placeholder="" class="form-control input-md {if $name_error}input_error{/if}" value="{$edit_user.name}">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="email">Email</label>  
  <div class="col-md-4">
  <input id="email" name="email" type="email" placeholder="" class="form-control input-md {if $email_error}input_error{/if}" required="" value="{$edit_user.email}">    
	{if $email_error}
            <p class="error_text">{$email_error}</p>
        {/if}

  </div>
{if !$edit_user.email_verified}
  <div class="col-md-4">
	<p>To resend verification email <a href="/admin/users/{$edit_user.id}#email_verification">Visit the users profile</a></p>
  </div>
{/if}
</div>


<!-- Button (Double) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-8">
    <button id="submit" name="submit" class="btn btn-success">Save changes</button>
    <button id="resetform" name="resetform" class="btn btn-danger" type="reset">Reset form</button>
  </div>
</div>

</fieldset>
</form>


<p>To send the user a password reset email <a href="/admin/users/{$edit_user.id}#password_reset">Visit the users profile</a></p>


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
	$( "#logo" ).change(function() {
		logo_url=$('#logo').val();$('#logo_link').attr('href',logo_url);$('#logo_img').attr('src',logo_url);
	});
</script>
</body>
</html>
