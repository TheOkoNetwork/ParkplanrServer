{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Picsolve | Picsolve account | Unlink</title>
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
        Unlink Picsolve account
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li class=""><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li class=""><a href="/picsolve"<i class="fa fa-camera"></i> Picsolve</a></li>
        <li class=""><a href="/picsolve/picsolveaccount"><i class="fa fa-user"></i> Picsolve account</a></li>
        <li class=""><a href="/picsolve/picsolveaccount"><i class="fa fa-trash"></i> Unlink</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
		<!-- stuff here -->
	  </h3>
        </div>
        <div class="box-body">
			<p>By unlinking your Picsolve account from {#app_full_name#}</p>
			<p>Your digipasses will no longer automagically claimed</p>
			<p>Photo's will not be saved to your cloud storage accounts, but photos currently in those accounts will remain</p>
			<br />
			<form class="form-horizontal" METHOD="POST">
				<fieldset>
					<!-- Button (Double) -->
					<div class="form-group">
						<label class="col-md-4 control-label" for="submit"></label>
						<div class="col-md-4">
							<button id="Submit" name="Submit" class="btn btn-danger">Unlink Picsolve account</button>
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
