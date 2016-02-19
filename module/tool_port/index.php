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

		// Get the value
		$host_name=$_GET['host_name'];
			
		$min_port=$_GET['min_port'];
		$max_port=$_GET['max_port'];
		
		// Test ports values
		if(!is_numeric($min_port)) message(4,"min_port must be numeric","critical");
		if(!is_numeric($max_port)) message(4,"max_port must be numeric","critical");
		if($min_port<1) message(4,"min_port must be >0","critical");
		if($max_port<2) message(4,"max_port must be >1","critical");
		if($min_port>$max_port) message(4,"min_port must be < max_port","critical");
		if($min_port==$max_port) message(4,"min_port must be different than max_port","critical");
		
		// Execute Netcat command
		echo "<h2>$host_name - Open ports</h2>";
		$cmd_netcat_ports="$path_netcat -w 1 -v -z $host_name $min_port-$max_port 2>&1 | grep succeeded! | cut -d ' ' -f 4";
		$cmd_netcat_services="$path_netcat -w 1 -v -z $host_name $min_port-$max_port 2>&1 | grep succeeded! | cut -d ' ' -f 6 | tr -s '[' ' ' | tr -s ']' ' '";
					
		exec("$cmd_netcat_ports",$result_cmd_netcat_ports);
		exec("$cmd_netcat_services",$result_cmd_netcat_services);
			
		// Count the Number of Ports
		$count_ports=count($result_cmd_netcat_ports);
		if($count_ports==0) message (0,"No port found in this range","critical");
		
		echo "<table class='table' width='400px'> <tr> <th width='100px'> Port </th> <th> Service </th> </tr>";
		for($i=0;$i<$count_ports;$i++) {
			echo "<tr class='status_up'>";
			echo "<td><center>$result_cmd_netcat_ports[$i]</center></td>";
			echo "<td><center>$result_cmd_netcat_services[$i]</center></td></tr>";
			}
		echo "</table>&nbsp;";
?>
</body>
</html>
