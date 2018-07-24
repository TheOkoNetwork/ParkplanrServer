{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Users | {$edit_user.name} | DELETE</title>
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
        {$edit_user.name} ({$edit_user.email})
        <small>DELETING</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li><a href="/admin/users/"><i class="fa fa-users"></i> Users</a></li>
        <li><a href="/admin/users/{$edit_user.id}"><i class="fa fa-user"></i> {$edit_user.name}</a></li>
        <li class="active"><a href="/admin/users/{$edit_user.id}/delete"><i class="fa fa-trash"></i> Delete</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="box">
            <!-- /.box-header -->
            <div class="box-body">


<form class="form-horizontal" method="post">
<fieldset>

<legend>Are you sure you wish to delete {$edit_user.name} ({$edit_user.email})?</legend>

<!-- Button (Double) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-8">
    <button id="submit" name="submit" class="btn btn-danger">DELETE {$edit_user.name}</button>
    <a href="/admin/users" id="cancel" name="cancel" class="btn btn-success">Cancel</a>
  </div>
</div>

</fieldset>
</form>




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
