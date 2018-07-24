<?php
	//I contain functions for checking if a user has a certain permission
	function user_has_permission($user_id,$permission_permission) {
		//check the user actually exists
		$user=DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $user_id);
		if ($user) {
			//lookup permission
			$permission=DB::queryFirstRow("SELECT * FROM permissions WHERE permission=%s", $permission_permission);

			if ($permission) {
				//lookup what roles the user has
				$user_roles=DB::queryFirstColumn("SELECT role FROM user_roles WHERE user=%i", $user['id']);

				//lookup what roles have that permission
				$roles_with_permission=DB::queryFirstColumn("SELECT role FROM role_permissions WHERE permission=%i", $permission['id']);

				//loop over all roles the user has
				foreach ($user_roles as $user_role) {
					//does the role have the permission?
					if (in_array($user_role,$roles_with_permission)) {
						//role has permission
						return true;
					};
				};
				//none of the users role have that permission
				return false;
			} else {
				//Permission does not exist
				return false;
			};
		} else {
			//user does not exist
			return false;
		};
	};
	function current_user_has_permission($permission_permission) {
		return user_has_permission($_SESSION['parkplanr']['user']['id'],$permission_permission);
	};


	function web_require_permission($permission_permission) {
		global $smarty;

		if(user_has_permission($_SESSION['parkplanr']['user']['id'],$permission_permission)) {
			return true;
		} else {
			$smarty->assign('permission',$permission_permission);
			$smarty->display('permission_denied.tpl');
        		die();
		};
	};
?>
