{config_load file="smarty.conf"}
{debug}


<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Permission Denied!</title>
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
	Permission denied
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="/site"><i class="fa fa-home"></i> Home</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

<div class="error-page">
        <h2 class="headline text-red">403</h2>

        <div class="error-content">
          <h3><i class="fa fa-warning text-red"></i> You dont have permission to do that</h3>
	  <p>Required permission:{$permission}</p>
          <p>Please contact an administrator if this is incorrect</p>
          <p>Meanwhile, you may <a href="/site">return to the dashboard</a></p>

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
