{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Welcome!</title>
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
        Welcome to {#app_full_name#}
        <small>it all starts here</small>
      </h1>
      <ol class="breadcrumb">
        <li class="active"><a href="/site"><i class="fa fa-home"></i> Home</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Welcome!</h3>
        </div>
        <div class="box-body">
<!--		<h4>Due to a legal dispute Picsolve functionality is currently unavailable</h4><br />
		<p>We hope to get Picsolve functionality back into ParkPlanr as soon as possible</p>
		<p>Please follow the <a href="https://parkplanr.okonetwork.org.uk/site/2018/06/14/the-situation-so-far/">ParkPlanr site</a> for more information and update</p>
		<br />
		<hr />
-->		<p>we're so glad you're here</p>
		<p>With {#app_full_name#} you can</p>
		<ul>
			<li>Keep track of what rides you go on</li>
<!--			<li>Get alerted if a ride you want to go on reopens after downtime, or the queue time drops</li> -->
			<li>Automatically add photos from your Merlin&reg; annual digipass&trade; to your Picsolve&reg; account.</li>
			<li>Download photos from your Picsolve&reg; account in bulk</li>
<!--			<li>Keep important documents such as booking confirmations or tickets on hand</li> -->
			<li>Calculate discounts</li>
		</ul>
		<p></p>
		<p>We hope you don't have any issues, but if you do, or want to suggest a new feature, then feel free to drop us an email:<a href="mailto:{#app_support_email#}">{#app_support_email#}</a>
		{if !$user.email_verified}
		<p><b>Important!</b> We have sent an email to:{$user.email} to confirm your email address, please click the link in the email to verify your address.</p>
		<p>If you do not verify your email address within a week your account may be terminated, if you have not received the email, or the address we have is incorrect please visit your <a href="/profile">profile</a></p>
		{/if}
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
