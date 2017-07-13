<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
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
include("./functions.php");

$thruk_bps_deps = get_bps();
extract($_POST);

?>

<div id="page-wrapper">
	
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.tool_downtimes.title_nagiosdowntime"); ?></h1>
		</div>
	</div>
	
	<div class="row">

		<?php

		if(isset($submit)){
			
			echo '<div class="col-lg-12">';

			if($bp=="" || $start=="" || $end=="" || $user=="" || $comment=="") {
				message(7," : All fields are necessary","warning");
			}
			else {
				$date_start = new DateTime($start);
				$start = $date_start->getTimestamp();	
				$date_end = new DateTime($end);
				$end = 	$date_end->getTimestamp();
				
				if($end <= $start) {
					message(7," : End date <= Start date","warning");
				}
				else {
					set_downtime($bp,$start,$end,$user,$comment);
					message(6," : $bp","ok");
					if(isset($deps)) {
						foreach($thruk_bps_deps[$bp] as $thruk_bps_dep) {
							set_downtime($thruk_bps_dep,$start,$end,$user,$comment);
						}
						message(6," : $bp links","ok");
					} 
				}
			}

			echo '</div>';
			
		}
		?>
	
		<form id="tool-form" method="post">
			
				<div class="col-md-4">

					<div class="form-group">
						<label><?php echo getLabel("label.admin_bp.business_process"); ?></label>
						<select class="form-control" name="bp">
							<option value=""></option>
							<?php
							foreach($thruk_bps_deps as $thruk_bps_dep => $value) {
								echo "<option>$thruk_bps_dep</option>";
							}
							?>
						</select>
					</div>
					
					<div class="form-group">
						<div class="checkbox">
							<label><input type="checkbox" name="deps" checked><?php echo getLabel("label.admin_bp.linked_to_bp"); ?></label>
						</div>
					</div>
		
					<div class="form-group">
						<label><?php echo getLabel("label.begin"); ?></label>
						<div class="input-group">
							<input type="text" class="form-control datepicker_start" name="start">
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
					
					<div class="form-group">
						<label><?php echo getLabel("label.end"); ?></label>
						<div class="input-group">
							<input type="text" class="form-control datepicker_end" name="end">
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
					
					<div class="form-group">
						<label><?php echo getLabel("label.user"); ?></label>
						<input type="text" class="form-control" name="user" value="<?php echo $_SERVER["REMOTE_USER"]; ?>" readonly>
					</div>

					<div class="form-group">
						<label><?php echo getLabel("label.comments"); ?></label>
						<input type="text" class="form-control" name="comment">
					</div>

					<div class="form-group">
						<button class="btn btn-primary" type="submit" name="submit"><?php echo getLabel("action.submit"); ?></button>
					</div>					
					
				</div>
				
		</form>	
		
	</div>
	
</div>

<?php include("../../footer.php"); ?>
