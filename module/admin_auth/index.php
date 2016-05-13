<?php
/*
#########################################
#
# Copyright (C) 2014 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
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
        <?php include("../../include/include_module.php"); ?>
</head>

<body id='main'>

<script>
// Switch beetween MYSQL and LDAP display
function disable(){
        if(document.getElementById('ldap').checked){
                document.form_auth.ldap_ip.disabled=false;
                document.form_auth.ldap_port.disabled=false;
                document.form_auth.ldap_search.disabled=false;
                document.form_auth.ldap_user.disabled=false;
                document.form_auth.ldap_password.disabled=false;
                document.form_auth.ldap_rdn.disabled=false;
                document.form_auth.ldap_filter.disabled=false;
        }
        else{
                document.form_auth.ldap_ip.disabled=true;
                document.form_auth.ldap_port.disabled=true;
                document.form_auth.ldap_search.disabled=true;
                document.form_auth.ldap_user.disabled=true;
                document.form_auth.ldap_password.disabled=true;
                document.form_auth.ldap_rdn.disabled=true;
                document.form_auth.ldap_filter.disabled=true;
        }
}
</script>

<h1><?php echo $xmlmodules->getElementsByTagName("admin_auth")->item(0)->getAttribute("title")?></h1>

<?php

	global $database_eonweb;
	global $database_host;
	global $database_username;
	global $database_password;

	$action=retrieve_form_data("action",null);

	// Retrieve authentification backend settings
	$sqlresult=sqlrequest("$database_eonweb","select * from auth_settings;");
	$backend_selected=mysqli_result($sqlresult,0,"auth_type");
	if($backend_selected=="1"){
		$ldap_ip=mysqli_result($sqlresult,0,"ldap_ip");
                $ldap_port=mysqli_result($sqlresult,0,"ldap_port");
                $ldap_search=mysqli_result($sqlresult,0,"ldap_search");
                $ldap_filter=mysqli_result($sqlresult,0,"ldap_filter");
                $ldap_user=mysqli_result($sqlresult,0,"ldap_user");
                $ldap_password=mysqli_result($sqlresult,0,"ldap_password");
        	$ldap_rdn=mysqli_result($sqlresult,0,"ldap_rdn");
	}	

	// Submit authentification backend settings
	// If Update button pressed
        if($action == 'Update'){
		$backend_selected=retrieve_form_data("backend_selected",null);
		// If mysql selected, deletion of ldap_users list and zeros of auth_settings
		if($backend_selected=="mysql"){
			sqlrequest("$database_eonweb","delete from ldap_users_extend");
			$sqlresult=sqlrequest("$database_eonweb","update auth_settings set auth_type='0',ldap_ip=null,ldap_port=null,ldap_search=null,ldap_filter=null,ldap_user=null,ldap_password=null,ldap_rdn=null");
		}
		// Else (LDAP selected)
		else{
			// Retrieve of data sent by GET method
			$ldap_ip=retrieve_form_data("ldap_ip",null);
			$ldap_port=retrieve_form_data("ldap_port",null);
			$ldap_search=retrieve_form_data("ldap_search",null);
			$ldap_filter=retrieve_form_data("ldap_filter",null);
			$ldap_user=retrieve_form_data("ldap_user",null);
			$ldap_rdn=retrieve_form_data("ldap_rdn",null);

		        $sqlresult=sqlrequest("$database_eonweb","select * from auth_settings;");
		        $backend_selected=mysqli_result($sqlresult,0,"auth_type");
		        if($backend_selected=="1")
	                	$ldap_password=mysqli_result($sqlresult,0,"ldap_password");

			if($ldap_password==retrieve_form_data("ldap_password",null))
				$ldap_password=retrieve_form_data("ldap_password",null);
			else
				$ldap_password=base64_encode(retrieve_form_data("ldap_password",null));

			if($ldap_ip=="" || $ldap_port=="" || $ldap_search=="" || $ldap_rdn=="" || $ldap_filter=="")
				message(7," : All fields are necessary","warning");
			else
				$sqlresult=sqlrequest("$database_eonweb","update auth_settings set auth_type='1',ldap_ip='$ldap_ip',ldap_port='$ldap_port',ldap_search='$ldap_search',ldap_filter='$ldap_filter',ldap_user='$ldap_user',ldap_password='$ldap_password',ldap_rdn='$ldap_rdn'");
		}
		// In any case
		// Retrieve authentification backend settings
      		$sqlresult1=sqlrequest("$database_eonweb","select * from auth_settings;");
	        $backend_selected=mysqli_result($sqlresult1,0,"auth_type");
	        if($backend_selected=="0"){
			$ldap_ip="";
			$ldap_port="389";
			$ldap_search="";
			$ldap_filter="(objectclass=person)";
			$ldap_user="";
			$ldap_password="";
			$ldap_rdn="";
		}
	        elseif($backend_selected=="1"){
	                $ldap_ip=mysqli_result($sqlresult1,0,"ldap_ip");
	                $ldap_port=mysqli_result($sqlresult1,0,"ldap_port");
	                $ldap_search=mysqli_result($sqlresult1,0,"ldap_search");
	                $ldap_filter=mysqli_result($sqlresult1,0,"ldap_filter");
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
				foreach (array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','1','2','3','4','5','6','7','8','9','0') as $c){

					$ldap_current_filter=str_replace(")",")(name=".$c."*))",$ldap_filter);
					$ldap_current_filter="(&".$ldap_current_filter;

					//message(6,"Listing ".$c."* users ","ok");

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
							//$resq=mysqli_fetch_array($resq);
							if($resq[0]==0){
                                    mysqli_query($connexion, "INSERT INTO ldap_users_extended VALUES('".$dn."','".$info[$i][$ldap_rdn]["0"]."','".$username."',1)");
							        //message(6,"INSERTED: $dn","ok");
							}
						}
	                        	}	
        	                	else
                	                	message(6," : No LDAP entry found","info");
				}
				
				$total_groups=0;
				$sr=ldap_search($ldapconn, $ldap_search, "(objectclass=group)", array("dn" ,"name"));
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

<form action='./index.php' name='form_auth' method='GET'>
<center>
	<table class="table" width="500px">
         	<tr>
                       	<th>Authentification Backend</th>
                        <th>Choice</th>
		</tr>
		<tr>
			<td>MySQL Backend</td>
			<td align="center" valign="center"><input type='radio' id='mysql' class='checkbox' name='backend_selected' value='mysql' 
			<?php if($backend_selected=="0")echo "checked";?> onclick='disable()'>
			</td>
		</tr>
		<tr>
			<td>LDAP Backend</th>
			<td align="center" valign="center"><input type='radio' id='ldap' class='checkbox' name='backend_selected' value='ldap'
			<?php if($backend_selected=="1")echo "checked";?> onclick='disable()'>
			</td>
		</tr>
	</table>
	<table class="table" width="500px">
        	<tr>
                	<th colspan="2">LDAP Settings</td>
		</tr>
		<tr>	
			<td width="200px">LDAP server ip address</td>
			<td><input type="text" name="ldap_ip" style="width:300px;"
			<?php if(isset($ldap_ip))echo 'value="'.$ldap_ip.'"';?>>
			</td>
		</tr>
                <tr>    
                	<td width="200px">LDAP server port</td>
                        <td><input type="text" name="ldap_port" style="width:300px;"
			<?php
			if(isset($ldap_port))
				echo 'value="'.$ldap_port.'"';
			else
				echo 'value="389"'; 
			?>
			></td>
		</tr>
                <tr>    
                	<td width="200px">Search dn</td>
                        <td><input type="text" name="ldap_search" style="width:300px;"
			<?php if(isset($ldap_search))echo 'value="'.$ldap_search.'"';?>>
			</td>
                </tr>
		<tr>
                	<td width="200px">Search filter</td>
                        <td><input type="text" name="ldap_filter" style="width:300px;"
                        <?php
			if(isset($ldap_filter))
				echo 'value="'.$ldap_filter.'"';
			else
				echo 'value="(objectclass=person)"';
			?>>
                        </td>
		</tr>
                <tr>    
                	<td width="200px">Proxy user dn</td>
                        <td><input type="user" name="ldap_user" style="width:300px;"
			<?php if(isset($ldap_user))echo 'value="'.str_replace("\\","\\\\",$ldap_user).'"';?>>
			</td>
		</tr>
                <tr>    
                	<td width="200px">Proxy user password</td>
                        <td><input type="password" name="ldap_password" style="width:300px;"
			<?php if(isset($ldap_password))echo 'value="'.$ldap_password.'"';?>>
			</td>
		</tr>
                </tr>
		<tr>
                	<td width="200px">Login rdn</td>
                        <td><input type="text" name="ldap_rdn" style="width:300px;"
			<?php if(isset($ldap_rdn))echo 'value="'.$ldap_rdn.'"';?>>
			</td>
                </tr>
	</table>
	<input class='button' type='submit' name='action' value='Update'>
</center>
</form>

<?php
if($backend_selected=="0"){
        echo "<script>disable();</script>";
}
else{
        echo "<script>disable();</script>";
}
?>

</body>

</html>

