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
			<h1 class="page-header"><?php echo getLabel("label.report_performance.title"); ?></h1>
		</div>
	</div>

	<?php
	$timespan["end_now"] = time();
	$end_year = date("Y",$timespan["end_now"]);
	$end_month = date("m",$timespan["end_now"]);
	$end_day = date("d",$timespan["end_now"]);
	$end_hour = date("H",$timespan["end_now"]);
	$end_min = date("i",$timespan["end_now"]);
	$end_sec = 00;
	?>


	<form id="report-performance-form" action='display.php' method='GET'>
		<div id="search" class="row">
			<div class="col-md-4 form-group">
				<label><?php echo getLabel("label.report_performance.select_period"); ?></label>
				<select id="date" class="form-control" name='date'>
					<option value='today'><?php echo getLabel("label.report_performance.today"); ?></option>
					<option value='lastday'><?php echo getLabel("label.report_performance.last_day"); ?></option>
					<option value='lastweek'><?php echo getLabel("label.report_performance.last_week"); ?></option>
					<option value='last2week'><?php echo getLabel("label.report_performance.last_2_week"); ?></option>
					<option value='lastmonth'><?php echo getLabel("label.report_performance.last_month"); ?></option>
					<option value='last2month'><?php echo getLabel("label.report_performance.last_2_month"); ?></option>
					<option value='lastyear'><?php echo getLabel("label.report_performance.last_year"); ?></option>
				</select>
			</div>
			<div class="col-md-4 form-group">
				<label><?php echo getLabel("label.report_performance.search_title"); ?></label>
				<div class="input-group">
					<input id="title" type="text" name="title" class="form-control" autocomplete="off" onFocus='$(this).autocomplete({source: <?php echo get_title_list_from_cacti();?>})'>
					<span class="input-group-btn">
						<button class="btn btn-primary" type="submit" value="Display"><?php echo getLabel("action.display"); ?></button>
					</span>
				</div>
			</div>
		</div>
	</form>
	
	<div id="response"></div>
</div>

<?php include("../../footer.php"); ?>