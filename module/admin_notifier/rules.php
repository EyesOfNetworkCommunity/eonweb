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

function get_field($field1, $base=false, $field2=false) {
	global $database_lilac;
	global $database_notifier;
	$hosts=array();
	
	if($field2){
		$request="SELECT name FROM nagios_$field1 UNION SELECT name FROM nagios_$field2";
		$result=sql($database_lilac,$request);
	}
	else{
		$request="SELECT name FROM $field1";
		$result=sql($database_notifier,$request);
	}
	
	if($field1=="service"){
		$request="SELECT description FROM nagios_$field1 UNION SELECT name FROM nagios_$field2";
		$result=sql($database_lilac,$request);
	}
	
	if($base=="host"){
		$request="SELECT name FROM $field1 WHERE type='host'";
		$result=sql($database_notifier,$request);
	}
	elseif($base=="service"){
		$request="SELECT name FROM $field1 WHERE type='service'";
		$result=sql($database_notifier,$request);
	}
	
	foreach($result as $line) {
		$hosts[]=$line[0];
	}

	$hosts= array_unique($hosts);
	echo json_encode($hosts);
}
?>

<div id="page-wrapper">
	
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_notifier.rules.add"); ?></h1>
		</div>
	</div>
	
	<?php
	// States
	$rule_state1="-";
	if(!empty($_POST['rule_check'])){
		$i=0;
		foreach($_POST['rule_check'] as $selected){
			if($i==0){
				$rule_state1=$selected;
				$i++;
			}
			else{
				$rule_state1.=",".$selected;
				$i++;
			}
		}
		if(($_POST['type']=="host")&&($i==3)) {
			$rule_state1="*";
		}elseif(($_POST['type']=="service")&&($i==4)) {
			$rule_state1="*";
		}
	}
	
	$rule_track=0;
	if(isset($_POST['rule_track'])){
		$rule_track=$_POST['rule_track'];
	}
	
	// Hosts
	$rule_host="-";
	if(!empty($_POST['host'])){
		$rule_host="";
		foreach($_POST['host'] as $selected){
			$rule_host.=$selected.",";
		}
		$rule_host=rtrim($rule_host,",");
	}
	
	// Methods
	$rule_method_ids=null;
	if(!empty($_POST['methods'])){
		$rule_method_names = "";
		foreach($_POST['methods'] as $selected){
			$sql=sql($database_notifier,"SELECT id,name FROM methods WHERE name=? AND type=?", array($selected, $_POST['type']));
			if($sql[0]!=FALSE) {
				$select=$sql[0];
				$rule_method_ids.=$select.",";
				$select=$sql[1];
				$rule_method_names.=$select.",";
			}
		}
		$rule_method_ids=rtrim($rule_method_ids,",");
		$rule_method_names=rtrim($rule_method_names,",");
	}
	
	// Contacts
	$rule_contact="-";
	if(!empty($_POST['contacts'])){
		$rule_contact="";
		foreach($_POST['contacts'] as $selected){
			$rule_contact.=$selected.",";
		}
		$rule_contact=rtrim($rule_contact,",");
	}
	
	// Services
	$rule_service="-";
	if(!empty($_POST['services'])){
		$rule_service="";
		foreach($_POST['services'] as $selected){
			$rule_service.=$selected.",";
		}
		$rule_service=rtrim($rule_service,",");
	}

	// Get post data
	$rule_id=retrieve_form_data("id",null);
	$rule_name=retrieve_form_data("rule_name",null); 
	$rule_name_old=retrieve_form_data("rule_name_old",null); 
	$rule_type=retrieve_form_data("type",null);
	if(is_null($rule_type) or ($rule_type!="host" and $rule_type!="service")) { $rule_type="host"; }
	$rule_debug=retrieve_form_data("rule_debug",null);
	if ( $rule_debug == '1') {
		$rule_debug = 1;
	} else {
		$rule_debug = 0;
	}
	if(isset($rule_contact)){
		$rule_contact = (is_null($rule_contact)) ? "-" : $rule_contact;
	}
	if(isset($rule_host)){
		$rule_host = (is_null($rule_host)) ? "-" : $rule_host;
	}
	if(isset($rule_service)){
		$rule_service = (is_null($rule_service)) ? "-" : $rule_service;
	}
	if(isset($rule_state1)){
		$rule_state=$rule_state1;
		$rule_state = (is_null($rule_state)) ? "*" : $rule_state;
	}
	$rule_notification=retrieve_form_data("rule_notification",null);
	$rule_timeperiod=retrieve_form_data("rule_timeperiod",null);
		
	// ADD or UPDATE
	if(isset($_POST["add"]) || isset($_POST["update"])) {
	
		if($rule_timeperiod && $rule_timeperiod!=""){
			$sql=sql($database_notifier,"SELECT count(id) as count,id from timeperiods WHERE name=?", array($rule_timeperiod));
			if($sql[0]["count"]!=0) {
				$rule_timeperiod_id=$sql[0]["id"];
			}
		}
	
		if($rule_name){
			$sql_test = "SELECT count(name) FROM rules WHERE name=?";
			$rule_exist = sql($database_notifier,$sql_test, array($rule_name));
			$ajout=$rule_exist[0]; 
		}
		
		if(!$rule_notification || $rule_notification=="") {
			$rule_notification="-";
		}
		
		if(!$rule_name || $rule_name==""){
			message(7," : Your rule need a name",'warning');
		}
		elseif(!$rule_method_ids || $rule_method_ids==""){
			message(7," : Your rule need at least one method",'warning');
		}
		elseif(!$rule_timeperiod || $rule_timeperiod==""){
			message(7," : Your rule need a timeperiod",'warning');
		}
		elseif(!isset($rule_timeperiod_id)){
			message(7," : Your timeperiod does not exist",'warning');
		}
		elseif((isset($_POST["add"]) && $ajout!=0) || (isset($_POST["update"]) && $ajout!=0 && $rule_name != $rule_name_old)) {
			message(7," : This rule name already exist",'warning');
		}elseif(isset($_POST["add"])){
			$sql_sort_key = sql($database_notifier,"select max(sort_key+1) as sort_key from rules where type=?", array($rule_type));
			$rule_sort_key = $sql_sort_key[0]["sort_key"];
			$sql_add = "INSERT INTO rules VALUES('',?,?,?,?,?,?,?,?,?,?,?)";
			$rule_id = sql($database_notifier,$sql_add, array($rule_name, $rule_type, $rule_debug, $rule_contact, $rule_host, $rule_service, $rule_state, $rule_notification, $rule_timeperiod_id, $rule_sort_key, $rule_track));
			$methodze=explode(",",$rule_method_ids);
			foreach($methodze as $selected){
				sql($database_notifier,"INSERT INTO rule_method VALUES(?, ?)", array($rule_id, $selected));
			}
			message(6," : Rule have been added",'ok');
			$rule_name_old=$rule_name;
		}elseif(isset($_POST["update"])){
			$sql_add = "UPDATE rules SET name=?, type=?, debug=?, contact=?,
			host=?, service=?, state=?, notificationnumber=?,timeperiod_id=?, tracking=?
			WHERE id=?";
			sql($database_notifier,$sql_add, array($rule_name, $rule_type, $rule_debug, $rule_contact, $rule_host, $rule_service, $rule_state, $rule_notification, $rule_timeperiod_id, $rule_track));
			sql($database_notifier,"DELETE FROM rule_method WHERE rule_id=?", array($rule_id));
			$methodze=explode(",",$rule_method_ids);
			foreach($methodze as $selected){
				sql($database_notifier,"INSERT INTO rule_method VALUES(?, ?)", array($rule_id, $selected));
			}
			message(6," : Rule have been updated",'ok');
			$rule_name_old=$rule_name;
		}
	
	}
	
	// Display existing values
	if(isset($_GET["id"])) { $rule_id=$_GET["id"]; }
	if($rule_id) {
		if(is_numeric($rule_id)) {
			$rule_sql=sql($database_notifier,"SELECT rules.id,rules.name as name,rules.type as type,debug,
			contact,host,service,state,notificationnumber,timeperiods.name as timeperiod,timeperiod_id, rules.tracking as tracking,
			GROUP_CONCAT(methods.name) as methods
			FROM rules,timeperiods,methods,rule_method
			WHERE rules.timeperiod_id=timeperiods.id
			AND rules.id = rule_method.rule_id
			AND methods.id = rule_method.method_id
			AND methods.type = rules.type
			AND rules.id=?", array($rule_id));
			
			if($rule_sql[0]["id"]) {
				$rule_id=$rule_sql[0]["id"];
				$rule_name=$rule_sql[0]["name"];
				$rule_name_old=$rule_sql[0]["name"];
				$rule_type=$rule_sql[0]["type"];
				$rule_debug=$rule_sql[0]["debug"];
				$rule_contact=$rule_sql[0]["contact"];
				$rule_host=$rule_sql[0]["host"];
				$rule_service=$rule_sql[0]["service"];
				$rule_state=$rule_sql[0]["state"];
				$rule_notification=$rule_sql[0]["notificationnumber"];
				$rule_timeperiod=$rule_sql[0]["timeperiod"];
				$rule_timeperiod_id=$rule_sql[0]["timeperiod_id"];
				$rule_method_names=$rule_sql[0]["methods"];
				$rule_track=$rule_sql[0]["tracking"];
			} else {
				message(7," : Rule does not exist",'warning');
			}
		} else {
			$rule_id=null;
		}
	}
	
	$timeperiod=sql($database_notifier,"SELECT id from timeperiods");
	$methodhost=sql($database_notifier,"select id from methods where type='host'");
	$methodService=sql($database_notifier,"select id from methods where type='service'");	
	?>
		
	<form id="form-rules" action="./rules.php" method="POST" name="form">
		<input type="hidden" name="id" value="<?php echo $rule_id; ?>">
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.name") ?></label>
			<div class="col-md-9">
				<input class="form-control" type="text" name="rule_name" value="<?php echo $rule_name; ?>" autofocus>
				<input type="hidden" name="rule_name_old" value="<?php echo $rule_name_old; ?>">
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.type") ?></label>
			<div class="col-md-9">
				<input class="form-control" type="text" name="type" value="<?php echo $rule_type; ?>" readonly>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.debug") ?></label>
			<div class="col-md-9">
				<input class="checkbox" type="checkbox" name="rule_debug" value="1" 
				<?php 
				if (isset($rule_debug)) {
					if ($rule_debug > 0) {
						echo " checked";
					}
				}
				?>
				>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.contact") ?></label>
			<div class="col-md-9">
				<div class="form-group input-group">
					<input class="form-control" id="rule_contact1" type="text" name="rule_contact" onFocus='$(this).autocomplete({source: <?php get_field("contact",false,"contact_group"); ?>})'>
					<span class="input-group-btn">
						<input class="btn btn-success" id="rule_contact_button" type="button" value="<?php echo getLabel("action.add");?>">
						<input class="btn btn-danger" id="rule_contact_button_del" type="button" value="<?php echo getLabel("action.clear");?>">
					</span>
				</div>
				<select class="form-control" id="contacts" name="contacts[]" size=4 multiple="multiple">
				<?php 
					if(isset($rule_contact)){
						if($rule_contact!="-") {
							$division=explode(",", $rule_contact);
							foreach($division as $selected){
								echo "<option selected='selected' value='".$selected."'>".$selected."</option> ";
							}
						} 
					}
				?>
				</select>
				<a href="#" id="contact_all">All / None</a>
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.host") ?></label>
			<div class="col-md-9">
				<div class="form-group input-group">
					<input class="form-control" id="rule_host1" type="text" name="rule_host" onFocus='$(this).autocomplete({source: <?php echo get_field("host",false,"hostgroup");?>})'>
					<span class="input-group-btn">
						<input class="btn btn-success" id="rule_host_button" type="button" value="<?php echo getLabel("action.add");?>">
						<input class="btn btn-danger" id="rule_host_button_del" type="button" value="<?php echo getLabel("action.clear");?>">
					</span>
				</div>
				<select class="form-control" id="host" name="host[]" size=4 multiple="multiple">
				<?php 
					if(isset($rule_host)){
						if($rule_host!="-") {
							$division=explode(",", $rule_host);
							foreach($division as $selected){
								echo "<option selected='selected' value='".$selected."'>".$selected."</option> ";
							}
						}
					} 
				?>
				</select>
				<a href="#" id="host_all">All / None</a>
			</div>
		</div>
		
		<?php if($rule_type=="service") { ?>
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.service") ?></label>
			<div class="col-md-9">
				<div class="form-group input-group">
					<input class="form-control" id="rule_service2" type="text" name="rule_service1" onFocus='$(this).autocomplete({source: <?php echo get_field("service",false,"service_group");?>})'>
					<span class="input-group-btn">
						<input class="btn btn-success" id="rule_service_button" type="button" value="<?php echo getLabel("action.add");?>">
						<input class="btn btn-danger" id="rule_service_button_del" type="button" value="<?php echo getLabel("action.clear");?>">
					</span>
				</div>
				<select class="form-control" id="services" name="services[]" size=4 multiple="multiple">
				<?php 
					if(isset($rule_service)){
						if($rule_service!="-") {
							$division=explode(",", $rule_service);
							foreach($division as $selected){
								echo "<option selected='selected' value='".$selected."'>".$selected."</option> ";
							}
						}
					} 
				?> 
				</select>
				<a href="#" id="service_all">All / None</a>
			</div>
		</div>
		<?php } ?>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.state") ?></label>
			<div class="col-md-9">
			<?php if($rule_type=="host") { ?>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(isset($rule_state)){if(strstr($rule_state, "UP")!=false || strstr($rule_state, "*")){echo "checked";}} ?> value="UP" name="rule_check[]">UP</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(isset($rule_state)){if(strstr($rule_state, "DOWN")!=false || strstr($rule_state, "*")){echo "checked";}} ?> value="DOWN" name="rule_check[]">DOWN</label>
				</div>				
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(isset($rule_state)){if(strstr($rule_state, "UNREACHABLE")!=false || strstr($rule_state, "*")){echo "checked";}}?> value="UNREACHABLE" name="rule_check[]">UNREACHABLE</label>
				</div>
			<?php } elseif($rule_type=="service") { ?>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(isset($rule_state)){if(strstr($rule_state, "OK") || strstr($rule_state, "*")){echo "checked";}}?> value="OK" name="rule_check[]">OK</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(isset($rule_state)){if(strstr($rule_state, "WARNING") || strstr($rule_state, "*")){echo "checked";}}?> value="WARNING" name="rule_check[]">WARNING</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(isset($rule_state)){if(strstr($rule_state, "CRITICAL") || strstr($rule_state, "*")){echo "checked";}}?> value="CRITICAL" name="rule_check[]">CRITICAL</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(isset($rule_state)){if(strstr($rule_state, "UNKNOWN") || strstr($rule_state, "*")){echo "checked";}}?> value="UNKNOWN" name="rule_check[]">UNKNOWN</label>
				</div>
			<?php } ?>
				<a href="#" id="rule_all1">All / None</a>
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.notification") ?></label>
			<div class="col-md-9">
				<input class="form-control" type="text" name="rule_notification" value="<?php if($rule_notification!="-"){ echo $rule_notification; } ?>">
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.notifperiod") ?></label>
			<div class="col-md-9">
				<input class="form-control" type="text" id="rule_timeperiod" name="rule_timeperiod" value="<?php echo $rule_timeperiod; ?>" onFocus='$(this).autocomplete({source: <?php echo get_field("timeperiods");?>})'>
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.method") ?></label>
			<div class="col-md-9">
				<div class="form-group input-group">
					<input class="form-control" type="text" id="rule_method" name="rule_method" value="<?php if(isset($rule_method)){echo $rule_method;} ?>" onFocus='$(this).autocomplete({source: <?php echo get_field("methods",$rule_type);?>})'>
					<span class="input-group-btn">
						<input class="btn btn-success" id="rule_method_button" type="button" value="<?php echo getLabel("action.add");?>">
						<input class="btn btn-danger" id="rule_method_button_del" type="button" value="<?php echo getLabel("action.clear");?>">
					</span>
				</div>
				<select class="form-control" id="methods" name="methods[]" size=4 multiple="multiple">
				<?php 
				if(isset($rule_method_names)){
					$division=explode(",", $rule_method_names);
					foreach($division as $selected){
						echo "<option selected='selected' value='".$selected."'>".$selected."</option> ";
					}
				} 
				?>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.track") ?></label>
			<div class="col-md-9">
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(isset($rule_track) && $rule_track==1){ echo "checked"; } ?> value=1 name="rule_track"></label>
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<?php
				if (isset($rule_id) && $rule_id != null) {
					echo "<input class='btn btn-primary' type='submit' name='update' value=".getLabel('action.update').">";
				}
				else {
					echo "<input class='btn btn-primary' type='submit' name='add' value=".getLabel('action.add').">";
				}
			?>
			<input class="btn btn-default" type="button" name="back" value="<?php echo getLabel("action.cancel"); ?>" onclick="location.href='index.php'">
		</div>
	</form>
</div>

<?php include("../../footer.php"); ?>
