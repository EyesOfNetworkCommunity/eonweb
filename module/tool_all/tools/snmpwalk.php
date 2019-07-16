<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
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

// Test Hostname
if ($host_name == ""){
	message(4,"The host doesn't exist","critical");
	die;
}

if( $snmp_com != "" && $snmp_version != "" ) {
	$snmp_community=$snmp_com;
}
else{
	message(4,"Could not get SNMP Community","critical");
	die;
}

if(!isset($snmp_community) or $snmp_community == "") message(4,"Could not determine snmp community","critical");

// Get host detail with snmp command
if($snmp_version == "3"){
	$username = retrieve_form_data("username","");
	$password = retrieve_form_data("password","");
	$snmp_auth_protocol = retrieve_form_data("snmp_auth_protocol","");
	$snmp_priv_passphrase = retrieve_form_data("snmp_priv_passphrase","");
	$snmp_priv_protocol = retrieve_form_data("snmp_priv_protocol","");
	$snmp_context = retrieve_form_data("context","");


	if($username == "" || $password == "") message(4,"Could not determine username and password","critical");
	
	if($snmp_context == "")
		$snmp_context = "";
	else
		$snmp_context = "-n $snmp_context";
		if($snmp_priv_protocol == "")
			$snmp_priv_protocol = "-l authNoPriv";
	else
		$snmp_priv_protocol = "-l authPriv -x $snmp_priv_protocol";
		if($snmp_priv_passphrase != "" && $snmp_priv_protocol!="-l authNoPriv")
			$snmp_priv_passphrase = "-X $snmp_priv_passphrase";
	else
		$snmp_priv_passphrase = "";

	$command = "snmpwalk -c ".escapeshellarg($snmp_community)." -v ".escapeshellarg($snmp_version)." -a ".escapeshellarg($snmp_auth_protocol)." -u ".escapeshellarg($username)." -A ".escapeshellarg($password)." ".escapeshellarg($snmp_priv_protocol)." ".escapeshellarg($snmp_priv_passphrase)." ".escapeshellarg($snmp_context)." ".escapeshellarg($host_name);
}
else{
	$command = "snmpwalk -c ".escapeshellarg($snmp_community)." -v ".escapeshellarg($snmp_version)." ".escapeshellarg($host_name);
}

echo '
	<div class="panel panel-default">
		<div class="panel-heading">Host : '.$host_name.'</div>
		<div class="panel-body" style="overflow: auto; max-height: 800px;">';
echo 		"<p class='text-info fa fa-info-circle'> ".getLabel("label.exec_command")." : <b>".$command."</b></p><br>";
			$handle = popen($command,'r');
echo 		"<p>";
			while($read = fread($handle,100)){ 
				echo nl2br($read); 
				flush();
			} 
			pclose($handle);
echo 		"</p>";
echo '
		</div>
	</div>';
?>
