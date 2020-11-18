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

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_user.title"); ?></h1>
		</div>
	</div>

	<?php
	global $database_eonweb;
	global $database_lilac;
	$action=retrieve_form_data("action",null);
	$user_mgt_list=retrieve_form_data("user_mgt_list",null);
	$user_selected=retrieve_form_data("user_selected",null);
	
	if($action == 'submit') 
	{
		switch($user_mgt_list)
		{
			case "add_user":
				echo "<meta http-equiv=refresh content='0;url=add_modify_user.php'>";
				break;
			case "delete_user":
				if (isset($user_selected[0]))
				{
					for ($i = 0; $i < count($user_selected); $i++)
 					{
						// Get user name
						$user_res = sql($database_eonweb,"select user_name from users where user_id=?", array($user_selected[$i]));
						$user_name = $user_res[0]["user_name"];

						// Delete user in eonweb
						sql($database_eonweb,"delete from users where user_id=?", array($user_selected[$i]));

						// Delete user in lilac
						$lilac_contactid = sql($database_lilac,"select id from nagios_contact where name=?", array($user_name));
						$lilac_contactid = $lilac_contactid[0]["id"];
						require_once('/srv/eyesofnetwork/lilac/includes/config.inc');
						$lilac->delete_contact($lilac_contactid);
						
						// Delete user files
						$user_files_path="$path_eonweb/$dir_imgcache/".strtolower($user_name);
						@unlink("$user_files_path-ged.xml");

						// delete user in nagvis
						$bdd = new PDO('sqlite:/srv/eyesofnetwork/nagvis/etc/auth.db');
						$req = $bdd->prepare("SELECT userId FROM users WHERE name = ?");
						$req->execute(array($user_name));
						$nagvis_user_exist = $req->fetch();
						if($nagvis_user_exist > 0){
							$userId = $nagvis_user_exist['userId'];
							$req = $bdd->prepare("DELETE FROM users2roles WHERE userId = ?");
							$req->execute(array($userId));
							$req = $bdd->prepare("DELETE FROM users WHERE userId = ?");
							$req->execute(array($userId));
						}

						// delete user in cacti
						$cacti_user_exist = sql($database_cacti, "SELECT id FROM user_auth WHERE username = ?", array($user_name));
						$cacti_user_exist = $cacti_user_exist[0];
						if ($cacti_user_exist["id"] > 0){
							$userId = $cacti_user_exist["id"];
							sql($database_cacti,"DELETE FROM user_auth WHERE id = ?", array($userId));
						}

						// Logging action
						logging("admin_user","DELETE : $user_selected[$i]");
						message(8," : User $user_name removed",'ok');
					}
				}
				break;
		}
	}
        
	// Get the name user and description group
	$user_name_descr = sql($database_eonweb, "SELECT user_name,user_descr,user_id,group_name,user_type,user_limitation FROM users LEFT OUTER JOIN groups ON groups.group_id = users.group_id ORDER BY user_name");
	?>

	<form action="./index.php" method="GET" class="form-inline">
		<div class="dataTable_wrapper">
			<table class="table table-striped datatable-eonweb table-condensed">
				<thead>
				<tr>
					<th class="col-md-2 text-center"><?php echo getLabel("label.admin_user.select"); ?></th>
					<th><?php echo getLabel("label.admin_user.user_name"); ?></th>
					<th><?php echo getLabel("label.admin_user.user_limit"); ?></th>
					<th><?php echo getLabel("label.admin_user.user_type"); ?></th>
					<th><?php echo getLabel("label.admin_user.user_mail"); ?></th>
					<th><?php echo getLabel("label.admin_user.user_desc"); ?></th>
					<th><?php echo getLabel("label.admin_user.user_group"); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach($user_name_descr as $line){
					$user_mail=sql($database_lilac,"SELECT email FROM nagios_contact WHERE name=?", array($line[0]));
					$user_mail = $user_mail[0]["email"];
				?>
				<tr>
					<td class="text-center">
						<?php
						if($line[2]=="1")
							echo "<input type='checkbox' name='user_selected[]' value='$line[2]' disabled>";
						else
							echo "<input type='checkbox' name='user_selected[]' value='$line[2]'>";
						?>
					</td>
					<td>
						<?php echo"<a href='./add_modify_user.php?user_id=$line[2]'> $line[0] </a>";?>
					</td>
					<td>
						<?php
						if($line[5]=="0")
								$type="NO";
						else
								$type="<a href='../module_filters/index.php?user_id=$line[2]&user_name=$line[0]'>YES</a>";
						echo "$type";
						?>
					</td>
					<td>
						<?php
						if($line[4]=="0")
							$type="MYSQL";
						else
							$type="LDAP";
						echo "$type";
						?>
					</td>
					<td>
						<?php echo "$user_mail";?>
					</td>
					<td>
						<?php echo "$line[1]";?>
					</td>
					<td>
						<?php echo "$line[3]";?>
					</td>
				</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</div>
		
		<div class="form-group">
			<select class="form-control" name="user_mgt_list" size=1>
			<?php	
			// Get the global table
			global $array_user_mgt;

			// Get the first array key
			reset($array_user_mgt);

			// Display the list of management choices
			while (list($mgt_name, $mgt_url) = each($array_user_mgt)) {
				echo "<option value='$mgt_url'>".getLabel($mgt_name)."</option>";
			}
			?>
			</select>
		</div>
		<button class="btn btn-primary" type="submit" name="action" value="submit"><?php echo getLabel("action.submit"); ?></button>
	</form>

</div>

<?php include("../../footer.php"); ?>
