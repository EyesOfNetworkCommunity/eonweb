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
<body id="main">
<?php
        // Test Hostname
        if (!isset($_GET['host_name'])) message(4,"The host doesn't exist","critical");
	$host_name=$_GET['host_name'];
	$command="";

	// display the header	
	echo "<h2> Host : $host_name </h2>";
	
	if( isset($_GET['snmp_com']) && isset($_GET['snmp_version']) ) {
		$snmp_community = $_GET['snmp_com'];
		$snmp_version =$_GET['snmp_version'];

		if ($snmp_version=="3") {
	                $username = retrieve_form_data("username","");
        	        $password = retrieve_form_data("password","");
                	$snmp_auth_protocol = retrieve_form_data("snmp_auth_protocol","");
	                $snmp_priv_passphrase = retrieve_form_data("snmp_priv_passphrase","");
	                $snmp_priv_protocol = retrieve_form_data("snmp_priv_protocol","");
	                $snmp_context = retrieve_form_data("context","");

	                if($snmp_context=="")
	                        $snmp_context="";
	                else
	                        $snmp_context="-n $snmp_context";

	                if($snmp_priv_protocol=="")
	                        $snmp_priv_protocol="-l authNoPriv";
	                else
	                        $snmp_priv_protocol="-l authPriv -x $snmp_priv_protocol";

	                if($snmp_priv_passphrase!="" && $snmp_priv_protocol!="-l authNoPriv")
                	        $snmp_priv_passphrase="-X $snmp_priv_passphrase";
        	        else
	                        $snmp_priv_passphrase="";

        	        $command="-a $snmp_auth_protocol -u $username -A $password $snmp_priv_protocol $snmp_priv_passphrase $snmp_context";
		}
	}
	
	// Get host detail with snmp command
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name sysUpTime",$result_sysuptime);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name sysName",$result_sysname);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name sysLocation",$result_syslocation);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name snmpOutTraps",$result_snmpouttraps);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name snmpEnableAuthenTraps",$result_authentrap);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name sysDescr",$result_sysdescr);

	// Get interface detail with snmp command
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name ifIndex",$result_index);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name ifDescr",$result_descr);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name ifAlias",$result_alias);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name ifSpeed",$result_speed);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name ifOperStatus",$result_status);
	exec("snmpwalk -Oqv -c $snmp_community -v $snmp_version $command $host_name ifAdminStatus",$result_admstatus);

	// Display Host Information
	echo "<table class='table'>";
	echo "<tr><th> System name </th><td>";
		array_walk($result_sysname,'display_value');
        echo "</td></tr>";

 	echo "<tr><th> System location </th><td>";
        	array_walk($result_syslocation,'display_value');
        echo "</td></tr>";

 	echo "<tr><th> System Uptime </th><td>";
	        array_walk($result_sysuptime,'display_value');
        echo "</td></tr>";

 	echo "<tr><th> Auth. traps </th><td>";
	        array_walk($result_authentrap,'display_value');
        echo "</td></tr>";

	echo "<tr><th> Nb of send trap </th><td>";
	        array_walk($result_snmpouttraps,'display_value');
        echo "</td></tr>";

        echo "<tr><th> System Description </th><td>";
	        array_walk($result_sysdescr,'display_value');
        echo "</td></tr></table>";


	// Display interface info
	echo "<table class='table'><tr>";
	echo "<th> index </th>";
	echo "<th> network interface </th>";
	echo "<th> alias </th>";
	echo "<th> speed </th>";
	echo "<th> operstatus </th>";
	echo "<th> adminstatus </th>";
	echo "</tr>";

	$nbr_ifindex=count($result_index);
	for($i=0;$i<$nbr_ifindex;$i++)
	{
		if(!isset($result_alias[$i])) $result_alias[$i]=" ";
		switch ("$result_status[$i]") {
			case "up" :
				echo "<tr class='status_up'>";
				echo "<td>$result_index[$i]</td>";
				echo "<td>$result_descr[$i]</td>";
				echo "<td>$result_alias[$i]</td>";
				echo "<td>" . (($result_speed[$i]) / 1000000) . " Mo </td><td> up </td>";
				echo "<td>$result_admstatus[$i]</td></tr>";
				break;
			case "down" :
				echo "<tr class='status_down'>";
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
	echo "</table>&nbsp;";
	
?>
</body>
</html>
