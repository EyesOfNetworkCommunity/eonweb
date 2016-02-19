<?php
/*
#########################################
#
# Copyright (C) 2014 EyesOfNetwork Team
# DEV NAME : Michael Aubertin
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

	// display the header	
	echo "<h2> Host : $host_name </h2><br>";
	
	if( isset($_GET['snmp_com']) && isset($_GET['snmp_version']) ) {
		$snmp_community=$_GET['snmp_com'];
		$snmp_version=$_GET['snmp_version'];
	}

	if(!isset($snmp_community) or $snmp_community=="") message(4,"Could not determine snmp community","critical");

	// Get host detail with snmp command
	if($snmp_version == "3"){
                $username = retrieve_form_data("username","");
                $password = retrieve_form_data("password","");
                $snmp_auth_protocol = retrieve_form_data("snmp_auth_protocol","");
                $snmp_priv_passphrase = retrieve_form_data("snmp_priv_passphrase","");
                $snmp_priv_protocol = retrieve_form_data("snmp_priv_protocol","");
                $snmp_context = retrieve_form_data("context","");


		if($username=="" || $password=="") message(4,"Could not determine username and password","critical");
		
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

		$command="snmpwalk -c $snmp_community -v $snmp_version -a $snmp_auth_protocol -u $username -A $password $snmp_priv_protocol $snmp_priv_passphrase $snmp_context $host_name";
	}
	else{
		$command="snmpwalk -c $snmp_community -v $snmp_version $host_name";
	}

	echo "<br><b>".$command."</b><br><br>";
	$handle = popen($command,'r');
    	while($read = fread($handle,100)){ 
	        echo nl2br($read); 
		flush();
        } 
	pclose($handle);
	echo "<br><br>" 
?>
</body>
</html>
