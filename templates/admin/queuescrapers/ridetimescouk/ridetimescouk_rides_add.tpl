{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Queue scrapers | Ridetimes.co.uk | Add pairing</title>
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
        Adding pairing
        <small>Ridetimes.co.uk administration</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li><a href="/admin/queuescrapers/"><i class="fa fa-gear"></i> Queue scrapers</a></li>
        <li><a href="/admin/queuescrapers/ridetimescouk/"><i class="fa fa-fort-awesome"></i> Ridetimes.co.uk</a></li>
        <li class="active"><a href="/admin/rides/add"><i class="fa fa-plus"></i> Add</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="box">
            <!-- /.box-header -->
            <div class="box-body">


<form class="form-horizontal" method="post">
<fieldset>


<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="ride">Ride</label>
  <div class="col-md-4">
    <select id="ride" name="ride" class="form-control">
	{foreach item=ride from=$rides}
		<option value="{$ride.id}">{$ride.name}</option>
	{/foreach}
    </select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
	<label class="col-md-4 control-label" for="ride_id">Ridetimes.co.uk ID</label>  
	<div class="col-md-4">
		<input id="ride_id" name="ride_id" type="ride_id" placeholder="" class="form-control input-md" required="">
	</div>
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
	$( "#logo_url" ).change(function() {
		logo_url=$('#logo_url').val();$('#logo_link').attr('href',logo_url);$('#logo_img').attr('src',logo_url);
	});
</script>
</body>
</html>
