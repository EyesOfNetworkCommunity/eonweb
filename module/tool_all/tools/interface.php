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

$command="";

if( $snmp_com != "" && $snmp_version != "" ){
	$snmp_community = $snmp_com;

	if ($snmp_version == "3") {
		if($snmp_context == "")
			$snmp_context = "";
		else
			$snmp_context = "-n $snmp_context";

		if($snmp_priv_protocol == "")
			$snmp_priv_protocol = "-l authNoPriv";
		else
			$snmp_priv_protocol = "-l authPriv -x $snmp_priv_protocol";

		if($snmp_priv_passphrase != "" && $snmp_priv_protocol != "-l authNoPriv")
			$snmp_priv_passphrase = "-X $snmp_priv_passphrase";
		else
			$snmp_priv_passphrase = "";

		$command="-a ".escapeshellarg($snmp_auth_protocol)." -u ".escapeshellarg($username)." -A ".escapeshellarg($password)." ".escapeshellarg($snmp_priv_protocol)." ".escapeshellarg($snmp_priv_passphrase)." ".escapeshellarg($snmp_context);
	}
}
else{
	message(4,"Could not get SNMP Community","critical");
	die;
}

// Snmp command
$snmpwalk = "snmpwalk -Oqv -c ".escapeshellarg($snmp_community)." -v ".escapeshellarg($snmp_version)." ".$command." ".escapeshellarg($host_name);

// Get host detail with snmp command
exec("$snmpwalk sysUpTime",$result_sysuptime);
exec("$snmpwalk sysName",$result_sysname);
exec("$snmpwalk sysLocation",$result_syslocation);
exec("$snmpwalk snmpOutTraps",$result_snmpouttraps);
exec("$snmpwalk snmpEnableAuthenTraps",$result_authentrap);
exec("$snmpwalk sysDescr",$result_sysdescr);

// Get interface detail with snmp command
exec("$snmpwalk ifIndex",$result_index);
exec("$snmpwalk ifDescr",$result_descr);
exec("$snmpwalk ifAlias",$result_alias);
exec("$snmpwalk ifSpeed",$result_speed);
exec("$snmpwalk ifOperStatus",$result_status);
exec("$snmpwalk ifAdminStatus",$result_admstatus);

// Display Host Information
echo '
	<div class="panel panel-default">
		<div class="panel-heading">Host : '.$host_name.'</div>
		<div class="panel-body">';
echo "<div class='table-responsive'>";
echo "<table class='table table-condensed table-bordered'>";
echo "<tr><th> ".getLabel("label.sys_name")." </th><td>";
	array_walk($result_sysname,'display_value');
echo "</td></tr>";

echo "<tr><th> ".getLabel("label.sys_loc")." </th><td>";
	array_walk($result_syslocation,'display_value');
echo "</td></tr>";

echo "<tr><th> ".getLabel("label.sys_uptime")." </th><td>";
	array_walk($result_sysuptime,'display_value');
echo "</td></tr>";

echo "<tr><th> Auth. traps </th><td>";
	array_walk($result_authentrap,'display_value');
echo "</td></tr>";

echo "<tr><th> ".getLabel("label.nb_trap_send")." </th><td>";
	array_walk($result_snmpouttraps,'display_value');
echo "</td></tr>";

echo "<tr><th> ".getLabel("label.sys_desc")." </th><td>";
	array_walk($result_sysdescr,'display_value');
echo "</td></tr></table>";
echo "</div>";

// Display interface info
echo "<div class='table-responsive'>";
echo "<table class='table table-striped table-condensed'>";
echo "<thead><tr>";
echo "<th> index </th>";
echo "<th> network interface </th>";
echo "<th> alias </th>";
echo "<th> speed </th>";
echo "<th> operstatus </th>";
echo "<th> adminstatus </th>";
echo "</tr></thead>";

$nbr_ifindex=count($result_index);
for($i=0;$i<$nbr_ifindex;$i++)
{
	if(!isset($result_alias[$i])) $result_alias[$i]=" ";
	switch ("$result_status[$i]") {
		case "up" :
			echo "<tr class='success'>";
			echo "<td>$result_index[$i]</td>";
			echo "<td>$result_descr[$i]</td>";
			echo "<td>$result_alias[$i]</td>";
			echo "<td>" . (($result_speed[$i]) / 1000000) . " Mo </td><td> up </td>";
			echo "<td>$result_admstatus[$i]</td></tr>";
			break;
		case "down" :
			echo "<tr class='danger'>";
			echo "<td>$result_index[$i]</td>";
			echo "<td>$result_descr[$i]</td>";
			echo "<td>$result_alias[$i]</td>";
			echo "<td>" . (($result_speed[$i]) / 1000000) . " Mo </td><td> down </td>";
			echo "<td>$result_admstatus[$i]</td></tr>";
			break;
		case "testing" :
			echo "<tr class='status_testing'>";
			echo "<td>$result_index[$i]</td>";
			echo "<td>$result_descr[$i]</td>";
			echo "<td>$result_alias[$i]</td>";
			echo "<td>" . (($result_speed[$i]) / 1000000) . " Mo </td><td> testing </td>";
			echo "<td>$result_admstatus[$i]</td></tr>";
			break;
		case "unknow" :
			echo "<tr class='status_unknow'>";
				echo "<td>$result_index[$i]</td>";
				echo "<td>$result_descr[$i]</td>";
				echo "<td>$result_alias[$i]</td>";
			echo "<td>" . (($result_speed[$i]) / 1000000) . " Mo </td><td> unknow </td>";
				echo "<td>$result_admstatus[$i]</td></tr>";
			break;
		case "dormant" :
			echo "<tr class='status_sleep'>";
				echo "<td>$result_index[$i]</td>";
				echo "<td>$result_descr[$i]</td>";
				echo "<td>$result_alias[$i]</td>";
			echo "<td>" . (($result_speed[$i]) / 1000000) . " Mo </td><td> dormant </td>";
				echo "<td>$result_admstatus[$i]</td></tr>";
			break;
		case "not present" :
			echo "<tr class='status_notpresent'>";
				echo "<td>$result_index[$i]</td>";
				echo "<td>$result_descr[$i]</td>";
				echo "<td>$result_alias[$i]</td>";
			echo "<td>" . (($result_speed[$i]) / 1000000) . " Mo </td><td> not present </td>";
				echo "<td>$result_admstatus[$i]</td></tr>";
			break;
		case "lower layer down" :
			echo "<tr class='status_lowlayerdown'>";
				echo "<td>$result_index[$i]</td>";
				echo "<td>$result_descr[$i]</td>";
				echo "<td>$result_alias[$i]</td>";
			echo "<td>" . (($result_speed[$i]) / 1000000) . " Mo </td><td> lower layer down </td>";
				echo "<td>$result_admstatus[$i]</td></tr>";
			break;
		default :
			echo "<tr class='status_notdefined'>";
				echo "<td>$result_index[$i]</td>";
				echo "<td>$result_descr[$i]</td>";
				echo "<td>$result_alias[$i]</td>";
			echo "<td>" . (($result_speed[$i]) / 1000000) . " Mo </td><td> unknow </td>";
				echo "<td>$result_admstatus[$i]</td></tr>";
	}
}
echo 			"</table>";
echo 		"</div>";
echo '
		</div>
	</div>';
?>
