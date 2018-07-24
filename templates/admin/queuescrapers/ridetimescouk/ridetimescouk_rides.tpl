{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Queue scrapers | Ridetimes.co.uk</title>
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
        Ridetimes.co.uk (Alton Towers Resort)
        <small>Queue scraper</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li><i class="fa fa-gear"></i> Queue scrapers</li>
        <li class="active"><a href="/admin/queuescrapers/ridetimescouk/"><i class="fa fa-fort-awesome"></i> Ridetimes.co.uk</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">



	<div class="box">
	<div class="box-header with-border">
		<a href="/admin/queuescrapers/ridetimescouk/add" class="btn btn-success"><span class="fa fa-plus"></span> Add ride</a>
        </div>
            <div class="box-body">
              <table id="rides_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Name</th>
                  <th>Ride ID</th>
                  <th></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>	
			{foreach item=ride from=$rides}
				<tr>
					<td><a href="/admin/rides/{$ride.id}">{$ride.name}</a></td>
					<td>{$ride.ride_id}</td>
					<td><a class="btn btn-primary" href="/admin/queuescrapers/ridetimescouk/{$ride.id}/edit">Edit {$ride.name}/{$ride.ride_id}</a></a></td>
					<td><a class="btn btn-danger" href="/admin/queuescrapers/ridetimescouk/{$ride.id}/delete">DELETE</a></td>
		                </tr>
			{/foreach}
                </tbody>
                <tfoot>
                <tr>
                  <th>Name</th>
                  <th>Ride ID</th>
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
      "emptyTable": "No Ridetimes.co.uk rides in the database",
      "zeroRecords": "No Ridetimes.co.uk rides match your filter"
    }
} );
</script>
</body>
</html>
