{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Queue scrapers | Ridetimes.co.uk | {$ride.name}/{$ride.ride_id} | DELETE</title>
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
        {$ride.name}/{$ride.ride_id}
        <small>DELETING</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li><a href="/admin/queuescrapers/"><i class="fa fa-gear"></i> Queue scrapers</a></li>
        <li><a href="/admin/queuescrapers/ridetimescouk/"><i class="fa fa-fort-awesome"></i> Ridetimes.co.uk</a></li>
        <li><a href="/admin/queuescrapers/ridetimescouk/{$ride.id}"><i class="fa fa-circle"></i> {$ride.name}</a></li>
        <li class="active"><a href="/admin/queuescrapers/ridetimescouk/{$ride.id}/delete"><i class="fa fa-trash"></i> Delete</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="box">
            <!-- /.box-header -->
            <div class="box-body">


<form class="form-horizontal" method="post">
<fieldset>

<legend>Are you sure you wish to delete the pairing between {$ride.name} and ride_id {$ride.id}?</legend>

<!-- Button (Double) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-8">
    <button id="submit" name="submit" class="btn btn-danger">DELETE {$ride.name}</button>
    <a href="/admin/rides" id="cancel" name="cancel" class="btn btn-success">Cancel</a>
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

<script>
	$( "#logo" ).change(function() {
		logo_url=$('#logo').val();$('#logo_link').attr('href',logo_url);$('#logo_img').attr('src',logo_url);
	});
</script>
</body>
</html>
