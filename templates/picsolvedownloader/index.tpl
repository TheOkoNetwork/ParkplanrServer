{config_load file="smarty.conf"}

<!DOCTYPE html>
<html>
{include file="head.tpl"}
  <title>{#app_full_name#} | Picsolve | Downloader</title>
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
        Picsolve Downloader
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li class=""><a href="/site"><i class="fa fa-home"></i> Home</a></li>
        <li class=""><a href="/picsolve"><i class="fa fa-camera"></i> Picsolve</a></li>
        <li class="active"><a href="/picsolve/downloader"><i class="fa fa-gears"></i> Downloader</a></li>
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
		<p>If you are using a desktop/laptop and wish to download your Picsolve photos to your computer then please use the <a href="http://picsolvedownloader.okonetwork.org.uk">Legacy Picsolve Downloader</a></p>
		<p>please note that it will NOT have nice file names</p>
		<p>If you are using an Android device then give ParkPlanr a try, its free and has Queue times/Picsolve/Ride count/Park maps/Discount calculator and more to come! <a href='https://play.google.com/store/apps/details?id=uk.org.okonetwork.parkplanr&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1'><img alt='Get it on Google Play' src='https://play.google.com/intl/en_gb/badges/images/generic/en_badge_web_generic.png' width="25%" /></a></p>
		<hr />
		<br />
		<br />
		<br />
		{if !$user.picsolve_auth_token}
			<p>Please <a href="/picsolve/account">sign in to your <img src="https://www.picsolve.com/assets/app/images/picsolve-logo.png" alt="Picsolve"> account</a></p>
			<p>Then you will be able to have all of your Picsolve photos saved into a folder(called ParkPlanr) on your prefered Cloud provider, Currently Google Drive and Dropbox are supported</p>
		{else}
			<p>Click the button for your prefered Cloud provider to have all of your Picsolve photos saved to a folder(called ParkPlanr) on that account (just once)</p>

			<a href="/picsolve/downloader/googledrive" tooltip="Save all Picsolve photos to Google Drive"> <img src="https://developers.google.com/drive/images/drive_icon.png" /><img src="https://developers.google.com/drive/images/drive_logo.png" /></a>
			<a href="/picsolve/downloader/dropbox" tooltip="Save all Picsolve photos to Dropbox"> <img src="/img/social/dropbox.png" height="48px" /></a>
<!--
			<a href="/picsolve/downloader/onedrive" tooltip="Save all Picsolve photos to Onedrive"> <img src="/img/social/onedrive.png" height="48px" /></a>
-->
			<hr />
			<b>Auto saver</b>
			<p>By using the Auto saver ParkPlanr will automatically save all of your Picsolve Photos to your chosen cloud provider daily without you needing to click a button!</p>
			<p>Click the button for your prefered Cloud provider to have all of your Picsolve photos saved to a folder(called ParkPlanr) on that account daily.</p>

			{if in_array("GOOGLEDRIVE",$picsolvedownloader_accounts)}
				<a href="/picsolve/downloader/googledrive?cancelauto" class="btn btn-danger">Disable auto saving to Google Drive</a>
			{else}
				<a href="/picsolve/downloader/googledrive?auto=true" tooltip="Save all Picsolve photos to Google Drive"> <img src="https://developers.google.com/drive/images/drive_icon.png" /><img src="https://developers.google.com/drive/images/drive_logo.png" /></a>
			{/if}

			{if in_array("DROPBOX",$picsolvedownloader_accounts)}
				<a href="/picsolve/downloader/dropbox?cancelauto" class="btn btn-danger">Disable auto saving to Dropbox</a>
			{else}
				<a href="/picsolve/downloader/dropbox?auto=true" tooltip="Save all Picsolve photos to Dropbox"> <img src="/img/social/dropbox.png" height="48px" /></a>
			{/if}
<!--
			{if in_array("ONEDRIVE",$picsolvedownloader_accounts)}
				<a href="/picsolve/downloader/onedrive?cancelauto" class="btn btn-danger">Disable auto saving to Onedrive</a>
			{else}
				<a href="/picsolve/downloader/onedrive?auto=true" tooltip="Save all Picsolve photos to Onedrive"> <img src="/img/social/onedrive.png" height="48px" /></a>
			{/if}
-->

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
