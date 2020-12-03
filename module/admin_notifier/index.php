<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Bastien PUJOS
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

// Init action
$action=null;
if(isset($_GET["action"])) {
	$action=$_GET["action"];
}
?>
<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_notifier.title"); ?></h1>
		</div>
	</div>
		
	<?php 
	/*
	*************** ACTIONS 
	*/
	if(isset($_POST["actions"])) {
		switch($_POST["actions"]) {
			case "move_rule":
				$rule_sql = sql($database_notifier, "SELECT sort_key,type FROM rules WHERE id=?", array($_POST["rule_id"]));
				$rule_sort_key = $rule_sql[0]["sort_key"];
				$rule_type = $rule_sql[0]["type"];
				if($_POST["move"] == "up") { 
					$new_pos=$rule_sort_key-1; 
				}
				elseif($_POST["move"] == "down") { 
					$new_pos=$rule_sort_key+1; 
				}
				sql($database_notifier, "UPDATE rules SET sort_key= ? WHERE type=? and id!=? and sort_key=?", array($rule_sort_key, $rule_type, $_POST["rule_id"], $new_pos));
				sql($database_notifier, "UPDATE rules SET sort_key=? WHERE type=? and id=?", array($new_pos, $rule_sql, $_POST["rule_id"]));
				break;
			case "del_rule_host":
				if(isset($_POST["rule_host_selected"])){
					$i=0;
					while(isset($_POST["rule_host_selected"][$i])){
 						sql($database_notifier,"DELETE FROM rule_method WHERE rule_id=?", array($_POST["rule_host_selected"][$i]));
						sql($database_notifier,"DELETE FROM rules WHERE id=?", array($_POST["rule_host_selected"][$i]));
						$i++;
					}
					message(6," : Selected host rules have been deleted",'ok');
				}
				else{
					message(8," : Please select host rule",'warning');
				}
				break;
			case "del_rule_service":
				if(isset($_POST["rule_service_selected"])){
					$i=0;
					while(isset($_POST["rule_service_selected"][$i])){
 						sql($database_notifier,"DELETE FROM rule_method WHERE rule_id=?", array($_POST["rule_service_selected"][$i]));
 						sql($database_notifier,"DELETE FROM rules WHERE type='service' and id=?", array($_POST["rule_service_selected"][$i]));
						$i++;
					}
					message(6," : Selected service rules have been deleted",'ok');
				}
				else{
					message(8," : Please select service rule",'warning');
				}
				break;
			case "del_timeperiod":
				if(isset($_POST["timeperiod_selected"])){
					$i=0;
					while(isset($_POST["timeperiod_selected"][$i])){
						sql($database_notifier,"DELETE FROM timeperiods WHERE id=?", array($_POST["timeperiod_selected"][$i]));
						$sql=sql($database_notifier,"SELECT count(name) FROM rules WHERE timeperiod_id=?", array($_POST["timeperiod_selected"][$i]));
						$select=$sql[0];
						if($select!=0){
							message(8," : One of the selected timeperiods couldn't be deleted because at least a rule is using it",'warning');
						}else{
							message(6," : One of the selected timeperiods have been deleted",'ok');
						}
						$i++;
					}
				}
				else{
					message(8," : Please select timeperiod",'warning');
				}
				$action="timeperiods";
				break;
			case "del_method":
				if(isset($_POST["method_selected"])){
					$i=0;
					while(isset($_POST["method_selected"][$i])){
						sql($database_notifier,"DELETE FROM methods WHERE id=?", array($_POST["method_selected"][$i]));
						$sql=sql($database_notifier,"SELECT count(rule_id) FROM rule_method WHERE method_id=?", array($_POST["method_selected"][$i]));
						$select=$sql[0];
						if($select!=0){
							message(8," : One of the selected method couldn't be deleted because at least a rule is using it",'warning');
						}else{
							message(6," : One of the selected method have been deleted",'ok');
						}
						$i++;
					}
				}
				else{
					message(8," : Please select method",'warning');
				}
				$action="methods";
				break;
			case "del_contact":
				if(isset($_POST["contact_selected"])){
					$i=0;
					while(isset($_POST["contact_selected"][$i])){
						sql($database_notifier,"DELETE FROM contacts WHERE name=?", array($_POST["contact_selected"][$i]));
						$sql=sql($database_notifier,"SELECT count(name) FROM contacts WHERE name=?", array($_POST["contact_selected"][$i]));
						$select= $sql[0];
						if($select!=0){
							message(8," : One of the selected method couldn't be deleted because at least a rule is using it",'warning');
						}else{
							message(6," : One of the selected method have been deleted",'ok');
						}
						$i++;
					}
				}
				else{
					message(8," : Please select method",'warning');
				}
				$action="contacts";
				break;
		}
		echo "<br>";
	}
		
	/*
	*************** RULES 
	*/
	if($action==null or $action =="rules") {
		// SQL get rules
		$rules_sql="SELECT rules.id,rules.name as name,debug,contact,host,service,state,notificationnumber,
			timeperiods.name as timeperiod, timeperiods.id as timeperiod_id, rules.tracking as tracking, GROUP_CONCAT(methods.name) as methods
			FROM rules,timeperiods,methods,rule_method
			WHERE rules.timeperiod_id=timeperiods.id
			AND rules.id = rule_method.rule_id
			AND methods.id = rule_method.method_id
			AND methods.type = rules.type";
	?>
	
	<!-- HOST RULES -->
	<div class="row">
		<div class="col-lg-12">
			<h2><?php echo getLabel("label.admin_notifier.host"); ?></h2>
		</div>
	</div>
	
	<form action="./index.php" method="POST">
		<div class="dataTable_wrapper">
			<div class="table-responsive">
				<table class="table table-striped datatable-eonweb table-condensed sort">
					<thead>
						<tr>
							<th class="text-center"> <?php echo getLabel("label.admin_group.select"); ?>  </th>
							<th> <?php echo getLabel("label.admin_notifier.name"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.debug"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.contacts"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.hosts"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.rules.service"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.rules.state"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.notification"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.notifperiod"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.methods.menu"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.rules.track"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.move"); ?>  </th>
						</tr>
					</thead>
					<tbody>
					<?php
					// Get host rules
					$rules_host=sql($database_notifier,$rules_sql." AND rules.type='host' GROUP BY name ORDER BY sort_key");
				
					foreach($rules_host as $line) {
					?>
						<tr>
							<td class="text-center"><label><input type="checkbox" class="checkbox" name="rule_host_selected[]" value="<?php echo $line["id"]; ?>"></label></td>
							<td><a href="rules.php?id=<?php echo $line["id"]; ?>"><?php echo $line["name"]; ?></a></td>
							<td><?php echo $line["debug"]; ?></td>
							<td><?php echo $line["contact"]; ?></td>
							<td><?php echo $line["host"]; ?></td>
							<td><?php echo $line["service"]; ?></td>
							<td><?php echo $line["state"]; ?></td>
							<td><?php echo $line["notificationnumber"]; ?></td>
							<td><a href="timeperiods.php?id=<?php echo $line["timeperiod_id"]; ?>"><?php echo $line["timeperiod"]; ?></a></td>
							<td><?php echo $line["methods"]; ?></td>
							<td><?php echo $line["tracking"]; ?></td>
							<td>
								<a class="up" href="javascript:void(0)"><i class="fa fa-arrow-up"></i></a>
								<a class="down" href="javascript:void(0)"><i class="fa fa-arrow-down"></i></a>
							</td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="form-group">
				<a href="./rules.php?type=host" class="btn btn-success" role="button"><?php echo getLabel("action.add");?></a>
				<button class="btn btn-danger" type="submit" name="actions" value="del_rule_host"><?php echo getLabel("action.clear");?></button>
			</div>
		</div>
	</form>
		
	<!-- SERVICE RULES -->	
	<div class="row">
		<div class="col-lg-12">
			<h2><?php echo getLabel("label.admin_notifier.service"); ?></h2>
		</div>
	</div>
	
	<form action="./index.php" method="POST">
		<div class="dataTable_wrapper">
			<div class="table-responsive">
				<table class="table table-striped datatable-eonweb table-condensed sort">
					<thead>
						<tr>
							<th class="text-center"> <?php echo getLabel("label.admin_group.select"); ?>  </th>
							<th> <?php echo getLabel("label.admin_notifier.name"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.debug"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.contacts"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.hosts"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.rules.service"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.rules.state"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.notification"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.notifperiod"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.methods.menu"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.rules.track"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.move"); ?>  </th>
						</tr>
					</thead>
					<tbody>
					<?php
					// Get service rules
					$rules_service=sql($database_notifier,$rules_sql." AND rules.type='service' GROUP BY name ORDER BY sort_key");
					
					foreach($rules_service as $line) {
					?>
						<tr>
							<td class="text-center"><label><input type="checkbox" class="checkbox" name="rule_service_selected[]" value="<?php echo $line["id"]; ?>"></label></td>
							<td><a href="rules.php?id=<?php echo $line["id"]; ?>"><?php echo $line["name"]; ?></a></td>
							<td><?php echo $line["debug"]; ?></td>
							<td><?php echo $line["contact"]; ?></td>
							<td><?php echo $line["host"]; ?></td>
							<td><?php echo $line["service"]; ?></td>
							<td><?php echo $line["state"]; ?></td>
							<td><?php echo $line["notificationnumber"]; ?></td>
							<td><a href="timeperiods.php?id=<?php echo $line["timeperiod_id"]; ?>"><?php echo $line["timeperiod"]; ?></a></td>
							<td><?php echo $line["methods"]; ?></td>
							<td><?php echo $line["tracking"]; ?></td>
							<td>
								<a class="up" href="javascript:void(0)"><i class="fa fa-arrow-up"></i></a>
								<a class="down" href="javascript:void(0)"><i class="fa fa-arrow-down"></i></a>
							</td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="form-group">
				<a href="./rules.php?type=service" class="btn btn-success" role="button"><?php echo getLabel("action.add");?></a>
				<button class="btn btn-danger" type="submit" name="actions" value="del_rule_service"><?php echo getLabel("action.clear");?></button>
			</div>
		</div>
	</form>
	
	<?php
	/*
	*************** METHODS 
	*/
	} elseif($action=="methods") {
	?>	
	<!-- HOST METHODS -->
	<div class="row">
		<div class="col-lg-12">
			<h2><?php echo getLabel("label.admin_notifier.methods.host"); ?></h2>
		</div>
	</div>
	
	<form action="./index.php" method="POST">
		<div class="dataTable_wrapper">
			<div class="table-responsive">
				<table class="table table-striped datatable-eonweb table-condensed">
					<thead>
						<tr>
							<th class="text-center"> <?php echo getLabel("label.admin_group.select"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.name"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.line"); ?> </th>
						</tr>
					</thead>
					<tbody>
					<?php
					// Get service rules
					$methods=sql($database_notifier,"select id,type,name,line from methods where type='host' order by name");
					
					foreach($methods as $line) {
					?>
						<tr>
							<td class="text-center"><label><input type="checkbox" class="checkbox" name="method_selected[]" value="<?php echo $line["id"]; ?>"></label></td>
							<td><a href="methods.php?id=<?php echo $line["id"]; ?>"><?php echo $line["name"]; ?></a></td>
							<td><?php echo $line["line"]; ?></td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="form-group">
				<a href="./methods.php?type=host" class="btn btn-success" role="button"><?php echo getLabel("action.add");?></a>
				<button class="btn btn-danger" type="submit" name="actions" value="del_method"><?php echo getLabel("action.clear");?></button>
			</div>
		</div>
	</form>
	
	<!-- SERVICE METHODS -->
	<div class="row">
		<div class="col-lg-12">
			<h2><?php echo getLabel("label.admin_notifier.methods.service"); ?></h2>
		</div>
	</div>
	
	<form action="./index.php" method="POST">
		<div class="dataTable_wrapper">
			<div class="table-responsive">
				<table class="table table-striped datatable-eonweb table-condensed">
					<thead>
						<tr>
							<th class="text-center"> <?php echo getLabel("label.admin_group.select"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.name"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.line"); ?> </th>
						</tr>
					</thead>
					<tbody>
					<?php
					// Get service rules
					$methods=sql($database_notifier,"select id,type,name,line from methods where type='service' order by name");

					foreach($methods as $line) {
					?>
						<tr>
							<td class="text-center"><label><input type="checkbox" class="checkbox" name="method_selected[]" value="<?php echo $line["id"]; ?>"></label></td>
							<td><a href="methods.php?id=<?php echo $line["id"]; ?>"><?php echo $line["name"]; ?></a></td>
							<td><?php echo $line["line"]; ?></td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="form-group">
				<a href="./methods.php?type=service" class="btn btn-success" role="button"><?php echo getLabel("action.add");?></a>
				<button class="btn btn-danger" type="submit" name="actions" value="del_method"><?php echo getLabel("action.clear");?></button>
			</div>
		</div>	
	</form>
	<?php
	/*
	*************** NOTIFPERIODS 
	*/
	} elseif($action=="timeperiods") {
	?>	
	<div class="row">
		<div class="col-lg-12">
			<h2><?php echo getLabel("label.admin_notifier.period"); ?></h2>
		</div>
	</div>
	
	<form action="./index.php" method="POST">
		<div class="dataTable_wrapper">
			<div class="table-responsive">
				<table class="table table-striped datatable-eonweb table-condensed">
					<thead>
						<tr>
							<th class="text-center"> <?php echo getLabel("label.admin_group.select"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.name"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.days"); ?> </th>
							<th> <?php echo getLabel("label.admin_notifier.hour"); ?> </th>
						</tr>
					</thead>
					<tbody>
					<?php
					// Get service rules
					$timeperiods=sql($database_notifier,"select id,name,daysofweek,timeperiod from timeperiods order by name");

					foreach($timeperiods as $line) {
					?>
						<tr>
							<td class="text-center"><label><input type="checkbox" class="checkbox" name="timeperiod_selected[]" value="<?php echo $line["id"]; ?>"></label></td>
							<td><a href="timeperiods.php?id=<?php echo $line["id"]; ?>"><?php echo $line["name"]; ?></a></td>
							<td><?php echo $line["daysofweek"]; ?></td>
							<td><?php echo $line["timeperiod"]; ?></td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="form-group">
				<a href="./timeperiods.php" class="btn btn-success" role="button"><?php echo getLabel("action.add");?></a>
				<button class="btn btn-danger" type="submit" name="actions" value="del_timeperiod"><?php echo getLabel("action.clear");?></button>
			</div>
		</div>	
	</form>
	<?php
	/*
	*************** CONTACTS
	*/
	}elseif($action=="contacts") {
	?>
		<div class="row">
			<div class="col-lg-12">
				<h2><?php echo getLabel("label.admin_notifier.contactdesact"); ?></h2>
			</div>
		</div>
	
		<form action="./index.php?action=contacts" method="POST">
			<div class="dataTable_wrapper">
				<div class="table-responsive">
					<table class="table table-striped datatable-eonweb table-condensed">
						<thead>
							<tr>
								<th class="text-center"> <?php echo getLabel("label.admin_group.select"); ?> </th>
								<th> <?php echo getLabel("label.admin_notifier.contact.name"); ?> </th>
								<th> <?php echo getLabel("label.admin_notifier.debug"); ?> </th>
							</tr>
						</thead>
						<tbody>
						<?php
						// Get service rules
						$contacts=sql($database_notifier,"select * from contacts order by name");

						foreach($contacts as $line) {
						?>
							<tr>
								<td class="text-center"><label><input type="checkbox" class="checkbox" name="contact_selected[]" value="<?php echo $line["name"]; ?>"></label></td>
								<td><a href="contact.php?name=<?php echo $line["name"]; ?>"><?php echo $line["name"]; ?></a></td>
								<td><?php echo $line["debug"]; ?></td>
							</tr>
						<?php
						}
						?>
						</tbody>
					</table>
				</div>
				<div class="form-group">
					<a href="./contact.php" class="btn btn-success" role="button"><?php echo getLabel("action.add");?></a>
					<button class="btn btn-danger" type="submit" name="actions" value="del_contact"><?php echo getLabel("action.clear");?></button>
				</div>
			</div>
		</form>
		<?php
	/*
	*************** EXPORT
	*/
	}elseif($action=="export") {
		exec("/usr/bin/php cli/export.php",$result_cmdact);
	?>
		<?php 
		if(count($result_cmdact) > 0) { 
			message(0," : ".implode("\n",$result_cmdact),"warning");
		} 
		?>
		
		<div class="row form-group">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<a style="text-decoration:none;" href="<?php echo "/module/admin_notifier/index.php?action=methods"; ?>">
							<i class="fa fa-envelope fa-fw"></i>
							<?php echo getLabel("label.admin_notifier.export.file")." ".$path_notifier_methods; ?>
						</a>
					</div>
					<div class="panel-body">
						<textarea class="form-control  textarea" rows="20" id="notifier1" name="maj" disabled><?php print file_get_contents($path_notifier_methods); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<a style="text-decoration:none;" href="<?php echo "/module/admin_notifier/index.php?action=rules"; ?>">
							<i class="fa fa-envelope fa-fw"></i>
							<?php echo getLabel("label.admin_notifier.export.file")." ".$path_notifier_rules; ?>
						</a>
					</div>
					<div class="panel-body">
						<textarea class="form-control  textarea" rows="20" id="notifier" name="maj" disabled><?php print file_get_contents($path_notifier_rules); ?></textarea>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	?>
</div>

<?php include("../../footer.php"); ?>
