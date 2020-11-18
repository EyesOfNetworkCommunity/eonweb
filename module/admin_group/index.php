<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
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
			<h1 class="page-header"><?php echo getLabel("label.admin_group.title"); ?></h1>
		</div>
	</div>

	<div id="errors"></div>

	<?php
	global $database_eonweb;
	global $database_lilac;
	$action=retrieve_form_data("action",null);
	$group_mgt_list=retrieve_form_data("group_mgt_list",null);
	$group_selected=retrieve_form_data("group_selected",null);
	
	if 	($action == 'submit')
	{
		switch($group_mgt_list)
		{
			case "add_group":
				echo "<meta http-equiv=refresh content='0;URL=add_modify_group.php'>";
				break;
			case "delete_group":
				if (isset($group_selected[0]))
				{
					for ($i = 0; $i < count($group_selected); $i++)
					{
						// Get group name
						$group_res = sql($database_eonweb,"select group_name from groups where group_id=?", array($group_selected[$i]));
						$group_name = $group_res[0]["group_name"];
						// Get users in group
						$users_in=sql($database_eonweb,"select user_name from users where group_id=?", array($group_selected[$i]));
						$users_in_names="";
						foreach($users_in as $line){
							$users_in_names=$line[0]." ".$users_in_names;
						}
						// Delete group if no users in
						if($users_in_names==""){
							// Delete in eonweb
							sql($database_eonweb,"delete from groupright where group_id=?", array($group_selected[$i]));

							sql($database_eonweb,"delete from groups where group_id=?", array($group_selected[$i]));

							// Delete in lilac
							require_once('/srv/eyesofnetwork/lilac/includes/config.inc');
							$ncg = NagiosContactGroupPeer::getByName($group_name);
							if($ncg){
								$ncg->delete();
							}

							logging("admin_group","DELETE : $group_selected[$i]");
							message(8," : Group $group_name removed",'ok');
						}
						else
							message(8," : Group $group_name contains users : $users_in_names",'warning');
					}
				}
				break;
		}
	}
	if( isset($_POST['action']) && $_POST['action'] == "import" ){
		if(!empty($_POST['import_list'])){
			// define if we import in nagvis or cacti (or both)
			if(isset($_POST['import_nagvis'])){ $in_nagvis = "yes"; }
			else { $in_nagvis = false; }
			if(isset($_POST['import_cacti'])){ $in_cacti = "yes"; }
			else { $in_cacti = false; }

			$errors_names = array();
			$nbr_ok = 0;
			foreach ($_POST['import_list'] as $key => $value) {
				$infos = explode("::", $value);
				$usrname = strtolower($infos[0]);
				$userdesc = $usrname;
				$usergroup = $infos[1];
				$user_password1 = "abcdefghijklmnopqrstuvwxyz";
				$user_password2 = "abcdefghijklmnopqrstuvwxyz";
				$usrtype = 1;
				$usrlocation = ldap_escape($infos[2]);
				$usrmail = $infos[3];
				$usrlimitation = 0;

				$sql = "SELECT group_id FROM groups WHERE group_name = ?";
				$query = sql($database_eonweb, $sql, array($usergroup));

				$usergroup = $query[0]["group_id"];

				$nagvis_role_id = "";
				// will insert in nagvis, only if checked
				if(isset($_POST["create_user_in_nagvis"])){
					$nagvis_role_id = $_POST["nagvis_group"];
					$in_nagvis = "yes";
				}

				// will insert in cacti, only if checked
				if(isset($_POST["create_user_in_cacti"])){
					$in_cacti = "yes";
				}

				$test = insert_user(stripAccents($usrname), $userdesc, $usergroup, $user_password1, $user_password2, $usrtype, $usrlocation,$usrmail,$usrlimitation, false, $in_nagvis, $in_cacti, $nagvis_role_id);
				
				if( is_null($test) ){
					array_push($errors_names, $usrname);
				} else {
					$nbr_ok++;
				}
			}
			message(8, " : " . $nbr_ok . " import(s) OK", "ok");
		} else {
			message(8, " : Aucun user LDAP dans la liste d'imports...", "warning");
		}
	}

	//Get the name group and description group
	$group_name_descr=sql($database_eonweb,"SELECT group_name,group_descr,group_id,group_type FROM groups ORDER BY group_name");
	
	// determine if there is LDAP conf
	$request = sql($database_eonweb, "SELECT auth_type FROM auth_settings");
	$conf_type=$request[0]["auth_type"];
	$ldap_conf = ($conf_type == "1") ? true : false;
	?>

	<form action="./index.php" method="GET" class="form-inline">
		<div class="dataTable_wrapper">
			<table class="table table-striped datatable-eonweb table-condensed">
				<thead>
				<tr>
					<th class="col-md-2 text-center"><?php echo getLabel("label.admin_group.select"); ?></th>
					<th><?php echo getLabel("label.admin_group.group_name"); ?></th>
					<th><?php echo getLabel("label.admin_group.group_type"); ?></th>
					<th><?php echo getLabel("label.admin_group.group_desc"); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach($group_name_descr as $line){
				$type = ($line[3] != "1") ? "MySQL" : "LDAP";
				?>
				<tr class="<?php echo $type; ?>">
					<td class="text-center">
						<?php
						if($line[2]=="1")
							echo "<input type='checkbox' name='group_selected[]' value='$line[2]' disabled>";
						else
							echo "<input type='checkbox' name='group_selected[]' value='$line[2]'>";
						?>
					</td>
					<td>
						<?php
						if($line[2]=="1")
							echo"$line[0]";
						else
							echo"<a href='./add_modify_group.php?group_id=$line[2]'>$line[0]</a>";
						?>
					</td>
					<td>
						<?php echo $type ?>
					</td>
					<td>
						<?php echo "$line[1]";?>
					</td>
				</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</div>
		
		<div class="form-group">
			<select class="form-control" name="group_mgt_list" size=1>
			<?php
			// Get the global table
			global $array_group_mgt;

			// Get the first array key
			reset($array_group_mgt);

			// Display the list of management choices
			$cpt = 1;
			while (list($mgt_name, $mgt_url) = each($array_group_mgt)) {
				if($cpt == 3){
					if($ldap_conf){
						echo "<option value='$mgt_url'>".getLabel($mgt_name)."</option>";
					}
				} else {
					echo "<option value='$mgt_url'>".getLabel($mgt_name)."</option>";
				}
				$cpt++;
			}
			?>
			</select>
		</div>
		<button id="mgt_group_submit" class="btn btn-primary" type="submit" name="action" value="submit"><?php echo getLabel("action.submit"); ?></button>
		<button class="btn btn-primary hidden" id="show_ldap_users"><?php echo getLabel("action.show_ldap_users"); ?></button>
	</form>

	<br>

	<div id="result"></div>

	<div id="loading-modal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" style="width: 66px;">
			<div class="modal-content">
				<img src="/images/loader.gif" alt="loading">
			</div>
		</div>
	</div>

</div>
	
<?php include("../../footer.php"); ?>
