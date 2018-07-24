{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Ride tags | Adding Ride tag</title>
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
        Adding a new ride
        <small>rides administration</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin/"><i class="fa fa-gear"></i> Administration</a></li>
        <li class=""><a href="/admin/ridetags/"><i/ class="fa fa-tags"></i> Ride tags</a></li>
        <li class="active"><a href="/admin/ridetags/add"><i class="fa fa-plus"></i> Add</a></li>
     </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="box">
            <!-- /.box-header -->
            <div class="box-body">


<form class="form-horizontal" method="post">
<fieldset>

<!-- Form Name -->
<legend></legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="tag">Tag</label>  
  <div class="col-md-4">
  <input id="tag" name="tag" type="text" placeholder="" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="park">Park</label>
  <div class="col-md-4">
    <select id="park" name="park" class="form-control">
	{foreach item=park from=$parks}
		<option value="{$park.id}">{$park.name}</option>
	{/foreach}
    </select>
  </div>
</div>


<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="queuetimes">Queue times</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="queuetimes-0">
      <input type="radio" name="queuetimes" id="queuetimes-0" value="1" checked="checked">
      Yes
    </label>
        </div>
  <div class="radio">
    <label for="queuetimes-1">
      <input type="radio" name="queuetimes" id="queuetimes-1" value="0">
      No
    </label>
        </div>
  </div>
</div>

<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="ridecount">Ride count</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="ridecount-0">
      <input type="radio" name="ridecount" id="ridecount-0" value="1" checked="checked">
      Yes
    </label>
        </div>
  <div class="radio">
    <label for="ridecount-1">
      <input type="radio" name="ridecount" id="ridecount-1" value="0">
      No
    </label>
        </div>
  </div>
</div>

<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="area">Area</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="area-0">
      <input type="radio" name="area" id="area-0" value="1">
      Yes
    </label>
        </div>
  <div class="radio">
    <label for="area-1">
      <input type="radio" name="area" id="area-1" value="0" checked="checked">
      No
    </label>
        </div>
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
