<?php
/*
#########################################
#
# Copyright (C) 2014 EyesOfNetwork Team
# DEV NAME : Jean-Philippe Levy
# VERSION 4.2
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

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<script src="../../js/jquery.js"></script>
		<link rel="stylesheet" href="../../css/jquery.autocomplete.css" type="text/css" />
		<script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>
		<?php include("../../include/include_module.php"); ?>
	</head>

	<body id='main'>

		<script>
			function disable(){
				if(document.form_user.user_name.disabled){
					document.form_user.user_name.disabled=false;
					document.form_user.user_password1.disabled=false;
					document.form_user.user_password2.disabled=false;
					document.form_user.user_location.disabled=true;
				}
				else{
					document.form_user.user_name.disabled=true;
					document.form_user.user_password1.disabled=true;
					document.form_user.user_password2.disabled=true;
					document.form_user.user_location.disabled=false;
				}
			}
			function disable_group(){
				if(document.form_user.user_group.disabled){
					document.form_user.user_group.disabled=false;
				}
				else{
					document.form_user.user_group.disabled=true;
				}
			}
		</script>

		<?php
		
		/********************************************************
		*		FUNCTIONS DECLARATIONS			*
		********************************************************/

		// Retrieve Group Information
		function retrieve_user_info($user_id)
		{
			global $database_eonweb;
			return sqlrequest("$database_eonweb","SELECT user_name, user_descr, group_id, user_passwd, user_type, user_location, user_limitation  FROM users WHERE user_id='$user_id'");
		}

		//--------------------------------------------------------

		// Update User Information & Right
		function update_user($user_id, $user_name, $user_descr, $user_group, $user_password1, $user_password2 ,$user_type, $user_location, $user_mail, $user_limitation, $old_group_id, $old_name)
		{
			global $database_eonweb;
			global $database_lilac;
			global $path_eonweb;
			global $dir_imgcache;

			// Check if user exist
			if($user_name!=$old_name)	
				$user_exist=mysqli_result(sqlrequest("$database_eonweb","SELECT count('user_name') from users where user_name='$user_name';"),0);
			else
				$user_exist=0;

			// Check user_descr
			if($user_descr=="")
				$user_descr=$user_name;

			if (($user_name != "") && ($user_name != null) && ($user_id != null) && ($user_id != "") && ($user_exist == 0)) {
				if (($user_password1 != "") && ($user_password1 != null) && ($user_password1 == $user_password2)) {

					$eonweb_groupname=mysqli_result(sqlrequest("$database_eonweb","SELECT group_name FROM groups WHERE group_id='$user_group'"),0,"group_name");			
					$eonweb_oldgroupname=mysqli_result(sqlrequest("$database_eonweb","SELECT group_name FROM groups WHERE group_id='$old_group_id'"),0,"group_name");			
					if ($user_password1 != "abcdefghijklmnopqrstuvwxyz") {
						$passwd_temp = md5($user_password1);
						// Update into eonweb
						sqlrequest("$database_eonweb","UPDATE users set user_name='$user_name', user_descr='$user_descr',group_id='$user_group',user_passwd='$passwd_temp',user_type='$user_type',user_location='$user_location',user_limitation='$user_limitation' WHERE user_id ='$user_id'");
					}
					else {
						// Update into eonweb
						sqlrequest("$database_eonweb","UPDATE users set user_name='$user_name', user_descr='$user_descr',group_id='$user_group',user_type='$user_type',user_location='$user_location',user_limitation='$user_limitation' WHERE user_id ='$user_id'");
					}
			
					// Update into lilac
					$lilac_userid=mysqli_result(sqlrequest("$database_lilac","SELECT id FROM nagios_contact WHERE name='$old_name'"),0,"id");
					$lilac_groupid=mysqli_result(sqlrequest("$database_lilac","SELECT id FROM nagios_contact_group WHERE name='$eonweb_groupname'"),0,"id");
					$lilac_oldgroupid=mysqli_result(sqlrequest("$database_lilac","SELECT id FROM nagios_contact_group WHERE name='$eonweb_oldgroupname'"),0,"id");

					sqlrequest("$database_lilac","UPDATE nagios_contact set name='".str_replace(","," ",$user_name)."', alias='$user_descr', email='$user_mail' WHERE name ='$old_name'");
					sqlrequest("$database_lilac","DELETE from nagios_contact_group_member WHERE contact='$lilac_userid' and contactgroup='$lilac_groupid'");
					sqlrequest("$database_lilac","DELETE from nagios_contact_group_member WHERE contact='$lilac_userid' and contactgroup='$lilac_oldgroupid'");
					if($lilac_groupid!="" and $lilac_userid!="" and $user_limitation!="1")
						sqlrequest("$database_lilac","INSERT into nagios_contact_group_member (contactgroup,contact) values('$lilac_groupid','$lilac_userid')");

					// logging action
					logging("admin_user","UPDATE : $user_id $user_name $user_descr $user_limitation $user_group $user_type $user_location");

					// renaming files
					if($user_name!=$old_name){
						foreach (glob("$path_eonweb/$dir_imgcache/$old_name*.png") as $filename)
							unlink($filename);
						if(file_exists("$path_eonweb/$dir_imgcache/$old_name-ged.xml"))
							rename("$path_eonweb/$dir_imgcache/$old_name-ged.xml","$path_eonweb/$dir_imgcache/$user_name-ged.xml");
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
		$old_group_id = mysqli_result(sqlrequest($database_eonweb,"select group_id from users where user_id='$user_id'"),0,"group_id");
		$old_name = retrieve_form_data("user_name_old","");

		if($user_type=="1"){
			$result = sqlrequest($database_eonweb,"select login from ldap_users_extended where dn='$user_location'");
			$username = mysqli_result($result,0,"login");
			$user_name = strtolower($username);
			//message(8,"User location1: $user_location",'ok');	// For debug pupose, to be removed
			//message(8,"User name1: $user_name",'ok');		// For debug pupose, to be removed
			$user_password1 = "abcdefghijklmnopqrstuvwxyz";
			$user_password2 = "abcdefghijklmnopqrstuvwxyz";		
		}
		else{
			$user_name = retrieve_form_data("user_name",null);
			$user_password1 = retrieve_form_data("user_password1","");
			$user_password2 = retrieve_form_data("user_password2","");
		}

		if ($user_id == null) 
		{
			echo "<h1>".$xmlmodules->getElementsByTagName("admin_user")->item(0)->getAttribute("new")."</h1>";
			
			//------------------------------------------------------------------------------------------------
			// ACCOUNT CREATION (New user ID)
			//------------------------------------------------------------------------------------------------
			if 	(isset($_POST['add']))
			{
				$user_group = retrieve_form_data("user_group","");
				$user_id=insert_user(stripAccents($user_name), $user_descr, $user_group, $user_password1, $user_password2, $user_type, $user_location,$user_mail,$user_limitation, true);
				//message(8,"User location: $user_location",'ok');	// For debug pupose, to be removed

				// Retrieve Group Information from database
				if($user_id){
					$user_name_descr = retrieve_user_info($user_id);
					$user_name=mysqli_result($user_name_descr,0,"user_name");
					$user_mail=mysqli_result(sqlrequest("$database_lilac","SELECT email FROM nagios_contact WHERE name='$user_name'"),0,"email");
					$user_descr=mysqli_result($user_name_descr,0,"user_descr");
					$user_group=mysqli_result($user_name_descr,0,"group_id");
					$user_type=mysqli_result($user_name_descr,0,"user_type");
					$user_limitation = retrieve_form_data("user_limitation","");
					$user_location=mysqli_result($user_name_descr,0,"user_location");
					$user_password1= "abcdefghijklmnopqrstuvwxyz";
					$user_password2= "abcdefghijklmnopqrstuvwxyz";
				}
			}
			//------------------------------------------------------------------------------------------------
		}
		else
		{
			echo "<h1>".$xmlmodules->getElementsByTagName("admin_user")->item(0)->getAttribute("mod")."</h1>";

			//------------------------------------------------------------------------------------------------
                        // ACCOUNT UPDATE (and retrieve parameters)
                        //------------------------------------------------------------------------------------------------
			if (isset($_POST['update'])){
				update_user($user_id, stripAccents($user_name), $user_descr, $user_group, $user_password1, $user_password2, $user_type, $user_location, $user_mail, $user_limitation, $old_group_id, $old_name);	
				//message(8,"Update: User location = $user_location",'ok');	// For debug pupose, to be removed
				//message(8,"Update: User name =  $user_name",'ok');			// For debug pupose, to be removed
			}

			// Retrieve Group Information from database
			$user_name_descr = retrieve_user_info($user_id);
			$user_name=mysqli_result($user_name_descr,0,"user_name");
			$user_mail=mysqli_result(sqlrequest("$database_lilac","SELECT email FROM nagios_contact WHERE name='$user_name'"),0,"email");
			$user_descr=mysqli_result($user_name_descr,0,"user_descr");
			$user_group=mysqli_result($user_name_descr,0,"group_id");
			$user_type=mysqli_result($user_name_descr,0,"user_type");
			$user_limitation=mysqli_result($user_name_descr,0,"user_limitation");
			$user_location=mysqli_result($user_name_descr,0,"user_location");
			$user_password1="abcdefghijklmnopqrstuvwxyz";
			$user_password2="abcdefghijklmnopqrstuvwxyz";
			//message(8,"Mod: User location = $user_location",'ok');       // For debug pupose, to be removed
                        //message(8,"Mod: User name =  $user_name",'ok');                      // For debug pupose, to be removed

			//------------------------------------------------------------------------------------------------
		}

		?>

		<form action='./add_modify_user.php' method='POST' name='form_user'>
			<input type='hidden' name='user_id' value='<?php echo $user_id?>'>
				<center>
					<table class="table">
						<tr>
							<td><h2>User Name</h2></td>
							<td>
								<input type='textbox' name='user_name' value='<?php echo $user_name?>' style="width:300px;">
								<input type='hidden' name='user_name_old' value='<?php echo $user_name?>'>
							</td>
						</tr>
						<?php if($user_id!="1"){ ?>
						<tr>
							<td><h2>User Limited</h2></td>
							<td>
								<?php
									if($user_limitation=="1") $checked="checked='yes'";
									else $checked="";
									echo "<input type='checkbox' class='checkbox' name='user_limitation' value='1' $checked onclick='disable_group()'>";
								?>
							</td>
						</tr>
						<tr>
							<td><h2>Ldap User</h2></td>
							<td>
								<?php
									if($user_type=="1") $checked="checked='yes'";
									else $checked="";
									echo "<input type='checkbox' class='checkbox' name='user_type' value='1' $checked onclick='disable()'>";
								?>
							</td>
						</tr>
						<tr>
							<td><h2>Ldap Login</h2></td>
							<td>
								<?php
									echo "<input id='user_location' name='user_location' type='text' style='width:300px;' value='".htmlspecialchars($user_location, ENT_QUOTES)."'>";
								?>
								<script type="text/javascript">
								$(function() {
									$("#user_location").autocomplete("search.php?request=search_user");
									$("#user_location").result(function(event, data, formatted) {
										if (data)
											$(this).parent().find("input").val(data[1]);
									});
								});
								</script>
							</td>
						</tr>
						<?php 
						} 
						else {
							echo "<input type='hidden' name='user_type' value='0'>";
							echo "<input type='hidden' name='user_group' value='1'>";
						}
						?>
						<tr>
							<td><h2>User Mail</h2></td>
							<td>
								<input type='textbox' name='user_mail' value='<?php echo $user_mail?>' style="width:300px;">
							</td>
						</tr>

						<tr>
							<td><h2>User Description</h2></td>
							<td>
								<input type='textbox' name='user_descr' value='<?php echo $user_descr?>' style="width:300px;">
							</td>
						</tr>
						<tr>
							<td><h2>User Password</h2></td>
							<td>
								<input type='password' name='user_password1' value='<?php echo $user_password1?>' style="width:300px;">
							</td>
						</tr>
						<tr>
							<td><h2>User Password Confirmation</h2></td>
							<td>
								<input type='password' name='user_password2' value='<?php echo $user_password2?>' style="width:300px;">
							</td>
						</tr>
						<?php if($user_id!="1") { ?>
						<tr>
							<td><h2>User Group</h2></td>
							<td>
								<select name='user_group' size=1>
									<?php
										$result=sqlrequest("$database_eonweb","SELECT group_id,group_name from groups");
										while ($line = mysqli_fetch_array($result))
										{
											if ($user_group == $line[0])
												echo "<OPTION value='$line[0]' SELECTED>$line[1] </OPTION>";
											else
												echo "<OPTION value='$line[0]'>$line[1] </OPTION>";					
										}
									?>
								</select>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td class="blanc" align="center" colspan="2">
								<?php
									if ($user_id !=null)
										echo "<input class='button' type='submit' name='update' value='update'>";
									else
										echo "<input class='button' type='submit' name='add' value='add'>";
									echo "&nbsp;<input class='button' type='button' name='back' value='back' onclick='location.href=\"index.php\"'>";
								?>
							</td>
						</tr>
					</table>
				</center>
		</form>

		<?php
			if($user_limitation=="1" && $user_id!="1"){
				echo "<script>disable_group();</script>";
			}
			elseif($user_id!="1"){
				echo "<script>disable_group();</script>";
				echo "<script>disable_group();</script>";
			}
			if($user_type=="1" && $user_id!="1"){
				echo "<script>disable();</script>";
			}
			elseif($user_id!="1"){
				echo "<script>disable();</script>";
				echo "<script>disable();</script>";
			}
			//message(8,"Mod2: User location = $user_location",'ok');       // For debug pupose, to be removed
                        //message(8,"Mod2: User name =  $user_name",'ok');                      // For debug pupose, to be removed

		?>

	</body>
</html>
