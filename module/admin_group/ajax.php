<?php
/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
# VERSION : 5.0
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

include('../../include/config.php');
include('../../include/arrays.php');
include('../../include/function.php');

// Retrieve authentification backend settings
$sqlresult1=sqlrequest("$database_eonweb","select * from auth_settings;");
$backend_selected=mysqli_result($sqlresult1,0,"auth_type");

if($backend_selected=="1"){

	$ldap_ip=mysqli_result($sqlresult1,0,"ldap_ip");
	$ldap_port=mysqli_result($sqlresult1,0,"ldap_port");
	$ldap_search=mysqli_result($sqlresult1,0,"ldap_search");
	$ldap_user_filter=mysqli_result($sqlresult1,0,"ldap_user_filter");
	$ldap_group_filter=mysqli_result($sqlresult1,0,"ldap_group_filter");
	$ldap_user=mysqli_result($sqlresult1,0,"ldap_user");
	$ldap_password=mysqli_result($sqlresult1,0,"ldap_password");
	$ldap_rdn=mysqli_result($sqlresult1,0,"ldap_rdn");

	// Connection au LDAP
	$ldapconn=ldap_connect($ldap_ip,$ldap_port);
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
	$ldapbind=ldap_bind($ldapconn, $ldap_user, base64_decode($ldap_password));
	
	if($ldapbind){
		if(isset($_POST['group_names'])){
			extract($_POST);
		} else {
			die;
		}
		
		$ldap_users = array();
		foreach ($group_names as $group_name) {
			$sql = "SELECT group_dn FROM groups WHERE group_name = '$group_name'";
			$result = sqlrequest("$database_eonweb", "$sql");
			$group_dn = mysqli_result($result,0,"group_dn");

			$mini_array = array();
			foreach ($ldap_search_begins as $c){
				$filter = "(&(objectCategory=user)(memberOf=$group_dn)(name=" . $c . "*))";
				
				$sr=ldap_search($ldapconn, $ldap_search, $filter, array("dn" ,"name", "samaccountname", "mail"));
				$info = ldap_get_entries($ldapconn, $sr);

				if($info){
					for($i=0;$i<$info["count"];$i++){
						array_push($mini_array, $info[$i]);
					}
				}
			}
			if( count($mini_array) > 0 ){
				$ldap_users["$group_name"] = $mini_array;
			}
		}	
	} else {
		message(0," : LDAP Connection Failed","warning"); die;
	}
} else {
	message(0," : Aucune configuration LDAP trouvée...","warning"); die;
}

?>



<form method="post">

	<div class="row">
		<div class="col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo getLabel("label.ldap_usr_list"); ?></div>
				<div class="panel-body">
					<div class="dataTable_wrapper">
						<table class="table table-striped table-condensed datatable-eonweb">
							<thead>
								<tr>
									<th><?php echo getLabel("label.user"); ?></th>
									<th>Email</th>
									<th><?php echo getLabel("label.group"); ?></th>
									<th class="col-md-3 text-center"><?php echo getLabel("label.admin_group.select"); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($ldap_users as $group_name => $ldap_user) {
									foreach ($ldap_user as $user) {
										echo "<tr>";
										echo '<input type="hidden" value="' . $user["dn"] . '">';
										echo "<td>" . $user['samaccountname'][0] . "</td>";
										echo "<td>"; if(isset($user['mail'])){echo $user['mail'][0];} echo "</td>";
										echo "<td>" . $group_name . "</td>";
										echo "<td class='text-center'>
												<input type='checkbox' name='user_import[]' value='".$user['samaccountname'][0]."'>
											  </td>";
										echo "</tr>";
									}	
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div><!-- !.col-md-8 -->
		

		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php echo getLabel("label.import_list"); ?>
					<button class="btn btn-primary btn-xs" type="submit" name="action" value="import"><?php echo getLabel("action.import"); ?></button>
				</div>
				<div class="panel-body">
					<table class="table table-condensed table-striped">
						<thead>
							<tr>
								<th><?php echo getLabel("label.user"); ?></th>
								<th><?php echo getLabel("label.group"); ?></th>
							</tr>
						</thead>
						<tbody id="import_list"></tbody>
					</table>
				</div>
			</div>
		</div><!-- !.col-md-4 -->
			
		
	</div><!-- !#row -->
	

</form>

<?php include('admin_group.php'); ?>
