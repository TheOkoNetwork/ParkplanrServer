{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Picsolve | Receipt OCR</title>
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
	OCR
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li class=""><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li class=""><a href="/picsolve"<i class="fa fa-camera"></i> Picsolve</a></li>
        <li class="active"><a href="/picsolve/ocr"><i class="fa fa-ticket"></i> Receipt OCR</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
		Picsolve Receipt OCR
	  </h3>
        </div>
        <div class="box-body">
			<p>Please note, receipt OCR'ing is highly experimental</p>
			<p>Upload a photo of the claim code section of the receipt and in a few minutes you will receive a notification listing the photos added</p>

			<form class="form-horizontal" method="POST" enctype="multipart/form-data">
				<fieldset>

					<!-- File Button --> 
					<div class="form-group">
					  <label class="col-md-4 control-label" for="images">Receipt photo's</label>
					  <div class="col-md-4">
					    <input id="images" name="images[]" class="input-file" type="file" accept="image/*" multiple>
					  </div>
					</div>

					<!-- Button (Double) -->
					<div class="form-group">
						<label class="col-md-4 control-label" for="submit"></label>
						<div class="col-md-4">
							<button id="Submit" name="Submit" class="btn btn-success">Upload file</button>
						</div>
					</div>
				</fieldset>
			</form>
        </div>
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
</body>
</html>
