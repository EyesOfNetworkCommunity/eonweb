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

function get_field($field1, $base, $field2=false) {
	global $database_lilac;
	global $database_notifier;
	$hosts=array();
	
	if($field2){
		$request="SELECT name FROM nagios_$field1 UNION SELECT name FROM nagios_$field2";
		$result=sql($database_lilac,$request);
	}
	foreach($result as $line){
		$hosts[]=$line[0];
	}
	$hosts= array_unique($hosts);
	echo json_encode($hosts);
}
?>
<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_notifier.contact.add.modify"); ?></h1>
		</div>
	</div>
	
	<?php 
		$rule_debug=retrieve_form_data("rule_debug",null);
		$contact=retrieve_form_data("contact",null);
		$contact_old=retrieve_form_data("contact_old",null);
		
		if($contact){
			$sql_test = "SELECT count(name) FROM contacts where name = ?";
			$timeperiod_exist = sql($database_notifier,$sql_test, array($contact));
			$ajout= $timeperiod_exist[0]; 
		}
		
		// ADD or UPDATE Contacts
		if(isset($_POST["add"]) || isset($_POST["update"])) {
			if(!$contact || $contact==""){
					message(7," : You need a contact to disable",'warning');
			}elseif((isset($_POST["add"]) && $ajout!=0)||(isset($_POST["update"]) && $ajout!=0 && $contact != $contact_old)){
					message(7," : This contact is already disabled",'warning');
			}elseif(isset($_POST["add"])){	
				sql($database_notifier,"INSERT INTO contacts VALUES(?,?)", array($contact, $rule_debug));
				message(6," : Contact have been added",'ok');
				$contact_old=$contact;
			}elseif($_POST["update"]){
				message(6," : Contact have been updated",'ok');	
				$sql_update = "UPDATE contacts SET name=?, debug=? WHERE name=?";
				sql($database_notifier,$sql_update, array($contact, $rule_debug, $contact));
				$contact_old=$contact;
			}
		}
		// DISPLAY
		elseif(isset($_GET["name"])) {
			$contact_sql=sql($database_notifier,"SELECT * from contacts where name=?", array($_GET["name"]));
			if($contact_sql[0]["name"]) {
				$contact = $contact_sql[0]["name"];
				$contact_old = $contact_sql[0]["name"];
				$rule_debug = $contact_sql[0]["debug"];
			} else {
				message(7," : Contact does not exist",'warning');
			}
		}

		$sql=sql($database_notifier,"select name from contacts order by name");
	?>
	
	<form action="./contact.php" method="POST" name="form">
	
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.contact.name") ?></label>
			<div class="col-md-9">
				<input class="form-control" id="contact" type="text" name="contact" value="<?php echo $contact; ?>" onFocus='$(this).autocomplete({source: <?php get_field("contact",false,"contact_group"); ?>})'>
				<input type="hidden" name="contact_old" value="<?php echo $contact_old; ?>">

			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.rules.debug") ?></label>
			<div class="col-md-9">
				<select class="form-control" name="rule_debug">
				<?php
				for($i=0;$i<=3;$i++) {
					if($rule_debug==$i) {
						echo "<option selected>$i</option>";
					} else {
						echo "<option>$i</option>";
					}
				}
				?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<?php
				if (isset($contact) && $contact != null) {
					echo "<input class='btn btn-primary' type='submit' name='update' value=".getLabel('action.update').">";
				}
				else {
					echo "<input class='btn btn-primary' type='submit' name='add' value=".getLabel('action.add').">";
				}
			?>
			<input class="btn btn-default" type="button" name="back" value="<?php echo getLabel("action.cancel"); ?>" onclick="location.href='index.php?action=contacts'">
		</div>
	</form>
</div>

<?php include("../../footer.php"); ?>
