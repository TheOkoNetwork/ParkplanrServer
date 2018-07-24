{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Users</title>
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
        Users
        <small>Administration</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li class="active"><a href="/admin/users/"><i class="fa fa-users"></i> Users</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">



	<div class="box">
	<div class="box-header with-border">
        </div>
            <div class="box-body">
              <table id="users_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Avatar</th>
                  <th>Firebase</th>
                  <th></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>	
			{foreach item=edit_user from=$edit_users}
				<tr>
					<td><a href="/admin/users/{$edit_user.id}">{$edit_user.name}</a></td>
					<td><a href="/admin/users/{$edit_user.id}#email">{$edit_user.email}</a>{if !$edit_user.email_verified}*Not verified* {if isset($edit_user.expired)}Expired{/if}{/if}</td>
					<td><img src="{$edit_user.profile_image}" class="img-circle" alt="User Image" height="50px"></td>
					<td>{if $edit_user.firebase_uid}Migrated{else}Legacy{/if}</td>
					<td><a class="btn btn-primary" href="/admin/users/{$edit_user.id}/edit">Edit {$edit_user.name}</a></a></td>
					<td>{if $user.id!=$edit_user.id}<a class="btn btn-danger" href="/admin/users/{$edit_user.id}/delete">DELETE</a>{/if}</td>
		                </tr>
			{/foreach}
                </tbody>
                <tfoot>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Avatar</th>
                  <th>Admin</th>
                  <th>Firebase</th>
                  <th></th>
                  <th></th>
                </tr>
                </tfoot>
              </table>
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
	$('#users_table').DataTable( {
    "language": {
      "emptyTable": "No users in the database",
      "zeroRecords": "No users match your filter"
    }
} );
</script>
</body>
</html>
