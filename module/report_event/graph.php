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

include("../../include/config.php");
include("../../include/arrays.php");
include("../../include/function.php");

# --- If Display
if(isset($_POST["display"])) {
	// define $type
	$type = $_POST["type"];

	// search fields results
	$field = $_POST["field"];
	$value = $_POST["value"];
	
	if( isset($value) && $value != "" ){
		echo "<p class='alert alert-info'><i class='fa fa-info-circle'> </i> ".$field." : ".$value."</p>";
	}
	
	# --- Display reports
	if($type!="") {
		$pie_infos = slaPieChart($field, $value, "");
		$bar_infos = slaBarChart($field, $value);
		echo '
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.sla").'
						</div>
						<div class="panel-body">
							<div id="sla_pie"></div>
						</div>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.sla").'
						</div>
						<div class="panel-body">
							<div id="sla_bar"></div>
						</div>
					</div>
				</div>
			</div>';
		echo "<script>drawSlaPieChart('sla_pie', $pie_infos)</script>";
		echo "<script>drawSlaBarChart('sla_bar', $bar_infos)</script>";
		
		echo '<div class="row">';
		// SLA pie chart of the day
		if(isset( $_POST["by_day"]) && $_POST["by_day"] == "on" ) {
			//echo "<h1>".getLabel("label.report_event.day")."</h1>";
			//echo "<p class='fa fa-info-circle text-info'> ".date("d/m/Y H:i:s",strtotime("- 1 day"))." - ".date("d/m/Y H:i:s",time())."</p>";
			$pie_infos = slaPieChart($field, $value, "day");
			echo '
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.day").'
						</div>
						<div class="panel-body">
							<p class="fa fa-info-circle text-info"> '.date("d/m/Y H:i:s",strtotime("- 1 day")).' - '.date("d/m/Y H:i:s",time()).'</p>
							<div id="sla_pie_day"></div>
						</div>
					</div>
				</div>';
			echo "<script>drawSlaPieChart('sla_pie_day', $pie_infos)</script>";
		}
		
		// SLA pie chart of the week
		if( isset($_POST["by_week"]) && $_POST["by_week"] == "on" ) {
			//echo "<p class='fa fa-info-circle text-info'> ".date("d/m/Y H:i:s",strtotime("- 1 week"))." - ".date("d/m/Y H:i:s",time())."</p>";
			$pie_infos = slaPieChart($field, $value, "week");
			echo '
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.week").'
						</div>
						<div class="panel-body">
							<p class="fa fa-info-circle text-info"> '.date("d/m/Y H:i:s",strtotime("- 1 week")).' - '.date("d/m/Y H:i:s",time()).'</p>
							<div id="sla_pie_week"></div>
						</div>
					</div>
				</div>';
			echo "<script>drawSlaPieChart('sla_pie_week', $pie_infos)</script>";
		}
		
		// SLA pie chart of the month
		if( isset($_POST["by_month"]) && $_POST["by_month"] == "on" ) {
			$pie_infos = slaPieChart($field, $value, "month");
			echo '
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.month").'
						</div>
						<div class="panel-body">
							<p class="fa fa-info-circle text-info"> '.date("d/m/Y H:i:s",strtotime("- 1 month")).' - '.date("d/m/Y H:i:s",time()).'</p>
							<div id="sla_pie_month"></div>
						</div>
					</div>
				</div>';
			echo "<script>drawSlaPieChart('sla_pie_month', $pie_infos)</script>";
		}
		
		// SLA pie chart of the year
		if( isset($_POST["by_year"]) && $_POST["by_year"] == "on" ) {
			$pie_infos = slaPieChart($field, $value, "year");
			echo '
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.year").'
						</div>
						<div class="panel-body">
							<p class="fa fa-info-circle text-info"> '.date("d/m/Y H:i:s",strtotime("- 1 year")).' - '.date("d/m/Y H:i:s",time()).'</p>
							<div id="sla_pie_year"></div>
						</div>
					</div>
				</div>';
			echo "<script>drawSlaPieChart('sla_pie_year', $pie_infos)</script>";
		}
		echo '</div>';
	}
	else {
		// normal active graphs (pie and bar)
		$pie_infos = pieChart("active", $field, $value, "");
		$bar_infos = barChart("active", $field, $value);
		echo '
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.act_event").'
						</div>
						<div class="panel-body">
							<div id="active_pie"></div>
						</div>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.act_event").'
						</div>
						<div class="panel-body">
							<div id="active_bar"></div>
						</div>
					</div>
				</div>
			</div>';
		echo "<script>drawPieChart('active_pie', $pie_infos)</script>";
		echo "<script>drawBarChart('active_bar', $bar_infos, 'active')</script>";
		
		// normal history graphs (pie and bar)
		$pie_infos = pieChart("history", $field, $value, "");
		$bar_infos = barChart("history", $field, $value);
		echo '
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.his_event").'
						</div>
						<div class="panel-body">
							<div id="history_pie"></div>
						</div>
					</div>
				</div>
				
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.his_event").'
						</div>
						<div class="panel-body">
							<div id="history_bar"></div>
						</div>
					</div>
				</div>
			</div>';
		echo "<script>drawPieChart('history_pie', $pie_infos)</script>";
		echo "<script>drawBarChart('history_bar', $bar_infos, 'history')</script>";
		
		echo '<div class="row">';
		// normal "by_day" graph
		if( isset($_POST["by_day"]) && $_POST["by_day"] == "on" ) {
			$pie_infos = pieChart("history", $field, $value, "day");
			echo '
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.day").'
						</div>
						<div class="panel-body">
							<p class="fa fa-info-circle text-info"> '.date("d/m/Y H:i:s",strtotime("- 1 day")).' - '.date("d/m/Y H:i:s",time()).'</p>
							<div id="history_pie_day"></div>
						</div>
					</div>
				</div>';
			echo "<script>drawPieChart('history_pie_day', $pie_infos)</script>";
		}
		
		// normal "by_week" graph
		if( isset($_POST["by_week"]) && $_POST["by_week"] == "on" ) {
			$pie_infos = pieChart("history", $field, $value, "week");
			echo '
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.week").'
						</div>
						<div class="panel-body">
							<p class="fa fa-info-circle text-info"> '.date("d/m/Y H:i:s",strtotime("- 1 week")).' - '.date("d/m/Y H:i:s",time()).'</p>
							<div id="history_pie_week"></div>
						</div>
					</div>
				</div>';
			echo "<script>drawPieChart('history_pie_week', $pie_infos)</script>";
		}
		
		// normal "by_month" graph
		if( isset($_POST["by_month"]) && $_POST["by_month"] == "on" ) {
			$pie_infos = pieChart("history", $field, $value, "month");
			echo '
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.month").'
						</div>
						<div class="panel-body">
							<p class="fa fa-info-circle text-info"> '.date("d/m/Y H:i:s",strtotime("- 1 month")).' - '.date("d/m/Y H:i:s",time()).'</p>
							<div id="history_pie_month"></div>
						</div>
					</div>
				</div>';
			echo "<script>drawPieChart('history_pie_month', $pie_infos)</script>";
		}
		
		// normal "by_year" graph
		if( isset($_POST["by_year"]) && $_POST["by_year"] == "on" ) {
			$pie_infos = pieChart("history", $field, $value, "year");
			echo '
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i> '.getLabel("label.report_event.year").'
						</div>
						<div class="panel-body">
							<p class="fa fa-info-circle text-info"> '.date("d/m/Y H:i:s",strtotime("- 1 year")).' - '.date("d/m/Y H:i:s",time()).'</p>
							<div id="history_pie_year"></div>
						</div>
					</div>
				</div>';
			echo "<script>drawPieChart('history_pie_year', $pie_infos)</script>";
		}
		echo '</div>';
	}
}

?>
