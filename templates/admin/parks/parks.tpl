{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Parks</title>
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
        Parks
        <small>Administration</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li class="active"><a href="/admin/parks/"><i class="fa fa-compass"></i> Parks</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">



	<div class="box">
	<div class="box-header with-border">
	        {if current_user_has_permission("ADMIN_PARKS_ADD")}
			<a href="/admin/parks/add" class="btn btn-success"><span class="fa fa-plus"></span> Add park</a>
		{/if}
        </div>
            <div class="box-body">
              <table id="parks_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Name</th>
                  <th>Slogan</th>
                  <th>Website</th>
                  <th>Logo</th>
	          {if current_user_has_permission("ADMIN_PARKS_EDIT")}
			<th></th>
		  {/if}
	          {if current_user_has_permission("ADMIN_PARKS_DELETE")}
			<th></th>
		  {/if}
                </tr>
                </thead>
                <tbody>	
			{foreach item=park from=$parks}
				<tr>
					<td>{$park.name}</td>
					<td>{$park.slogan}</td>
					<td><a href="{$park.website}">{$park.website}</a></td>
					<td><a href="{$park.logo_url}"><img src="{#s3_assets_url#}/images/parks/{$park.id}" height="125"></a></td>
				        {if current_user_has_permission("ADMIN_PARKS_EDIT")}
						<td><a class="btn btn-{if $park.disabled}danger{else}primary{/if}" href="/admin/parks/{$park.id}/edit">Edit {$park.name}</a></a></td>
					{/if}
				        {if current_user_has_permission("ADMIN_PARKS_DELETE")}
						<td><a class="btn btn-danger" href="/admin/parks/{$park.id}/delete">DELETE</a></td>
					{/if}
		                </tr>
			{/foreach}
                </tbody>
                <tfoot>
                <tr>
                  <th>Name</th>
                  <th>Slogan</th>
                  <th>Website</th>
                  <th>Logo</th>
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
	$('#parks_table').DataTable( {
    "language": {
      "emptyTable": "No parks in the database",
      "zeroRecords": "No parks match your filter"
    }
} );
</script>
</body>
</html>
