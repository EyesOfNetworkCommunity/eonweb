<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
# VERSION : 5.2
# APPLICATION : eonweb for eyesofnetwork project
#
# LICENCE :
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
#########################################
*/

include("../../header.php");
include("../../side.php");

?>

<div id="page-wrapper">

	<?php
		/********************************************************
		*		FUNCTIONS DECLARATIONS			                *
		********************************************************/

		// Retrieve Group Information
		function retrieve_user_info($user_id)
		{
			global $database_eonweb;
			$user = sql($database_eonweb,"SELECT user_name, user_descr, group_id, user_passwd, user_type, user_location, user_limitation, user_language, theme FROM users WHERE user_id=?", array($user_id));
			$user = $user[0];
			return $user; 
		}

		// Display user language selection  
		function GetUserLang() {

			global $database_eonweb;
			global $user_id;
			global $path_languages;

			// definition of variables and Research language files
			$path_label_lang = "label.admin_user.user_lang_"; 
			$files = array('en');
			$handler = opendir($path_languages);

			while ($file = readdir($handler)) {
				if(preg_match('#messages-(.+).json#', $file, $matches)){
					$files[] = $matches[1];
				}
			}

			closedir($handler);
			$files = array_filter($files);
			array_unshift($files,"0");
			$files = array_unique($files);

			// creation of a select and catch values
			$langtmp = sql($database_eonweb,"SELECT user_language FROM users WHERE user_id=?", array($user_id));
			$langtmp = $langtmp[0][0];
			$res = '<select class="form-control" name="user_language">';
			foreach($files as $v) {
				if($v == $langtmp){
					$res.="<option value='".$v."' selected=selected>".getLabel($path_label_lang.$v)."</option>";
				}
				else{
					$res.="<option value='".$v."'>".getLabel($path_label_lang.$v)."</option>";
				}
			}
			$res .= '</select>';

			return $res;
		}

		// Display theme  
		function GetThemeList() {

			global $database_eonweb;
			global $user_id;
			$result = sql($database_eonweb, "SELECT `theme` FROM users WHERE user_id = ?", array($user_id));
			$result = $result[0];
			$dir = "/srv/eyesofnetwork/eonweb/themes/";
			$listTheme = scandir($dir);
			$res = '<select class="form-control" name="theme">';
			foreach($listTheme as $value) {
				if(is_dir($dir . $value)) {
					if($value != "." && $value != "..") {
						if($value == $result["theme"]){
							$res.="<option value='".$value."' selected=selected>".$value."</option>";
						}
						else if($value == "Default" && $result["theme"] == NULL){
							$res.="<option value='".$value."' id='aa' selected=selected>".$value."</option>";
						}
						else{
							$res.="<option value='".$value."'>".$value."</option>";
						}
					}
				}
			}
			$res .= '</select>';

			return $res;
		}
		
		//--------------------------------------------------------

		// Update User Information & Right
		function update_user($user_id, $user_name, $user_descr, $user_group, $user_password1, $user_password2 ,$user_type, $user_location, $user_mail, $user_limitation, $old_group_id, $old_name, $create_user_in_nagvis, $create_user_in_cacti, $nagvis_role_id, $user_language, $theme)
		{
			global $database_host;
			global $database_cacti;
			global $database_username;
			global $database_password;

			global $database_eonweb;
			global $database_lilac;
			global $path_eonweb;
			global $dir_imgcache;

			// Check if user exist
			if($user_name!=$old_name){	
				$user_exist=sql($database_eonweb,"SELECT count('user_name') from users where user_name=?", array($user_name));
				$user_exist = $user_exist[0][0];
			} else {
				$user_exist=0;
			}
			// Check user_descr
			if($user_descr=="")
				$user_descr=$user_name;

			if (($user_name != "") && ($user_name != null) && ($user_id != null) && ($user_id != "") && ($user_exist == 0)) {
				if (($user_password1 != "") && ($user_password1 != null) && ($user_password1 == $user_password2)) {

					$eonweb_groupname=sql($database_eonweb,"SELECT group_name FROM groups WHERE group_id=?", array($user_group));
					$eonweb_groupname = $eonweb_groupname[0]["group_name"];			
					$eonweb_oldgroupname=sql($database_eonweb,"SELECT group_name FROM groups WHERE group_id=?", array($old_group_id));
					$eonweb_oldgroupname = $eonweb_oldgroupname[0]["group_name"];			
					if ($user_password1 != "abcdefghijklmnopqrstuvwxyz") {
						$passwd_temp = md5($user_password1);
						// Update into eonweb
						$datas = array(
							$user_name,
							$user_descr,
							$user_group,
							$passwd_temp,
							$user_type,
							$user_location,
							$user_limitation,
							$user_language,
							$theme,
							$user_id
						);
						sql($database_eonweb,"UPDATE users set user_name=?, user_descr=?,group_id=?,user_passwd=?,user_type=?,user_location=?,user_limitation=?,user_language=?, theme=? WHERE user_id =?", $datas);
					}
					else {
						// Update into eonweb
						$datas = array(
							$user_name,
							$user_descr,
							$user_group,
							$user_type,
							$user_location,
							$user_limitation,
							$user_language,
							$theme,
							$user_id
						);
						sql($database_eonweb,"UPDATE users set user_name=?, user_descr=?,group_id=?,user_type=?,user_location=?,user_limitation=?,user_language=?, theme=? WHERE user_id =?", $datas);
					}
			
					// Update into lilac
					$lilac_userid=sql($database_lilac,"SELECT id FROM nagios_contact WHERE name=?", array($old_name));
					$lilac_userid = $lilac_userid[0]["id"];
					$lilac_groupid=sql($database_lilac,"SELECT id FROM nagios_contact_group WHERE name=?", array($eonweb_groupname));
					$lilac_groupid = $lilac_groupid[0]["id"];
					$lilac_oldgroupid = sql($database_lilac, "SELECT id FROM nagios_contact_group WHERE name=?", array($eonweb_oldgroupname));
					$lilac_oldgroupid = $lilac_oldgroupid[0]["id"];
					
					require_once('/srv/eyesofnetwork/lilac/includes/config.inc');
					$nc = NagiosContactPeer::getByName($old_name);
					if($nc){
						$nc->setName($user_name);
						$nc->setAlias($user_descr);
						$nc->setEmail($user_mail);
						$nc->save();
					}

					sql($database_lilac,"DELETE from nagios_contact_group_member WHERE contact=? and contactgroup=?", array($lilac_userid, $lilac_groupid));
					sql($database_lilac,"DELETE from nagios_contact_group_member WHERE contact=? and contactgroup=?", array($lilac_userid, $lilac_oldgroupid));
					if($lilac_groupid!="" and $lilac_userid!="" and $user_limitation!="1")
						sql($database_lilac,"INSERT into nagios_contact_group_member (contactgroup,contact) values(?, ?)", array($lilac_groupid, $lilac_userid));

					
					
					// update user into nagvis :
					$bdd = new PDO('sqlite:/srv/eyesofnetwork/nagvis/etc/auth.db');
					$req = $bdd->prepare("SELECT userId, name FROM users WHERE name=?");
					$req->execute(array($_POST["user_name_old"]));
                    $nagvis_user_exist = $req->fetch();

                    // this is nagvis default salt for password encryption security
					$nagvis_salt = '29d58ead6a65f5c00342ae03cdc6d26565e20954';

					if($nagvis_user_exist["userId"] > 0){
						// update in nagvis
						if($create_user_in_nagvis=="yes"){
							$nagvis_id = $nagvis_user_exist["userId"];
							$req = $bdd->prepare("UPDATE users SET name = ?, password = ? WHERE userId = ?");
							$req->execute(array($user_name, sha1($nagvis_salt.$passwd_temp), $nagvis_id));
							$req = $bdd->prepare("UPDATE users2roles SET roleId = ? WHERE userId = ?");
							$req->execute(array($nagvis_role_id, $nagvis_id));
						} else { // delete in nagvis
							$req = $bdd->prepare("DELETE FROM users WHERE userId = ?");
							$req->execute(array($nagvis_user_exist["userId"]));
							$req = $bdd->prepare("DELETE FROM users2roles WHERE userId = ?");
							$req->execute(array($nagvis_user_exist["userId"]));
						}
					} else{ // no user found in nagvis, so if checkbox is checked, we create
						if($create_user_in_nagvis=="yes"){
							$req = $bdd->prepare("INSERT INTO users (name, password) VALUES (?, ?)");
							$req->execute(array($user_name, sha1($nagvis_salt.$passwd_temp)));
							$nagvis_id = $bdd->lastInsertId();
							$req = $bdd->prepare("INSERT INTO users2roles (userId, roleId) VALUES (?, ?)");
							$req->execute(array($nagvis_id, $nagvis_role_id));
						}
					}

                     // Update user into cacti
                    $bdd = new PDO('mysql:host='.$database_host.';dbname='.$database_cacti, $database_username, $database_password);
					$req = $bdd->prepare("SELECT id FROM user_auth WHERE username=?");
					$req->execute(array($_POST["user_name_old"]));
                    $cacti_user_exist = $req->fetch();

                    if($cacti_user_exist["id"] > 0){
                    	$cacti_id = $cacti_user_exist["id"];
                    	if($create_user_in_cacti == "yes"){
							$req = $bdd->prepare("UPDATE user_auth SET username = ? WHERE id = ?");
							$req->execute(array($user_name, $cacti_id));
                    	} else {
							$req = $bdd->prepare("DELETE FROM user_auth WHERE id = ?");
							$req->execute(array($cacti_id));
                    	}
                    } else {
                    	if($create_user_in_cacti == "yes"){
        					$req = $bdd->prepare("INSERT INTO user_auth (username,realm,full_name,show_tree,show_list,show_preview,graph_settings,login_opts,policy_graphs,policy_trees,policy_hosts,policy_graph_templates,enabled) VALUES (?,2,?,'on','on','on','on',3,2,2,2,2,'on')");
							$req->execute(array($user_name, $user_descr));
						}
                    }
					$bdd = null;
					$req = null;
					// logging action
					logging("admin_user","UPDATE : $user_id $user_name $user_descr $user_limitation $user_group $user_type $user_location");

					// renaming files
					if($user_name!=$old_name){
						if(file_exists("$path_eonweb/$dir_imgcache/".strtolower($old_name)."-ged.xml"))
							rename("$path_eonweb/$dir_imgcache/".strtolower($old_name)."-ged.xml","$path_eonweb/$dir_imgcache/".strtolower($user_name)."-ged.xml");
					}
					message(8," : User updated",'ok');
					}
					else
						message(8," : Passwords do not match or are empty",'warning');
			}
			elseif($user_exist != 0 && $user_name!=$old_name)
				message(8," : User $user_name already exists",'warning');
			else
				message(8," : User name can not be empty",'warning');
		}

		/********************************************************
		*		END OF FUNCTIONS DECLARATIONS		*
		********************************************************/


		// Global parameter
		global $database_eonweb;
		global $database_lilac;

		// Get parameter
		$user_change_passord = retrieve_form_data("user_change_passord",null);
		$user_id = retrieve_form_data("user_id",null);

		// Secure the change password
		if (($user_change_passord != null) && ($user_id != $_COOKIE['user_id']))
			message(0,"No Access Right","critical");

		$user_location = retrieve_form_data("user_location","");
		$user_location = ldap_escape($user_location);
		$user_mail = retrieve_form_data("user_mail","");
		$user_descr = retrieve_form_data("user_descr","");
		$user_descr = htmlspecialchars($user_descr, ENT_QUOTES);
		$user_group = retrieve_form_data("user_group","");
		$user_type = retrieve_form_data("user_type","");
		$user_limitation = retrieve_form_data("user_limitation","");
		$user_language = retrieve_form_data("user_language","");
		$old_group_id = sql($database_eonweb,"select group_id from users where user_id=?", array($user_id));
		$old_group_id = $old_group_id[0]["group_id"];
		$old_name = retrieve_form_data("user_name_old","");
		$theme = retrieve_form_data("theme","");

		$create_user_in_nagvis = retrieve_form_data("create_user_in_nagvis","");
		$nagvis_role_id = retrieve_form_data("nagvis_group","");
		$create_user_in_cacti = retrieve_form_data("create_user_in_cacti","");

		if($user_type=="1"){
			$result = sql($database_eonweb,"select login from ldap_users_extended where dn=?", array($user_location));
			$username = $result[0]["login"];
			$user_name = strtolower($username);
			//message(8,"User location1: $user_location",'ok');	// For debug pupose, to be removed
			//message(8,"User name1: $user_name",'ok');		// For debug pupose, to be removed
			$user_password1 = "abcdefghijklmnopqrstuvwxyz";
			$user_password2 = "abcdefghijklmnopqrstuvwxyz";		
		}
		else{
			$user_name = retrieve_form_data("user_name",null);
			$user_name = strtolower($user_name);
			$user_password1 = retrieve_form_data("user_password1","");
			$user_password2 = retrieve_form_data("user_password2","");
		}

		if ($user_id == null) 
		{
			echo '<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">'.getLabel("label.admin_user.title_new").'</h1>
					</div>
				</div>';
			
			//------------------------------------------------------------------------------------------------
			// ACCOUNT CREATION (New user ID)
			//------------------------------------------------------------------------------------------------
			if 	(isset($_POST['add']))
			{
				$create_user_in_nagvis = retrieve_form_data("create_user_in_nagvis","");
				$create_user_in_cacti = retrieve_form_data("create_user_in_cacti","");
				if($create_user_in_nagvis == "yes"){ $nagvis_user = true; }
				else { $nagvis_user = false; }
				if($create_user_in_cacti == "yes"){ $cacti_user = true; }
				else { $cacti_user = false; }
				
				$user_group = retrieve_form_data("user_group","");
				$nagvis_grp = retrieve_form_data("nagvis_group", "");
				$user_id=insert_user(stripAccents($user_name), $user_descr, $user_group, $user_password1, $user_password2, $user_type, $user_location,$user_mail,$user_limitation, true, $create_user_in_nagvis, $create_user_in_cacti, $nagvis_grp, $user_language, $theme);
				//message(8,"User location: $user_location",'ok');	// For debug pupose, to be removed
				
				// Retrieve Group Information from database
				if($user_id){
					$user_name_descr = retrieve_user_info($user_id);
					$user_name=$user_name_descr["user_name"];
					$user_mail=sql($database_lilac,"SELECT email FROM nagios_contact WHERE name=?", array($user_name));
					$user_mail = $user_mail[0]["email"];
					$user_descr=$user_name_descr["user_descr"];
					$user_group=$user_name_descr["group_id"];
					$user_type=$user_name_descr["user_type"];
					$user_limitation = retrieve_form_data("user_limitation","");
					$user_language = retrieve_form_data("user_language","");
					$user_location=$user_name_descr["user_location"];
					$user_password1= "abcdefghijklmnopqrstuvwxyz";
					$user_password2= "abcdefghijklmnopqrstuvwxyz";
					$theme = retrieve_form_data("theme","");
				}
			}
			//------------------------------------------------------------------------------------------------
		}
		else
		{
			echo '<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">'.getLabel("label.admin_user.title_upd").'</h1>
					</div>
				</div>';

			//------------------------------------------------------------------------------------------------
			// ACCOUNT UPDATE (and retrieve parameters)
			//------------------------------------------------------------------------------------------------
			if (isset($_POST['update'])){
				update_user($user_id, stripAccents($user_name), $user_descr, $user_group, $user_password1, $user_password2, $user_type, $user_location, $user_mail, $user_limitation, $old_group_id, $old_name, $create_user_in_nagvis, $create_user_in_cacti, $nagvis_role_id, $user_language, $theme);	
				//message(8,"Update: User location = $user_location",'ok');	// For debug pupose, to be removed
				//message(8,"Update: User name =  $user_name",'ok');			// For debug pupose, to be removed
			}

			// Retrieve Group Information from database
			$user_name_descr = retrieve_user_info($user_id);
			$user_name=$user_name_descr["user_name"];
			$user_mail=sql($database_lilac,"SELECT email FROM nagios_contact WHERE name=?", array($user_name));
			$user_mail = $user_mail[0]["email"];
			$user_descr=$user_name_descr["user_descr"];
			$user_group=$user_name_descr["group_id"];
			$user_type=$user_name_descr["user_type"];
			$user_limitation=$user_name_descr["user_limitation"];
			$user_location=$user_name_descr["user_location"];
			$user_password1="abcdefghijklmnopqrstuvwxyz";
			$user_password2="abcdefghijklmnopqrstuvwxyz";

			// search the user in Cacti (to check the checkbox if he's found)
			$cacti_user = sql($database_cacti, "SELECT id FROM user_auth WHERE username =?", array($user_name));
			$cacti_user = $cacti_user[0];
			$cacti_user_found = $cacti_user;
			if($cacti_user_found != NULL){ $cacti_user = true; }
			else { $cacti_user = false; }

			// search the user in Nagvis (to check the checkbox if he's found)
			$bdd = new PDO('sqlite:/srv/eyesofnetwork/nagvis/etc/auth.db');
			$req = $bdd->prepare("SELECT count(*) FROM users WHERE name=?");
			$req->execute(array($user_name));
            $nagvis_user_exist = $req->fetch();
            if ($nagvis_user_exist["count(*)"] > 0){ $nagvis_user = true; }
            else { $nagvis_user = false; }

			//message(8,"Mod: User location = $user_location",'ok');       // For debug pupose, to be removed
			//message(8,"Mod: User name =  $user_name",'ok');                      // For debug pupose, to be removed

			//------------------------------------------------------------------------------------------------
		}

		// search all nagvis groups
		$bdd = new PDO('sqlite:/srv/eyesofnetwork/nagvis/etc/auth.db');
		$req = $bdd->query("SELECT * FROM roles");
		$nagvis_groups = $req->fetchAll(PDO::FETCH_OBJ);

		// get userId in Nagvis
		$req = $bdd->prepare("SELECT userId from users WHERE name = ?");
		$req->execute(array($user_name));
		$result = $req->fetch(PDO::FETCH_OBJ);

		$id_nagvis = false;
		$role_id = false;
		if($result){
			$id_nagvis = $result->userId;
			$req = $bdd->prepare("SELECT roleId FROM users2roles WHERE userId = ?");
			$req->execute(array($id_nagvis));
			$result = $req->fetch(PDO::FETCH_OBJ);

			if($result){
				$role_id = $result->roleId;
			}
		}
	?>

	<form id="form_user" action='./add_modify_user.php' method='POST' name='form_user'>
		<input type='hidden' name='user_id' value='<?php echo $user_id?>'>
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_name") ?></label>
			<div class="col-md-9">
				<input class="form-control" type='text' name='user_name' value='<?php echo $user_name?>'>
				<input type='hidden' name='user_name_old' value='<?php echo $user_name?>'>
			</div>
		</div>
			
		<?php if($user_id!="1"){ ?>
			<div class="row form-group">
				<label class="col-md-3"><?php echo getLabel("label.admin_user.user_limit"); ?></label>
				<div class="col-md-9">
					<?php
						if($user_limitation=="1") $checked="checked='yes'";
						else $checked="";
						echo "<input type='checkbox' class='checkbox' name='user_limitation' value='1' $checked onclick='disable_group()'>";
					?>
				</div>
			</div>
			
			<div class="row form-group">
				<label class="col-md-3"><?php echo getLabel("label.admin_user.user_ldap"); ?></label>
				<div class="col-md-9">
					<?php
						if($user_type=="1") $checked="checked='checked'";
						else $checked="";
						echo "<input type='checkbox' class='checkbox' name='user_type' value='1' $checked onclick='disable()'>";
					?>
				</div>
			</div>
			
			<div class="row form-group">
				<label class="col-md-3"><?php echo getLabel("label.admin_user.ldap_log"); ?></label>
				<div class="col-md-9">
					<?php
						echo "<input class='form-control' id='user_location' name='user_location' type='text' value='".htmlspecialchars($user_location, ENT_QUOTES)."'>";
					?>
				</div>
			</div>
		<?php 
		} 
		else {
			echo "<input type='hidden' name='user_type' value='0'>";
			echo "<input type='hidden' name='user_group' value='1'>";
			echo "<input type='hidden' name='create_user_in_nagvis' value='yes'>";
			echo "<input type='hidden' name='nagvis_group' value='1'>";
		}
		?>
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_mail"); ?></label>
			<div class="col-md-9">
				<input class="form-control" type='text' name='user_mail' value='<?php echo $user_mail?>'>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_desc"); ?></label>
			<div class="col-md-9">
				<input class="form-control" type='text' name='user_descr' value='<?php echo $user_descr?>'>
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_pwd"); ?></label>
			<div class="col-md-9">
				<input class="form-control" type='password' name='user_password1' value='<?php echo $user_password1?>'>
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_pwd2"); ?></label>
			<div class="col-md-9">
				<input class="form-control" type='password' name='user_password2' value='<?php echo $user_password2?>'>
			</div>
		</div>
		
		<!-- Adding a language defined by user -->
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_lang"); ?></label>
			<div class="col-md-9">
				<?php echo GetUserLang(); ?>
			</div>
		</div>

		<!-- Choose a theme -->
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_theme"); ?></label>
			<div class="col-md-9">
				<?php echo GetThemeList(); ?>
			</div>
		</div>
		
		<!-- If not user admin -->
		<?php if($user_id!="1") { ?>
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_group"); ?></label>
			<div class="col-md-9">
				<select class="form-control" name='user_group' size=1>
					<?php
						$result=sql($database_eonweb,"SELECT group_id,group_name from groups");
						foreach($result as $line){
							if ($user_group == $line[0])
								echo "<OPTION value='$line[0]' SELECTED>$line[1] </OPTION>";
							else
								echo "<OPTION value='$line[0]'>$line[1] </OPTION>";					
						}
					?>
				</select>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_nagvis"); ?></label>
			<div class="col-md-9">
				<div class="input-group col-md-5">
					<span class="input-group-addon">
		                <?php
							if(isset($nagvis_user) && $nagvis_user=="yes") $checked="checked='checked'";
		                    else $checked="";
		                    echo "<input type='checkbox' class='checkbox' name='create_user_in_nagvis' value='yes' $checked>";
						?>
					</span>
					<select class="form-control" name="nagvis_group">
						<?php foreach ($nagvis_groups as $group):
							$selected = "";
							if(!isset($_GET["user_id"]) && $group->name == "Guests" && !$role_id){
								$selected = "selected";
							}
							if($role_id == $group->roleId){
								$selected = "selected";
							}
						?>
							<option value="<?php echo $group->roleId; ?>" <?php echo $selected; ?>><?php echo $group->name; ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_user.user_cacti"); ?></label>
			<div class="col-md-9">
				<?php
					if(isset($cacti_user) && $cacti_user == "yes") $checked = "checked='checked'";
                    else $checked = "";
                    echo "<input type='checkbox' class='checkbox' name='create_user_in_cacti' value='yes' $checked>";
				?>
			</div>
		</div>
		
		<?php } ?>
		<div class="form-group">
			<?php
				if ($user_id !=null)
					echo "<button class='btn btn-primary' type='submit' name='update' value='update'>".getLabel("action.update")."</button>";
				else
					echo "<button class='btn btn-primary' type='submit' name='add' value='add'>".getLabel("action.add")."</button>";
				echo "<button class='btn btn-default' style='margin-left: 10px;' type='button' name='back' value='back' onclick='location.href=\"index.php\"'>".getLabel("action.cancel")."</button>";
			?>
		</div>
	</form>

</div>

<?php include("../../footer.php"); ?>
