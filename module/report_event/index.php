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
			<h1 class="page-header"><?php echo getLabel("label.report_event.title"); ?></h1>
		</div>
	</div>
	
	<?php
		# --- Type of report (events, sla, ...)
		if(isset($_GET["type"]))
			$type="?type=".$_GET["type"];
		else
			$type="";
	?>
	
	<form id="report-form" method='POST'>
		<!-- input hidden with type value in order to send the graph type in AJAX -->
		<input id="type" type="hidden" value="<?php echo $type; ?>">
		
		<div class="row">
			<div class="col-md-3 form-group">
				<label>Time</label>
				<div class="checkbox">
					<label><input id="by_day" type="checkbox" name="by_day"><?php echo getLabel("label.report_event.day"); ?></label>
				</div>
				<div class="checkbox">
					<label><input id="by_week" type="checkbox" name="by_week"><?php echo getLabel("label.report_event.week"); ?></label>
				</div>
				<div class="checkbox">
					<label><input id="by_month" type="checkbox" name="by_month"><?php echo getLabel("label.report_event.month"); ?></label>
				</div>
				<div class="checkbox">
					<label><input id="by_year" type="checkbox" name="by_year"><?php echo getLabel("label.report_event.year"); ?></label>
				</div>
			</div>
			
			<div class="col-md-4">
				<div class="form-group">
					<label>Filtre</label>
					<select class="form-control" id="field" name="field">
						<?php
						foreach ($array_ged_filters as $key => $value) {
							echo "<option value='$value'>$value</option>";
						}
						?>
					</select>
				</div>
				<div class="form-group">
					<label>
						Rechercher
						<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo getLabel("message.advanced_search_info"); ?>" style="cursor: pointer;"></i>
					</label>
					<div class="input-group">
						<input class="form-control col-md-3" id="value" name="value" type="text" placeholder="*<?php echo getLabel('action.search'); ?>*"/>
						<span class="input-group-btn">
							<button class="btn btn-primary" type="submit"><?php echo getLabel("action.search"); ?></button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</form>
	
	<div id="result"></div>
	
</div>

<?php include("../../footer.php"); ?>
