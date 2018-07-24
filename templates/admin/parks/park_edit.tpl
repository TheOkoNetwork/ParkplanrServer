{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Admin | Parks | {$park.name} | Edit</title>
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
        {$park.name}
        <small>Editing</small>
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
            <!-- /.box-header -->
            <div class="box-body">


<form class="form-horizontal" method="post">
<fieldset>

<!-- Form Name -->
<legend></legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="name">Name</label>  
  <div class="col-md-4">
  <input id="name" name="name" type="text" placeholder="" class="form-control input-md" required="" value="{$park.name}">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="slogan">Slogan</label>  
  <div class="col-md-4">
  <input id="slogan" name="slogan" type="text" placeholder="" class="form-control input-md" value="{$park.slogan}">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="website">Website</label>  
  <div class="col-md-4">
  <input id="website" name="website" type="text" placeholder="" class="form-control input-md" required="" value="{$park.website}">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="lat">Latitude</label>
  <div class="col-md-4">
  <input id="lat" name="lat" type="text" placeholder="" class="form-control input-md" required="" value="{$park.lat}">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="lon">Longitude</label>
  <div class="col-md-4">
  <input id="lon" name="lon" type="text" placeholder="" class="form-control input-md" required="" value="{$park.lon}">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="logo_url">Logo URL</label>  
  <div class="col-md-4">
  <input id="logo_url" name="logo_url" type="text" placeholder="" class="form-control input-md" required="" value="{$park.logo_url}">
  </div>
  <div class="col-md-1">
	<a href="{$park.logo_url}" id="logo_link"><img id="logo_img" src="{$park.logo_url}" height="25"></a>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="map_url">map URL</label>  
  <div class="col-md-4">
  <input id="map_url" name="map_url" type="text" placeholder="" class="form-control input-md" value="{$park.map_url}">
  </div>
  <div class="col-md-1">
	<a href="{$park.map_url}" id="map_link" target="_blank">Click here to view the map</a>
  </div>
</div>

<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="queuetimes">Queuetimes</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="queuetimes-0">
      <input type="radio" name="queuetimes" id="queuetimes-0" value="1" {if $park.queuetimes}checked="checked"{/if}>
      Yes
    </label>
        </div>
  <div class="radio">
    <label for="queuetimes-1">
      <input type="radio" name="queuetimes" id="queuetimes-1" value="0" {if !$park.queuetimes}checked="checked"{/if}>
      No
    </label>
        </div>
  </div>
</div>

<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="disabled">Disabled</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="disabled-0">
      <input type="radio" name="disabled" id="disabled-0" value="1" {if $park.disabled}checked="checked"{/if}>
      Yes
    </label>
        </div>
  <div class="radio">
    <label for="disabled-1">
      <input type="radio" name="disabled" id="disabled-1" value="0" {if !$park.disabled}checked="checked"{/if}>
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
	$( "#map_url" ).change(function() {
		map_url=$('#map_url').val();$('#map_link').attr('href',map_url);
	});
</script>
</body>
</html>
