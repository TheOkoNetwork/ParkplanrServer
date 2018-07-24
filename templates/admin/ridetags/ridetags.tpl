{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Ride tags</title>
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
        Ride tags
        <small>Administration</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li class="active"><a href="/admin/ridetags/"><i class="fa fa-tags"></i> Ride tags</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">



	<div class="box">
	<div class="box-header with-border">
		<a href="/admin/ridetags/add" class="btn btn-success"><span class="fa fa-plus"></span> Add tag</a>
        </div>
            <div class="box-body">
              <table id="rides_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Tag</th>
                  <th>Park</th>
                  <th>Queue times?</th>
                  <th>Ride count?</th>
                  <th>Area?</th>
                  <th></th>
                  <th></th>
                </tr>
                </thead>
                <tbody>	
			{foreach item=tag from=$tags}
				<tr>
					<td><a href="/admin/ridetags/{$tag.id}">{$tag.tag}</a></td>
					<td><a href="{$tag.park.logo_url}"><img src="{#s3_assets_url#}/images/parks/{$tag.park.id}" width="125" title="{$tag.park.name}"></a><span hidden>{$tag.park.name}</span></td>
					<td>{if $tag.queuetimes}Queuetimes{/if}</td>
					<td>{if $tag.ridecount}Ride count{/if}</td>
					<td>{if $tag.area}Area{/if}</td>
					<td><span hidden>{if $tag.disabled}disabled{/if}</span><a class="btn btn-{if $tag.disabled}danger{else}primary{/if}" href="/admin/ridetags/{$tag.id}/edit">Edit {$tag.tag}</a></a></td>
					<td><a class="btn btn-danger" href="/admin/ridetags/{$tag.id}/delete">DELETE</a></td>
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
