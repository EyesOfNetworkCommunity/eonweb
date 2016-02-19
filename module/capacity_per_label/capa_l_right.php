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

if(count($_GET)>0) { 

	# --- Retrieve the selected graph id 
	if(isset($_GET['graph'])) $graphlocal_graph_id = $_GET['graph'];
	else message (0,"could not get host value","critical");

        if(isset($_GET['date'])) $graphlocal_dateid = $_GET['date'];
        else message(0,"Could not get date value","critical");


	global $database_cacti;
	# --- Get the graph id from the graph_template id
	$result_graph=  sqlrequest($database_cacti,"SELECT id FROM graph_local WHERE graph_template_id='$graphlocal_graph_id' ");
        $nbr_ligne_graph = mysqli_num_rows($result_graph);
	for ($i=0;$i<$nbr_ligne_graph;$i++)
	{
		# --- Print the graph
		$graph_id = mysqli_result($result_graph,$i,"id");
		echo "<img src='../../cacti/graph_image.php?local_graph_id=$graph_id&rra_id=$graphlocal_dateid' border='0'><br><br>";
	}

}	
?>
</body>
</html>
