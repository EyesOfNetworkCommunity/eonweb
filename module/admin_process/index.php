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
<h1><?php echo $xmlmodules->getElementsByTagName("admin_process")->item(0)->getAttribute("title")?></h1>

<?php
        // Get the first array key
        reset($array_serv_system);
	$nbrproc=count($array_serv_system);
	if($nbrproc == '0') message(0,"Could not get process table information","critical");

	echo "<table class='table'>";
	echo "<tr><th>process</th><th>status</th><th>PID</th><th>actions</th></tr>";

	// Execute command if Need
	if(isset($_GET["getname"]) && isset($_GET["getact"]))
        {
                $getname=$_GET["getname"];
                $getact=$_GET["getact"];

                // EXEC the command
                $cmd_act=$array_serv_system[$getname]["proc_act"][$getact];

		// PB REGLE, > /dev/null !
		exec($cmd_act,$result_cmdact); 
	}

        // Display the list of process
        while (list($proc_name,$array_proc) = each($array_serv_system)) 
	{
                // Display process name
		echo "<tr><td>$proc_name</td>";
		
		// Display status	
		$cmd_status=$array_proc["status"];
		exec($cmd_status,$result_cmd);
		if ($result_cmd[0] == NULL) echo "<td class='status_down' > DOWN </td>";
		else echo "<td  class='status_up'> UP </td>";	

		//Display PID
		echo "<td>";
		array_walk($result_cmd,'display_value');
		echo "</td>";
	
		// Display actions		
		echo "<td>";
		$array_act=$array_proc["proc_act"];
		while (list($act_name,$act_cmd) = each($array_act))
		{
			if (($result_cmd[0] == NULL && $act_name != 'stop') || ($result_cmd[0] != NULL && $act_name != 'start' )) echo "<a href='?getname=$proc_name&getact=$act_name'>$act_name</a>&nbsp&nbsp";
		}
		echo "</td>";

		// Close
		echo "</tr>";
		$result_cmd=array();
	}
	echo "</table>";

	// Display command OUTPUT
	if(isset($_GET["getname"]) && isset($_GET["getact"]))
	{
		//Display the result
		echo "<br><h2> OUTPUT : </h2>";
		echo "<textarea cols='100' rows='15' name='result' scrolling='no' readonly>";
			array_walk($result_cmdact,'display_value');
		echo "</textarea>";
	}
?>
</body>
</html>

