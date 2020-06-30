<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.3
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
			<h1 class="page-header"><?php echo getLabel("label.admin_auth.title"); ?></h1>
		</div>
	</div>

<?php
	global $database_eonweb;
	global $database_host;
	global $database_username;
	global $database_password;
	global $ldap_search_begins;

	$action=retrieve_form_data("action",null);

	// Retrieve authentification backend settings
	$sqlresult=sqlrequest("$database_eonweb","select * from auth_settings;");
	$backend_selected=mysqli_result($sqlresult,0,"auth_type");
	
	if($backend_selected=="1"){
		$ldap_ip=mysqli_result($sqlresult,0,"ldap_ip");
		$ldap_port=mysqli_result($sqlresult,0,"ldap_port");
		$ldap_search=mysqli_result($sqlresult,0,"ldap_search");
		$ldap_user_filter=mysqli_result($sqlresult,0,"ldap_user_filter");
		$ldap_group_filter=mysqli_result($sqlresult,0,"ldap_group_filter");
		$ldap_user=mysqli_result($sqlresult,0,"ldap_user");
		$ldap_password=mysqli_result($sqlresult,0,"ldap_password");
		$ldap_rdn=mysqli_result($sqlresult,0,"ldap_rdn");
	} else {
		$ldap_password="";
	}

	// Submit authentification backend settings
	// If Update button pressed
	if($action == 'Update'){
		$backend_selected=retrieve_form_data("backend_selected",null);
		
		// If mysql selected, deletion of ldap_users list and zeros of auth_settings
		if($backend_selected=="mysql"){
			sqlrequest("$database_eonweb","delete from ldap_users_extended");
			$sqlresult=sqlrequest("$database_eonweb","update auth_settings set auth_type='0',ldap_ip=null,ldap_port=null,ldap_search=null,ldap_user_filter=null,ldap_group_filter=null,ldap_user=null,ldap_password=null,ldap_rdn=null");
		}
		// Else (LDAP selected)
		else{
			// Retrieve of data sent by GET method
			$ldap_ip=retrieve_form_data("ldap_ip",null);
			$ldap_port=retrieve_form_data("ldap_port",null);
			$ldap_search=retrieve_form_data("ldap_search",null);
			$ldap_user_filter=retrieve_form_data("ldap_user_filter",null);
			$ldap_group_filter=retrieve_form_data("ldap_group_filter",null);
			$ldap_user=retrieve_form_data("ldap_user",null);
			$ldap_rdn=retrieve_form_data("ldap_rdn",null);
			$ldap_password_new=retrieve_form_data("ldap_password",null);
			
			$sqlresult=sqlrequest("$database_eonweb","select * from auth_settings;");
			$backend_selected=mysqli_result($sqlresult,0,"auth_type");
			if($backend_selected=="1") { $ldap_password=mysqli_result($sqlresult,0,"ldap_password"); }

			if($ldap_password==$ldap_password_new) { $ldap_password=$ldap_password_new; }
			else { $ldap_password=base64_encode($ldap_password_new); }
			
			if($ldap_ip=="" || $ldap_port=="" || $ldap_search=="" || $ldap_rdn=="" || $ldap_user_filter=="" || $ldap_group_filter==""){
				message(7," : All fields are necessary","warning");
			}
			else {
				$sqlresult=sqlrequest("$database_eonweb","update auth_settings set auth_type='1',ldap_ip='$ldap_ip',ldap_port='$ldap_port',ldap_search='$ldap_search',ldap_user_filter='$ldap_user_filter',ldap_group_filter='$ldap_group_filter',ldap_user='$ldap_user',ldap_password='$ldap_password',ldap_rdn='$ldap_rdn'");
			}
		}
		// In any case
		// Retrieve authentification backend settings
		$sqlresult1=sqlrequest("$database_eonweb","select * from auth_settings;");
		$backend_selected=mysqli_result($sqlresult1,0,"auth_type");
		if($backend_selected=="0"){
			$ldap_ip="";
			$ldap_port="389";
			$ldap_search="";
			$ldap_user_filter="(objectclass=person)";
			$ldap_group_filter="(objectclass=group)";
			$ldap_user="";
			$ldap_password="";
			$ldap_rdn="";
		}
		elseif($backend_selected=="1"){
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
				message(6," : LDAP Connection Succeed","ok");
				// Set all ldap_users as unchecked
				sqlrequest("$database_eonweb","UPDATE ldap_users_extended SET checked=0");
				// Set all ldap_groups as unchecked
				sqlrequest("$database_eonweb","UPDATE ldap_groups_extended SET checked=0");

				// Filling the ldap_users 
				$connexion = mysqli_connect($database_host, $database_username, $database_password, $database_eonweb);
				if (!$connexion) {
					echo "<ul>";
					echo "<li class='msg_title'>Alert EyesOfNetwork - Message EON-database connect</li>";
					echo "<li class='msg'> Could not connect to database : $databasea_eonweb ($database_host)</li>";
					echo "</ul>";
					exit(1);
				}
			
				// Force UTF-8
				mysqli_query($connexion,"SET NAMES 'utf8'");

				// LDAP is case insensitive (RFC 2251)
				$total=0;
				foreach ($ldap_search_begins as $c){
					$ldap_current_filter="(&".$ldap_user_filter."(name=".$c."*))";

					$sr=ldap_search($ldapconn, $ldap_search, $ldap_current_filter, array("dn" ,"$ldap_rdn","name"));
					$info = ldap_get_entries($ldapconn, $sr);
                    
					if($info){
						$total=$total+$info["count"];
	
						for($i=0;$i<$info["count"];$i++){
							$dn=str_replace("\\,","\\\\,",$info[$i]["dn"]);
							$dn=str_replace("\\2C","\\\\\\\\,",$dn);
							$dn=str_replace('\'', '\\\'', $dn);
							$username=$info[$i]["name"][0];
							$resq=mysqli_query($connexion, "UPDATE ldap_users_extended SET dn='".$dn."', login='".$info[$i][$ldap_rdn]["0"]."', user='".$username."', checked=1 where dn='".$dn."'");
							
							if($resq[0]==0){
								mysqli_query($connexion, "INSERT INTO ldap_users_extended VALUES('".$dn."','".$info[$i][$ldap_rdn]["0"]."','".$username."',1)");
							}
						}
					}	
        	        else
                	   	message(6," : No LDAP entry found","info");
				}
				
				$total_groups=0;			
				foreach ($ldap_search_begins as $c){
				
					$ldap_current_filter="(&".$ldap_group_filter."(name=".$c."*))";

					$sr=ldap_search($ldapconn, $ldap_search, $ldap_current_filter, array("dn" ,"name"));
					
					$info = ldap_get_entries($ldapconn, $sr);
				
					if($info){
						$total_groups=$total_groups+$info["count"];

						for($i=0;$i<$info["count"];$i++){
							$dn=str_replace("\\,","\\\\,",$info[$i]["dn"]);
							$dn=str_replace("\\2C","\\\\\\\\,",$dn);
							$dn=str_replace('\'', '\\\'', $dn);
							$groupname=$info[$i]["name"][0];
							$resq=mysqli_query($connexion, "UPDATE ldap_groups_extended SET dn='".$dn."', group_name='".$groupname."', checked=1 where dn='".$dn."'");
							if($resq[0]==0){
								mysqli_query($connexion, "INSERT INTO ldap_groups_extended VALUES('".$dn."','".$groupname."',1)");
							}
						}
					}
				}
				message(6," : $total entrie(s) found","ok");
				message(6," : $total_groups group(s) found","ok");
				
				mysqli_query($connexion, "DELETE FROM ldap_users_extended WHERE checked=0");
				mysqli_query($connexion, "DELETE FROM ldap_groups_extended WHERE checked=0");
				mysqli_close($connexion);   		
			}			
			else
				message(0," : LDAP Connection Failed","warning");

		}
		if($sqlresult=="1")	
			message(6," : Authentification settings updated","ok");	
	}
