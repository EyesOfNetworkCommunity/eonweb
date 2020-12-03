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

?>
<div id="page-wrapper">
	
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_notifier.period.add"); ?></h1>
		</div>
	</div>
	
	<?php
	// Days
	$rule_day="-";
	if(!empty($_POST['rule_day'])){
		$i=0;
		foreach($_POST['rule_day'] as $selected){
			if($i==0){
				$rule_day=$selected;
				$i++;
			}
			else{
				$rule_day.=",".$selected;
				$i++;
			}
		}
		if($i==7){
			$rule_day="*";
		}
	}
	
	// Time periods
	$timeperiod="-";
	if(!empty($_POST['timeperiods'])){
		$i=0;
		foreach($_POST['timeperiods'] as $selected){
			if($i==0){
				$timeperiod=$selected;
				$i++;
			}
			else{
				$timeperiod.=",".$selected;
			}
		}
	} 
	
	// Get post data
	$timeperiod_id=retrieve_form_data("id",null);
	$timeperiod_name=retrieve_form_data("timeperiod_name",null); 
	$timeperiod_name_old=retrieve_form_data("timeperiod_name_old",null); 
	$timeperiod_daysofweek=$rule_day;
	$timeperiod_timeperiod=$timeperiod;
	
	// ADD or UPDATE
	if(isset($_POST["add"]) || isset($_POST["update"])) {
	
		// Check if timeperiod exists
		if($timeperiod_name){
			$sql_test = "SELECT count(name) FROM timeperiods where name = ?";
			$timeperiod_exist = sql($database_notifier, $sql_test, array($timeperiod_name));
			$ajout=$timeperiod_exist[0][0]; 
		}
	
		//Tests
		if(!$timeperiod_name || $timeperiod_name==""){
			message(6," : Your notifperiod need a name",'warning');
		}elseif((isset($_POST["add"]) && $ajout!=0)||(isset($_POST["update"]) && $ajout!=0 && $timeperiod_name != $timeperiod_name_old)){
			message(6," : Your notifperiod name already exist",'warning');
		}elseif(isset($_POST["add"])){	
			message(6," : Notifperiod have been added",'ok');
			$sql_add = "INSERT INTO timeperiods VALUES('', ?, ? , ?)";
			$timeperiod_id = sql($database_notifier, $sql_add, array($timeperiod_name, $timeperiod_daysofweek, $timeperiod_timeperiod));
			$timeperiod_name_old=$timeperiod_name;
		}elseif($_POST["update"]){
			message(6," : Notifperiod have been updated",'ok');	
			$sql_update = "UPDATE timeperiods SET name=?, daysofweek=?, timeperiod=? WHERE id=?";
			sql($database_notifier, $sql_update, array($timeperiod_name, $timeperiod_daysofweek, $timeperiod_timeperiod, $timeperiod_id));
			$timeperiod_name_old=$timeperiod_name;
		}
	}
	// DISPLAY
	elseif(isset($_GET["id"])) {
		if(is_numeric($_GET["id"])) {
			$timeperiod_sql=sql($database_notifier,"SELECT * from timeperiods where id=?", array($_GET["id"]));
			
			if($timeperiod_sql[0]["id"]) {
				$timeperiod_id = $timeperiod_sql[0]["id"];
				$timeperiod_name = $timeperiod_sql[0]["name"];
				$timeperiod_name_old = $timeperiod_sql[0]["name"];
				$timeperiod_daysofweek = $timeperiod_sql[0]["daysofweek"];
				$timeperiod_timeperiod = $timeperiod_sql[0]["timeperiod"];
			} else {
				message(7," : Notifperiod does not exist",'warning');
			}
		} else {
			$timeperiod_id=null;
		}
	}
	?>
		
	<form action="./timeperiods.php" method="POST" name="form">
		<input type="hidden" name="id" value="<?php echo $timeperiod_id; ?>">
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.period.name") ?></label>
			<div class="col-md-9">
				<input class="form-control" type="text" name="timeperiod_name" value="<?php echo $timeperiod_name; ?>" autofocus>
				<input type="hidden" name="timeperiod_name_old" value="<?php echo $timeperiod_name_old; ?>">
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.period.days") ?></label>
			<div class="col-md-9">
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(strstr($timeperiod_daysofweek, "mon")!=false || $timeperiod_daysofweek=="*"){echo "checked";} ?> value="mon" name="rule_day[]">Lundi</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(strstr($timeperiod_daysofweek, "tue")!=false || $timeperiod_daysofweek=="*"){echo "checked";} ?> value="tue" name="rule_day[]">Mardi</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(strstr($timeperiod_daysofweek, "wed")!=false || $timeperiod_daysofweek=="*"){echo "checked";} ?> value="wed" name="rule_day[]">Mercredi</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(strstr($timeperiod_daysofweek, "thu")!=false || $timeperiod_daysofweek=="*"){echo "checked";} ?> value="thu" name="rule_day[]">Jeudi</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(strstr($timeperiod_daysofweek, "fri")!=false || $timeperiod_daysofweek=="*"){echo "checked";} ?> value="fri" name="rule_day[]">Vendredi</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(strstr($timeperiod_daysofweek, "sat")!=false || $timeperiod_daysofweek=="*"){echo "checked";} ?> value="sat" name="rule_day[]">Samedi</label>
				</div>
				<div class="checkbox">
					<label><input class="checkbox" type="checkbox" <?php if(strstr($timeperiod_daysofweek, "sun")!=false || $timeperiod_daysofweek=="*"){echo "checked";} ?> value="sun" name="rule_day[]">Dimanche</label>
				</div>
				<a href="#" id="rule_all">All / None</a><br>
			</div>
		</div>
		
		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.period.hours") ?></label>
			<div class="col-md-9">
				<div class="form-group form-inline">
					<select id="selectHeureDeb1" name="selectHeureDeb">
						<?php for($i=0;$i<=23;$i++){
							if($i<10){
								$i="0".$i;
							} ?>
							<option value="<?php echo $i;?>"><?php echo $i;?></option> 
						<?php } ?>
					</select>
					:
					<select id="selectMinuteDeb1" name="selectMinuteDeb">
						<?php for($i=0;$i<=59;$i++){
							if($i<10){
								$i="0".$i;
							} ?>
							<option value="<?php echo $i;?>"><?php echo $i;?></option> 
						<?php } ?>
					</select>
					- 
					<select id="selectHeureFin1" name="selectHeureFin">
						<?php for($i=0;$i<=23;$i++){
							if($i<10){
								$i="0".$i;
							} ?>
							<option value="<?php echo $i;?>"><?php echo $i;?></option> 
						<?php } ?>
					</select>
					:
					<select id="selectMinuteFin1" name="selectMinuteFin">
						<?php for($i=0;$i<=59;$i++){
							if($i<10){
								$i="0".$i;
							} ?>
							<option value="<?php echo $i;?>"><?php echo $i;?></option> 
						<?php } ?>
					</select>
				</div>
			
				<div class="form-group">
					<input class="btn btn-success btn-sm" id="rule_timeperiod_button" type="button" value="Add">
					<input class="btn btn-danger btn-sm" id="rule_timeperiod_button_del" type="button" value="Delete">
				</div>

				<select class="form-control" id="timeperiods" name="timeperiods[]" size=4 multiple="multiple">
				<?php 
				if(isset($timeperiod_timeperiod)){
					if($timeperiod_timeperiod!="-") {
						$division=explode(",", $timeperiod_timeperiod);
						foreach($division as $selected){
							echo "<option selected='selected' value='".$selected."'>".$selected."</option> ";
						}
					}
				} 
				?>
				</select>
				<a href="#" id="notifperiod_all">All / None</a>
			</div>
		</div>
		
		<div class="form-group">
			<?php
				if (isset($timeperiod_id) && $timeperiod_id!=null) {
					echo "<input class='btn btn-primary' type='submit' name='update' value=".getLabel('action.update').">";
				}
				else {
					echo "<input class='btn btn-primary' type='submit' name='add' value=".getLabel('action.add').">";
				}
			?>
			<input class="btn btn-default" type="button" name="back" value="<?php echo getLabel("action.cancel"); ?>" onclick="location.href='index.php?action=timeperiods'">
		</div>
	</form>

</div>
<?php include("../../footer.php"); ?>
