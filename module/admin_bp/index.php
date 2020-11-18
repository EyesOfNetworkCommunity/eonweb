<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Michael Aubertin
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

		try {
			$db = new PDO('mysql:host='.$database_host.';dbname='.$database_nagios, $database_username, $database_password);
		} catch(Exception $e) {
			echo "Connection failed: " . $e->getMessage();
			exit('Unable to connect to database.');
		}

		$rule_type = "";
		$desc_bp = "";
		$min_value = "";
		$priority = "";

		$sql_type = "SELECT type, description, min_value , priority FROM bp WHERE name=?";
		$req = $db->prepare($sql_type);

		if(!$result_type = $req->execute(array($bp))){
			die('There was an error running the query');
		}

		while($row = $req->fetch()){   
			$rule_type = $row['type'];
			$desc_bp = $row['description'];
			$min_value = $row['min_value'];
			$priority = $row['priority'];
		} 

		if($min_value > 0) {
			$min_value = " ".$min_value;
		}

		$req = null;
		$db = null;
?>	
		<li>
			<div id="<?php echo $bp; ?>" class="tree-toggle">
				<div class="tree-line">
					<i class="glyphicon-link glyphicon"></i><?php echo getLabel("label.admin_bp.display") ?>:<?php echo $priority; ?>&nbsp;
					<b class="condition_presentation"><?php echo $rule_type.".".$min_value."</b>&nbsp;&nbsp;".$bp."&nbsp;&nbsp;(".$desc_bp.")"; ?>
				</div>
				<div class="list-inline marge-buttons">
					<button type="button" class="btn btn-xs btn-success" onclick="location.href='add_services.php?bp_name=<?php echo $bp; ?>&display=<?php echo $priority; ?>'"><i class="glyphicon glyphicon-plus"></i></button>
					<button type="button" class="btn btn-xs btn-info" onclick="editApplication('<?php echo $bp; ?>');"><i class="glyphicon glyphicon-pencil"></i></button>
					<button type="button" class="btn btn-xs btn-danger" onclick="ShowModalDeleteBP('<?php echo $bp; ?>');"><i class="glyphicon glyphicon-trash"></i></button>
				</div>
			</div>
		</li>				
<?php
	}

	function display_service($host_service,$bp_racine)
	{
		$service_name = explode(";", $host_service);
		$service_name = strtolower($service_name[1]);

?>
					<li class="end">
						<div id="<?php echo $bp_racine."::".$host_service; ?>" class="tree-toggle">
							<i class="nav-header glyphicon glyphicon-eye-open"></i>
							<?php echo $host_service."\n"; ?>
						</div>
					</li>	
<?php
	}

	function display_son($bp_racine)
	{

		global $database_nagios;
		global $database_host;
		global $database_username;
		global $database_password;
		try {
			$db = new PDO('mysql:host='.$database_host.';dbname='.$database_nagios, $database_username, $database_password);
		} catch(Exception $e) {
			echo "Connection failed: " . $e->getMessage();
			exit('Unable to connect to database.');
		}

		$t_bp_son = array();
		$t_service_son = array();

		$sql_bp = "SELECT bp_link FROM bp_links WHERE bp_name = ?";

		$sql_service = "SELECT host,service FROM bp_services WHERE bp_name = ? ORDER BY host,service";
		$req = $db->prepare($sql_bp);

		if(!$result_bp = $req->execute(array($bp_racine))){
			die('There was an error running the query');
		}

		while($row = $req->fetch()){   
			array_push($t_bp_son,$row['bp_link']);
		} 

		$req = null;
		$req = $db->prepare($sql_service);

		if(!$result_service = $req->execute(array($bp_racine))){
			die('There was an error running the query');
		}

		while($row = $req->fetch()){   
			array_push($t_service_son,$row['host'].";".$row['service']);
		}
		$req = null;
		$db = null;

		if(sizeof($t_bp_son) > 0 ) {
			for ($i = 0; $i < sizeof($t_bp_son); $i++) {
?>
					<li class="son">
						<ul class="nav nav-list tree">
<?php
							display_bp($t_bp_son[$i],$bp_racine);
							display_son($t_bp_son[$i]);
?>
						</ul>
					</li>
<?php
			}
		}
		if(sizeof($t_service_son) > 0 ) {
			for ($i = 0; $i < sizeof($t_service_son); $i++) {
				display_service($t_service_son[$i],$bp_racine);
			}
		}
	}

	$HTMLTREE ="";
	try {
		$db = new PDO('mysql:host='.$database_host.';dbname='.$database_nagios, $database_username, $database_password);
	} catch(Exception $e) {
		echo "Connection failed: " . $e->getMessage();
		exit('Unable to connect to database.');
	}

	$sql = "SELECT name FROM bp WHERE name NOT IN (SELECT bp_link FROM bp_links) ORDER BY priority, name";

	if(!$result = $db->query($sql)){
		die('There was an error running the query');
	}
	while($row = $result->fetch()){   
		array_push($t_bp_racine,$row['name']);
	} 

	$result = null;
	$db = null;

	?>
    
	<form class="form-inline">
		<div class="">
			<div class="form-group">
				<div class="btn-group">
					<button class="btn btn-info" type="button" onclick="ShowAll();"><?php echo getLabel("action.show_all") ?></button>
					<button class="btn btn-info" type="button" onclick="HideAll();"><?php echo getLabel("action.hide_all") ?></button>
				</div><!-- /btn-group -->
				
			</div>
			
			<div class="form-group">
				<div class="input-group">
					<input type="text" class="form-control" id="SearchFor" placeholder="<?php echo getLabel("action.search"); ?>...">
					<span class="input-group-btn">
						<button class="btn btn-info" id="FindIt" type="button"><?php echo getLabel("action.search"); ?></button>
					</span>
				</div><!-- /input-group -->
			</div>

			<div class="form-group">		                   
				<button type="button" class="btn btn-success" onclick="AddingApplication();">
					<?php echo getLabel("action.add_new_app"); ?>
				</button>
			</div>
			<div class="form-group">
				<button type="button" class="btn btn-primary" onclick="ShowModalApplyConfiguration();">
					<?php echo getLabel("action.apply_conf"); ?>
				</button>
			</div> 
		</div>

		<div id="body" class="pad-top">
<?php for ($i = 0; $i < sizeof($t_bp_racine); $i++) { ?>
			<div class="well well-sm">
				<ul class="nav nav-list tree">
			<?php
				display_bp($t_bp_racine[$i],$t_bp_racine[$i]);
				display_son($t_bp_racine[$i]);
			?>
				</ul>
			</div>
<?php } ?>
		</div>
	</form>

	<!-- modal for apply conf button -->
	<div id="popup_confirmation" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content panel-info">
				<div class="modal-header panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?php echo getLabel("action.delete"); ?></h4>
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
