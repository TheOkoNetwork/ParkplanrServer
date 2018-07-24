{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Rides | {$ride.name}</title>
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
        {$ride.name}-{$ride.park.name}
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li class=""><a href="/admin/rides/"><i class="fa fa-circle-o"></i> Rides</a></li>
        <li class=""><a href="/admin/rides/{$ride.id}"><i class="fa fa-circle-o"></i> {$ride.name}</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="box">
            <!-- /.box-header -->
            <div class="box-header">
                {if current_user_has_permission("ADMIN_RIDES_EDIT")}
			<a href="/admin/rides/{$ride.id}/edit" class="btn btn-primary">Edit</a>
		{/if}
                {if current_user_has_permission("ADMIN_RIDES_EDIT")}
			<a href="/admin/rides/{$ride.id}/delete" class="btn btn-danger">Delete</a>
		{/if}
	    </div>
            <div class="box-body">
		{if $ride.queuetimes}
			<span>Queue time is currently {$ride.queuetime} minutes</span><br />
		{else}
			<span>Ride not queue times enabled</span><br />
		{/if}
                {if current_user_has_permission("ADMIN_RIDES_EDIT")}
			<br />
			<h3>Tags</h3>
		
			<form method="POST" action="/admin/rides/{$ride.id}/tags/add">
				<select id="tag" name="tag">
					<option value="0">Select a tag</option>
					{foreach item=parkridetag from=$parkridetags}
						<option value="{$parkridetag.id}">{$parkridetag.tag}</option>
					{/foreach}
				</select>
				<button type="submit" class="btn btn-success">Add tag</button>
			</form>
		{/if}

		<table id="tags_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Tag</th>
                  <th>Queuetimes</th>
                  <th>Ridecount</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>		
		{foreach item=tag from=$ride.tags}
			<tr>
				<td>{$tag.tag}</td>
				<td>{if $tag.queuetimes eq 1}Yes{else}No{/if}</td>
				<td>{if $tag.ridecount eq 1}Yes{else}No{/if}</td>
				<td><a href="/admin/rides/{$ride.id}/tags/{$tag.id}/delete" class="btn btn-danger">Remove</a></td>
			</tr>
		{/foreach}
		</tbody>
                <tfoot>
                <tr>
                  <th>Tag</th>
                  <th>Queuetimes</th>
                  <th>Ridecount</th>
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
    $('#tags_table').DataTable( {
    "language": {
      "emptyTable": "No tags for this ride in the database",
      "zeroRecords": "No tags for this ride match your filter"
    }
} );
</script>
</body>
</html>
