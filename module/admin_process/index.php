<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
# VERSION : 5.2
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
			<h1 class="page-header"><?php echo getLabel("label.admin_process.title"); ?></h1>
		</div>
	</div>

	<?php
		// Get the first array key
		reset($array_serv_system);
		$nbrproc=count($array_serv_system);
		if($nbrproc == '0') message(0,"Could not get process table information","critical");

		echo "
			<div class='table-responsive'>
				<table class='table table-striped table-condensed'>";
		echo "<thead><tr><th>".getLabel("label.process")."</th><th>status</th><th>PID</th><th>actions</th></tr></thead>";

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
		
		echo "<tbody>";
		// Display the list of process
		while (list($proc_name,$array_proc) = each($array_serv_system))
		{
			$cmd_status=$array_proc["status"];
			exec($cmd_status,$result_cmd);

			if(!isset($result_cmd[0])) {
				$result_cmd[0]=NULL;
			}
			
			// Display process name
			if($result_cmd[0] == NULL){ $class = "danger"; }
			else{ $class = "success"; }
			echo "<tr><td>$proc_name</td>";
			
			// Display status
			if ($result_cmd[0] == NULL) echo "<td class='".$class."' >DOWN</td>";
			else echo "<td  class='".$class."'>UP</td>";	

			//Display PID
			echo "<td>";
			array_walk($result_cmd,'display_value');
			echo "</td>";
		
			// Display actions		
			echo "<td>";
			$array_act=$array_proc["proc_act"];
			while (list($act_name,$act_cmd) = each($array_act))
			{
				if ( ($result_cmd[0] == NULL && $act_name != 'stop') || ($result_cmd[0] != NULL && $act_name != 'start' ) )
				{
					if(isset($act_name) && $act_name == "stop"){ $class="btn btn-danger"; }
					elseif(isset($act_name) && ($act_name == "start" || $act_name == "restart") ){ $class="btn btn-success"; }
					else{ $class="btn btn-primary"; }
					echo "<a class='$class' href='index.php?getname=".urlencode($proc_name)."&getact=$act_name' role='button'>". getLabel("action.".$act_name) ."</a> ";
				}
			}
			echo "</td>";

			// Close
			echo "</tr>";
			$result_cmd=array();
		}
		echo "</tbody>";
		echo "</table> </div>";

		// Display command OUTPUT
		if(isset($_GET["getname"]) && isset($_GET["getact"]))
		{
			//Display the result
			echo "<div>";
			echo "	<textarea class='form-control textarea' rows='8' name='result' readonly>";
						array_walk($result_cmdact,'display_value');
			echo "	</textarea>";
			echo "</div>";
		}
	?>

</div>

<?php include("../../footer.php"); ?>
