{config_load file="smarty.conf"}

<header class="main-header">
    <!-- Logo -->
    <a href="/site" class="logo" style="{if ! $user.email_verified}background-color:red;{/if}{if ! $user.firebase_uid}background-color:red;{/if}">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>{#app_first_short_name#}</b>{#app_last_short_name#}</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>{#app_first_full_name#}</b>{#app_last_full_name#}</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" style="{if ! $user.email_verified}background-color:red;{/if}{if ! $user.firebase_uid}background-color:red;{/if}">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu" style="{if ! $user.email_verified}background-color:red;{/if}{if ! $user.firebase_uid}background-color:red;{/if}">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{$user.profile_image}" class="user-image user_profile_image" alt="User Image">
              <span class="hidden-xs">{$user.name}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{$user.profile_image}" class="img-circle user_profile_image" alt="User Image">

                <p>
                  {$user.name}
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="/profile" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a class="btn btn-default btn-flat" onclick="signout();">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>


{if ! $user.firebase_uid}
	<script>
		alert("Please sign in again to continue");
		signout();
	</script>
{/if}