?>

	<form action='./index.php' name='form_auth' method='POST' class='form'>
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo getLabel("label.admin_auth.auth_backend"); ?>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<label class="col-md-3">MySQL Backend</label>
						<div class="col-md-9">
							<input id="mysql" type="radio" value="mysql" name="backend_selected" <?php if($backend_selected=="0")echo "checked"; ?> onclick='disable()'>
						</div>
					</div>
				</div>
				<div>
					<div class="row">
						<label class="col-md-3">LDAP Backend</label>
						<div class="col-md-9">
							<input id="ldap" type="radio" value="ldap" name="backend_selected" <?php if($backend_selected=="1")echo "checked"; ?> onclick='disable()'>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo getLabel("label.admin_auth.ldap_settings"); ?>
			</div>
			<div class="panel-body">
				<div class="row">
					<label class="col-md-3"><?php echo getLabel("label.admin_auth.ldap_ip"); ?></label>
					<div class="col-md-9">
						<input type="text" name="ldap_ip" class="form-control"
						<?php if(isset($ldap_ip))echo 'value="'.$ldap_ip.'"';?>>
					</div>
				</div>
				<br>
				<div class="row" style="vertical-align: middle;">
					<label class="col-md-3"><?php echo getLabel("label.admin_auth.ldap_port"); ?></label>
					<div class="col-md-9">
						<input type="text" name="ldap_port" class="form-control"
						<?php
							if(isset($ldap_port))
								echo 'value="'.$ldap_port.'"';
							else
								echo 'value="389"'; 
						?>>
					</div>
				</div>
				<br>
				<div class="row">
					<label class="col-md-3"><?php echo getLabel("label.admin_auth.search_dn"); ?></label>
					<div class="col-md-9">
						<input type="text" name="ldap_search" class="form-control"
						<?php if(isset($ldap_search))echo 'value="'.$ldap_search.'"';?>>
					</div>
				</div>
				<br>
				<div class="row">
					<label class="col-md-3"><?php echo getLabel("label.admin_auth.search_user_filter"); ?></label>
					<div class="col-md-9">
					<input type="text" name="ldap_user_filter" class="form-control"
						<?php
							if(isset($ldap_user_filter))
								echo 'value="'.$ldap_user_filter.'"';
							else
								echo 'value="(objectclass=person)"';
						?>>
					</div>
				</div>
				<br>
				<div class="row">
					<label class="col-md-3"><?php echo getLabel("label.admin_auth.search_group_filter"); ?></label>
					<div class="col-md-9">
					<input type="text" name="ldap_group_filter" class="form-control"
						<?php
							if(isset($ldap_group_filter))
								echo 'value="'.$ldap_group_filter.'"';
							else
								echo 'value="(objectclass=group)"';
						?>>
					</div>
				</div>
				<br>
				<div class="row">
					<label class="col-md-3"><?php echo getLabel("label.admin_auth.proxy_user"); ?></label>
					<div class="col-md-9">
						<input type="text" name="ldap_user" class="form-control"
						<?php if(isset($ldap_user))echo 'value="'.str_replace("\\","\\\\",$ldap_user).'"';?>>
					</div>
				</div>
				<br>
				<div class="row">
					<label class="col-md-3"><?php echo getLabel("label.admin_auth.proxy_pwd"); ?></label>
					<div class="col-md-9">
						<input type="password" name="ldap_password" class="form-control"
						<?php if(isset($ldap_password))echo 'value="'.$ldap_password.'"';?>>
					</div>
				</div>
				<br>
				<div class="row">
					<label class="col-md-3"><?php echo getLabel("label.admin_auth.login_rdn"); ?></label>
					<div class="col-md-9">
						<input type="text" name="ldap_rdn" class="form-control"
						<?php if(isset($ldap_rdn))echo 'value="'.$ldap_rdn.'"';?>>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-3">
					<button class="btn btn-primary" type="submit" name="action" value="Update">
						<?php echo getLabel("action.update"); ?>
					</button>
					</div>
				</div>
			</div>
		</div>
	</form>

</div>
	
<?php include("../../footer.php"); ?>
