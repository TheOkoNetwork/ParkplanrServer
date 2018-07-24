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
		<p>Now processing {$job_file_count} images in OCR job {$ocr_job.id}</p>
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
