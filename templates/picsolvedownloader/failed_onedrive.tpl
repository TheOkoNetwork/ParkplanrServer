{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Onedrive error!</title>
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
        Error code:ONEDRIVE_FAIL
      </h1>
      <ol class="breadcrumb">
        <li><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/picsolve"><i class="fa fa-camera"></i> Picsolve</a></li>
        <li><a href="/picsolve/downloader"><i class="fa fa-gears"></i> Downloader</a></li>
        <li class="active">Dropbox</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="error-page">
        <h2 class=" text-red">ONEDRIVE_FAIL</h2>

        <div class="error-content">
          <h3><i class="fa fa-warning text-red"></i> Oops! Something went wrong.</h3>

          <p>
	    Sorry but something went wrong, please try again later.
	    If the issues occurs again please email support 

<a href="mailto:{#app_support_email#}?subject=Received%20error%20code%3ADROPBOX_FAIL&body=I%20received%20the%20error%20code%3ADROPBOX_FAIL%0AI%20was%20...
Please%20tell%20us%20what%20you%20were%20doing%20when%20the%20error%20occurred
%0ATimestamp%3A{$smarty.now}%20%0AUser ID%3A{$user.id}%20">{#app_support_email#}</a> quoting the error code and your User ID ({$user.id})</p>
            Meanwhile, you may <a href="/picsolve/downloader">Try again</a> or <a href="/site">return home</a>
          </p>
        </div>
      </div>
      <!-- /.error-page -->

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
