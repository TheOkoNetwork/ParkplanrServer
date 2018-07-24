{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Ride count</title>
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
        Ride count
        <small>Trip to {$ridecount_trip.park.name} on {$ridecount_trip.date|date_format:"%A, %B %e, %Y"}</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/ridecount/"><i class="fa fa-circle"></i> Ride count</a></li>
        <li class="active"><a href="/ridecount/{$ridecount_trip.id}"><i class="fa fa-circle"></i> Trip</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">



	<div class="box">
	<div class="box-header with-border">
<!--		<a href="/admin/ridecount_trips/add" class="btn btn-success"><span class="fa fa-plus"></span> Add ridecount_trip</a> -->
        </div>
            <div class="box-body">
              <table id="ridecount_trips_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Ride</th>
                  <th>Count</th>
                </tr>
                </thead>
                <tbody>	
			{foreach item=$ridecount_ride from=$ridecount_rides}
				<tr>
					<td><span hidden>{$ridecount_ride.ride.name}</span><img src="{#s3_assets_url#}/images/rides/{$ridecount_ride.ride.id}" height="125" tooltip="{$ridecount_ride.ride.name}"></td>
					<td>{$ridecount_ride.ride_count}</td>
		                </tr>
			{/foreach}
                </tbody>
                  <th>Ride</th>
                  <th>Count</th>
                <tfoot>
                <tr>
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
	$('#ridecount_trips_table').DataTable( {
    "language": {
      "emptyTable": "You don't have any ride counts",
      "zeroRecords": "No ride counts match your filter"
    }
} );
</script>
</body>
</html>
