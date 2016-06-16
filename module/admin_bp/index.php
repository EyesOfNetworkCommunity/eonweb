<?php
/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Michael Aubertin
# VERSION : 5.0
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
			<h1 class="page-header"><?php echo getLabel("label.admin_bp.title"); ?></h1>
		</div>
	</div>

	<?php
	global $database_nagios;
	global $database_host;
	global $database_username;
	global $database_password;

	$t_bp_racine = array();

	function display_bp($bp,$bp_racine) {
		
		global $database_nagios;
		global $database_host;
		global $database_username;
		global $database_password;
		$db = new mysqli($database_host, $database_username, $database_password, $database_nagios);

		if($db->connect_errno > 0){
			die('Unable to connect to database [' . $db->connect_error . ']');
		}

		$rule_type = "";
		$desc_bp = "";
		$min_value = "";
		$priority = "";

		$sql_type = "
		SELECT type, description, min_value , priority
		FROM bp 
		WHERE name='".$bp."'
		";

		if(!$result_type = $db->query($sql_type)){
			die('There was an error running the query [' . $db->error . ']');
		}

		while($row = $result_type->fetch_assoc()){   
			$rule_type = $row['type'];
			$desc_bp = $row['description'];
			$min_value = $row['min_value'];
			$priority = $row['priority'];
		} 

		if($min_value > 0) {
			$min_value = " ".$min_value;
		}

		$result_type->free();
		mysqli_close($db);

		print "<li>";
		print "<div class=\"tree-toggle nav-header\" id=\"$desc_bp\">";
		print "<i class=\"glyphicon-link glyphicon\"></i>";
		print "Display:".$priority."&nbsp;&nbsp;<b class=\"condition_presentation\">".$rule_type.$min_value."</b> ".$bp."&nbsp;&nbsp;";
		//if($priority == 0){
		print "(".$desc_bp.")";
		print "<div class='float-right'>";
		print "<a href=\"add_services.php?bp_name=$bp&display=$priority\"><button type=\"button\" class=\"btn-group light-round btn-success marge-left\"><i class=\"glyphicon glyphicon-plus\"></i></button></a>";
		print "<button type=\"button\" class=\"btn-group light-round btn-info\" onclick=\"editApplication('$bp');\"><i class=\"glyphicon glyphicon-pencil\"></i></button>";
		print "<button type=\"button\" class=\"light-round btn-group btn-danger\" onclick=\"ShowModalDeleteBP('$bp');\"><i class=\"glyphicon glyphicon-trash\"></i></button>";
		print "</div>";
		print "</div>";
		//print "<a class=\"img-hover\" onclick=\"DeleteBP('$bp');\"><img src=\"./images/link_delete.png\" height=\"25\" width=\"25\"></a>&nbsp;\n";
		print "</li>";
	}

	function display_service($host_service,$bp_racine)
	{
		$service_name = split(";", $host_service);
		$service_name = strtolower($service_name[1]);

		print "\n";
		print "<div id=\"$bp_racine::$host_service\" class=\"tree-toggle\"><i class=\"nav-header glyphicon glyphicon-eye-open\"></i> ".$host_service;
		print "</div>";
		print "\n";
	}

	function display_son($bp_racine)
	{

		global $database_nagios;
		global $database_host;
		global $database_username;
		global $database_password;
		$db = new mysqli($database_host, $database_username, $database_password, $database_nagios);

		if($db->connect_errno > 0){
			die('Unable to connect to database [' . $db->connect_error . ']');
		}

		$t_bp_son = array();
		$t_service_son = array();

		$sql_bp = "
		SELECT bp_link 
		FROM bp_links 
		WHERE bp_name = '".$bp_racine."'
		";

		$sql_service = "
		SELECT host,service 
		FROM bp_services 
		WHERE bp_name = '".$bp_racine."'  ORDER BY id
		";

		if(!$result_bp = $db->query($sql_bp)){
			die('There was an error running the query [' . $db->error . ']');
		}

		while($row = $result_bp->fetch_assoc()){   
			array_push($t_bp_son,$row['bp_link']);
		} 

		$result_bp->free();

		if(!$result_service = $db->query($sql_service)){
			die('There was an error running the query [' . $db->error . ']');
		}

		while($row = $result_service->fetch_assoc()){   
			array_push($t_service_son,$row['host'].";".$row['service']);
		}
		$result_service->free();
		mysqli_close($db);

		if(sizeof($t_bp_son) > 0 ) {
			for ($i = 0; $i < sizeof($t_bp_son); $i++) {
				echo "<ul class=\"nav nav-list tree\">";
				echo "\n";
				display_bp($t_bp_son[$i],$bp_racine);
				display_son($t_bp_son[$i]);
				echo "</ul>";
				echo "\n";
			}
		}
		if(sizeof($t_service_son) > 0 ) {
			for ($i = 0; $i < sizeof($t_service_son); $i++) {
				echo "<li>";
				echo "<ul>";
				display_service($t_service_son[$i],$bp_racine);
				echo "</ul>";
				echo "</li>\n";
			}
		}
	}

	$HTMLTREE ="";
	$db = new mysqli($database_host, $database_username, $database_password, $database_nagios);

	if($db->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}

	$sql = "
	  SELECT name 
	  FROM bp  
	  WHERE name 
	  NOT IN (SELECT bp_link FROM bp_links) 
	  ORDER BY priority
	";

	if(!$result = $db->query($sql)){
		die('There was an error running the query [' . $db->error . ']');
	}
	while($row = $result->fetch_assoc()){   
		array_push($t_bp_racine,$row['name']);
	} 

	$result->free();
	mysqli_close($db);

	?>
    
	<form class="form-vertical">
		<div class="row">
			<div class="form-group col-md-4">
				<div class="input-group">
					<span class="input-group-btn">
						<button class="btn btn-info" type="button" onclick="ShowAll();">Show All</button>
						<button class="btn btn-info" type="button" onclick="HideAll();">Hide All</button>
					</span>
					<input type="text" class="form-control" id="SearchFor" placeholder="Search for...">
					<span class="input-group-btn">
						<button class="btn btn-info" id="FindIt" type="button">Find it!</button>
					</span>
				</div><!-- /input-group -->
			</div>

			<div class="form-group col-md-8">		                   
				<button type="button" class="btn btn-success" onclick="AddingApplication();">
					Add new application
				</button>
				<button type="button" class="btn btn-primary" onclick="ShowModalApplyConfiguration();">
					Apply Configuration
				</button>
			</div> 
		</div>

		<div id="body" class="pad-top">
		<?php 
			for ($i = 0; $i < sizeof($t_bp_racine); $i++) {
				echo "<div class=\"well well-sm\">";
					echo "<ul class=\"nav nav-list tree\">";
						display_bp($t_bp_racine[$i],$t_bp_racine[$i]);
						display_son($t_bp_racine[$i]);
					echo "</ul>";
				echo "</div>";
			}
		?>
		</div>
	</form>

	<!-- modal for apply conf button -->
	<div id="popup_confirmation" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content panel-info">
				<div class="modal-header panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Suppression</h4>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button id="modal-confirmation-apply-conf" type="button" class="btn btn-primary">
						<?php echo getLabel("label.yes"); ?>
					</button>
					<button id="modal-confirmation-del-bp" type="button" class="btn btn-primary">
						<?php echo getLabel("label.yes"); ?>
					</button>
					<button id="action-cancel" type="button" class="btn btn-default" data-dismiss="modal">
						<?php echo getLabel("label.no"); ?>
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

</div>

<?php include("../../footer.php"); ?>
