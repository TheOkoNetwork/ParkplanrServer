{config_load file="smarty.conf"}

  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{$user.profile_image}" class="img-circle user_profile_image" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><marquee>{$user.name}</marquee></p>
	{if !$user.email_verified}
		<p>*Email NOT verifed*</p>
	{/if}

	{if current_user_has_permission("ADMIN")}
		<p>ADMIN</p>
	{/if}

          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">Menu</li>
        <li>
          <a href="/site">
            <i class="fa fa-home"></i> <span>Home</span>
          </a>
        </li>

        <li>
          <a href="/picsolve/downloader/">
            <i class="fa fa-camera"></i> <span>Picsolve Downloader</span>
          </a>
        </li>
        <li>
          <a href="/picsolve/digipass">
            <i class="fa fa-ticket"></i> <span>Digipass</span>
          </a>
        </li>

{if $user.picsolve_ocr}
	<li>
	  <a href="/picsolve/ocr/">
	    <i class="fa fa-ticket"></i> <span>Picsolve Receipt OCR</span>
	  </a>
        </li>
{/if}
        <li>
          <a href="/ridecount">
            <i class="fa fa-circle"></i> <span>Ride count</span>
          </a>
        </li>
<!--        <li>
          <a href="/queuetimes">
            <i class="fa fa-clock"></i> <span>Queue times</span>
          </a>
        </li> -->


	{if current_user_has_permission("ADMIN")}
		<li class="treeview">
                  <a href="#">
                    <i class="fa fa-gear"></i> <span>Administration</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                   </span>
		  </a>
		   <ul class="treeview-menu">
				{if $user.admin}
					<li><a href="/admin/firebasemigration/graphs"><i class="fa fa-pie-chart"></i> Firebase Migration</a></li>
				{/if}

				{if current_user_has_permission("ADMIN_PARKS")}
					<li><a href="/admin/parks"><i class="fa fa-compass"></i> Parks</a></li>
				{/if}
				{if current_user_has_permission("ADMIN_RIDES")}
					<li><a href="/admin/rides"><i class="fa fa-circle-o"></i> Rides</a></li>
				{/if}
				{if current_user_has_permission("ADMIN_RIDETAGS")}
					<li><a href="/admin/ridetags"><i class="fa fa-tags"></i> Ride tags</a></li>
				{/if}
				{if current_user_has_permission("ADMIN_USERS")}
					<li><a href="/admin/users"><i class="fa fa-users"></i> Users</a></li>
				{/if}

				{if current_user_has_permission("ADMIN_QUEUESCRAPERS")}
					<li class="treeview">
						<a href="#">
							<i class="fa fa-gear"></i> <span>Queue scrapers*</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							{if current_user_has_permission("ADMIN_QUEUESCRAPERS_RIDETIMESCOUK")}
								<li><a href="/admin/queuescrapers/ridetimescouk/"><i class="fa fa-fort-awesome"></i> Ridetimes.co.uk</a></li>
							{/if}
							{if current_user_has_permission("ADMIN_QUEUESCRAPERS_CHESSINGTON")}
<!-- CWOA							<li><a href="/admin/queuescrapers/chessington/"><i class="fa fa-circle"></i> Chessington</a></li> -->
							{/if}
							{if current_user_has_permission("ADMIN_QUEUESCRAPERS_THORPEPARK")}
<!-- THOR							<li><a href="/admin/queuescrapers/thorpepark/"><i class="fa fa-circle"></i> Thorpe Park</a></li> -->
							{/if}
							{if current_user_has_permission("ADMIN_QUEUESCRAPERS_LEGOLANDWINDSOR")}
<!-- LLWR							<li><a href="/admin/queuescrapers/legolandwindsor/"><i class="fa fa-circle"></i> Legoland Windsor</a></li> -->
							{/if}
						</ul>
					 </li>
				{/if}
		  </ul>
		 </li>
	{/if}
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
