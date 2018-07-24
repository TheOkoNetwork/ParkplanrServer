{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Rides</title>
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
        Rides
        <small>Administration</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li class="active"><a href="/admin/rides/"><i class="fa fa-circle-o"></i> Rides</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">



	<div class="box">
	<div class="box-header with-border">
		{if current_user_has_permission("ADMIN_RIDES_ADD")}
			<a href="/admin/rides/add" class="btn btn-success"><span class="fa fa-plus"></span> Add ride</a>
		{/if}
        </div>
            <div class="box-body">
              <table id="rides_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Name</th>
                  <th>Slogan</th>
                  <th>Logo</th>
                  <th>Park</th>
                </tr>
                </thead>
                <tbody>	
			{foreach item=ride from=$rides}
				<tr>
					<td><a href="/admin/rides/{$ride.id}">{$ride.name}</a></td>
					<td>{$ride.slogan}</td>
					<td><span style="display:none;">{$ride.logo_url}</span><a href="{$ride.logo_url}"><img src="{#s3_assets_url#}/images/rides/{$ride.id}" width="62.5" onerror="$(this).hide();"></a></td>
					<td><a href="/admin/parks/{$ride.park.id}"><img src="{#s3_assets_url#}/images/parks/{$ride.park.id}" height="62.5" title="{$ride.park.name}"></a><span hidden>{$ride.park.name}</span></td>
		                </tr>
			{/foreach}
                </tbody>
                <tfoot>
                <tr>
                  <th>Name</th>
                  <th>Slogan</th>
                  <th>Logo</th>
                  <th>Park</th>
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
	$('#rides_table').DataTable( {
    "language": {
      "emptyTable": "No rides in the database",
      "zeroRecords": "No rides match your filter"
    }
} );
</script>
</body>
</html>
