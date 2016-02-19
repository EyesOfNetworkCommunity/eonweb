<?php
/*
#########################################
#
# Copyright (C) 2014 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION 4.2
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
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<?php include("../../include/include_module.php"); ?>

		<script type="text/javascript" src="../../js/jquery.min.js"></script>
		<script src="../../js/highcharts.js"></script>
		<script src="../../js/highcharts-exporting.js"></script>
		<script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>

		<script src="report_script.js"></script>
	</head>
	
	<body id="main">

		<h1><?php echo $xmlmodules->getElementsByTagName("report_event")->item(0)->getAttribute("title")?></h1><br>

		<?php
			# --- Type of report (events, sla, ...)
			if(isset($_GET["type"]))
				$type="?type=".$_GET["type"];
			else
				$type="";

			# --- Languages
			$sla=$xmlmodules->getElementsByTagName("report_event")->item(0);

			# --- If Display
			if(isset($_POST["display"])) {
				# --- Search filters
				if(isset($_POST["value"])){
					if($_POST["value"]!=""){
						$myfilter["field"]=$_POST["field"];
						$myfilter["value"]=$_POST["value"];
						echo "<h2>".$myfilter["field"]." : ".$myfilter["value"]."</h2><br>";
					}
					else
						$myfilter=false;
				}
				else
				$myfilter=false;

				# --- Display reports
				if($type!="") {
					echo "<h1>".$sla->getAttribute("sla")."</h1>";
					echo "<script type=\"text/javascript\">getSlaGraph('pie', 'sla_pie', 'report_history_events_pie_sla.xml','','','history', '".$_POST["field"]."', '".$_POST["value"]."');</script>";
					echo "<script type=\"text/javascript\">getSlaGraph('bar', 'sla_bar', 'report_history_events_bar_sla.xml','','','history', '".$_POST["field"]."', '".$_POST["value"]."');</script>";
					echo "<div style=\"margin-bottom:50px; text-align: center;\">";
					echo "<center id=\"sla_pie\" style=\"vertical-align:top;min-width: 350px; height: 300px; max-width: 450px; display: inline-block;margin-right:100px;\"></center>";
					echo "<center id=\"sla_bar\" style=\"min-width: 350px; height: 300px; max-width: 450px; display: inline-block;\"></center>";
					echo "</div>";
					echo "<br><br>";

					if(isset($_POST["by_day"])) {
						echo "<h1>".$sla->getElementsByTagName("by_day")->item(0)->nodeValue."</h1>";
						echo "<i>".date("d/m/Y H:i:s",strtotime("- 1 day"))." - ".date("d/m/Y H:i:s",time())."</i>";
						echo "<script type=\"text/javascript\">getSlaGraph('pie', 'sla_pie_day', 'report_history_events_pie_sla.xml',".strtotime("- 1 day").",".time().",'history', '".$_POST["field"]."', '".$_POST["value"]."');</script>";
						echo "<div style=\"margin-bottom:50px;text-align:center;\">";
						echo "<center id=\"sla_pie_day\" style=\"vertical-align:top;min-width: 350px; height: 300px; max-width: 450px; display: inline-block;margin-right:100px;\"></center>";
						echo "</div>";
						echo "<br><br>";
					}

					if(isset($_POST["by_week"])) {
						echo "<h1>".$sla->getElementsByTagName("by_week")->item(0)->nodeValue."</h1>";
						echo "<i>".date("d/m/Y H:i:s",strtotime("- 1 week"))." - ".date("d/m/Y H:i:s",time())."</i>";
						echo "<script type=\"text/javascript\">getSlaGraph('pie', 'sla_pie_week', 'report_history_events_pie_sla.xml',".strtotime("- 1 week").",".time().",'history', '".$_POST["field"]."', '".$_POST["value"]."');</script>";
						echo "<div style=\"margin-bottom:50px;text-align:center;\">";
						echo "<center id=\"sla_pie_week\" style=\"vertical-align:top;min-width: 350px; height: 300px; max-width: 450px; display: inline-block;margin-right:100px;\"></center>";
						echo "</div>";
						echo "<br><br>";
					}
					if(isset($_POST["by_month"])) {
						echo "<h1>".$sla->getElementsByTagName("by_month")->item(0)->nodeValue."</h1>";
						echo "<i>".date("d/m/Y H:i:s",strtotime("- 1 month"))." - ".date("d/m/Y H:i:s",time())."</i>";
						echo "<script type=\"text/javascript\">getSlaGraph('pie', 'sla_pie_month', 'report_history_events_pie_sla.xml',".strtotime("- 1 month").",".time().",'history', '".$_POST["field"]."', '".$_POST["value"]."');</script>";
						echo "<div style=\"margin-bottom:50px;text-align:center;\">";
						echo "<center id=\"sla_pie_month\" style=\"vertical-align:top;min-width: 350px; height: 300px; max-width: 450px; display: inline-block;margin-right:100px;\"></center>";
						echo "</div>";
						echo "<br><br>";
					}

					if(isset($_POST["by_year"])) {
						echo "<h1>".$sla->getElementsByTagName("by_year")->item(0)->nodeValue."</h1>";
						echo "<i>".date("d/m/Y H:i:s",strtotime("- 1 year"))." - ".date("d/m/Y H:i:s",time())."</i>";
						echo "<script type=\"text/javascript\">getSlaGraph('pie', 'sla_pie_year', 'report_history_events_pie_sla.xml',".strtotime("- 1 year").",".time().",'history', '".$_POST["field"]."', '".$_POST["value"]."');</script>";
						echo "<div style=\"margin-bottom:50px;text-align:center;\">";
						echo "<center id=\"sla_pie_year\" style=\"vertical-align:top;min-width: 350px; height: 300px; max-width: 450px; display: inline-block;margin-right:100px;\"></center>";
						echo "</div>";
						echo "<br><br>";
					}
				}
				else {
					echo "<h1>".$sla->getAttribute("active")."</h1>";
					echo "<script type=\"text/javascript\">getGraph('pie', 'active_now_pie', 'report_active_events_pie_by_state.xml','','','active', '".$_POST["field"]."', '".$_POST["value"]."', 'no');</script>";
					echo "<script type=\"text/javascript\">getGraph('bar', 'active_now_bar', 'report_active_events_by_group.xml','','','active', '".$_POST["field"]."', '".$_POST["value"]."', 'no');</script>";
					echo "<div style=\"margin-bottom:50px; text-align: center;\">";
					echo "<center id=\"active_now_pie\" style=\"vertical-align:top;min-width: 350px; height: 300px; max-width: 450px; display: inline-block;margin-right:100px;\"></center>";
					echo "<center id=\"active_now_bar\" style=\"min-width: 350px; height: 300px; max-width: 450px; display: inline-block;\"></center>";
					echo "</div>";
					echo "<br><br>";

					echo "<h1>".$sla->getAttribute("history")."</h1>";
					echo "<script type=\"text/javascript\">getGraph('pie', 'history_pie', 'report_history_events_pie_by_state.xml','','','history', '".$_POST["field"]."', '".$_POST["value"]."', 'no');</script>";
					echo "<script type=\"text/javascript\">getGraph('bar', 'history_bar', 'report_history_events_by_group.xml','','','history', '".$_POST["field"]."', '".$_POST["value"]."', 'yes');</script>";
					echo "<div style=\"margin-bottom:50px; text-align: center;\">";
					echo "<center id=\"history_pie\" style=\"vertical-align:top;min-width: 350px; height: 300px; max-width: 450px; display: inline-block;margin-right:100px;\"></center>";
					echo "<center id=\"history_bar\" style=\"min-width: 350px; height: 300px; max-width: 450px; display: inline-block;\"></center>";
					echo "</div>";
					echo "<br><br>";

					if(isset($_POST["by_day"])) {
						echo "<h1>".$sla->getElementsByTagName("by_day")->item(0)->nodeValue."</h1>";
						echo "<i>".date("d/m/Y H:i:s",strtotime("- 1 day"))." - ".date("d/m/Y H:i:s",time())."</i>";
						echo "<script type=\"text/javascript\">getGraph('pie', 'day_pie', 'report_history_events_pie_by_state.xml',".strtotime("- 1 day").",".time().",'history', '".$_POST["field"]."', '".$_POST["value"]."', 'no');</script>";
						echo "<div style=\"margin-bottom:50px; text-align: center;\">";
						echo "<center id=\"day_pie\"></center>";
						echo "</div>";
						echo "<br><br>";
					}
					if(isset($_POST["by_week"])) {
						echo "<h1>".$sla->getElementsByTagName("by_week")->item(0)->nodeValue."</h1>";
						echo "<i>".date("d/m/Y H:i:s",strtotime("- 1 week"))." - ".date("d/m/Y H:i:s",time())."</i>";
						echo "<script type=\"text/javascript\">getGraph('pie', 'week_pie', 'report_history_events_pie_by_state.xml',".strtotime("- 1 week").",".time().",'history', '".$_POST["field"]."', '".$_POST["value"]."', 'no');</script>";
						echo "<div style=\"margin-bottom:50px; text-align: center;\">";
						echo "<center id=\"week_pie\"></center>";
						echo "</div>";
						echo "<br><br>";
					}

					if(isset($_POST["by_month"])) {
						echo "<h1>".$sla->getElementsByTagName("by_month")->item(0)->nodeValue."</h1>";
						echo "<i>".date("d/m/Y H:i:s",strtotime("- 1 month"))." - ".date("d/m/Y H:i:s",time())."</i>";
						echo "<script type=\"text/javascript\">getGraph('pie', 'month_pie', 'report_history_events_pie_by_state.xml',".strtotime("- 1 month").",".time().",'history', '".$_POST["field"]."', '".$_POST["value"]."', 'no');</script>";
						echo "<div style=\"margin-bottom:50px; text-align: center;\">";
						echo "<center id=\"month_pie\"></center>";
						echo "</div>";
						echo "<br><br>";
					}

					if(isset($_POST["by_year"])) {
						echo "<h1>".$sla->getElementsByTagName("by_year")->item(0)->nodeValue."</h1>";
						echo "<i>".date("d/m/Y H:i:s",strtotime("- 1 year"))." - ".date("d/m/Y H:i:s",time())."</i>";
						echo "<script type=\"text/javascript\">getGraph('pie', 'year_pie', 'report_history_events_pie_by_state.xml',".strtotime("- 1 year").",".time().",'history', '".$_POST["field"]."', '".$_POST["value"]."', 'no');</script>";
						echo "<div style=\"margin-bottom:50px; text-align: center;\">";
						echo "<center id=\"year_pie\"></center>";
						echo "</div>";
						echo "<br><br>";
					}
				}
			}
			else {
		?>
			<script type="text/javascript" src="../../js/jquery.js"></script>
			<script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>
			<div id="search">
			<form action='index.php<?php echo $type?>' method='post'>
			<h2>Define your report :</h2>

			<br>

			<select id="field" name="field" onchange="$('#value').focus();">
			<?php
			for($i=0;$i<count($array_ged_filters);$i++)
			echo "<option>$array_ged_filters[$i]</option>";
			?>
			</select>

			<input id="value" name="value" class="value" type="text" autocomplete="off" onFocus='$(this).autocomplete(<?php echo get_host_list_from_nagios();?>)' />

			| <?php echo $sla->getElementsByTagName("by_day")->item(0)->nodeValue?> <input type="checkbox" name="by_day" class="checkbox">
			| <?php echo $sla->getElementsByTagName("by_week")->item(0)->nodeValue?> <input type="checkbox" name="by_week" class="checkbox"> 
			| <?php echo $sla->getElementsByTagName("by_month")->item(0)->nodeValue?> <input type="checkbox" name="by_month" class="checkbox">
			| <?php echo $sla->getElementsByTagName("by_year")->item(0)->nodeValue?> <input type="checkbox" name="by_year" class="checkbox"> |

			<input class="button" type="submit" value="Display" name="display"></input>
			<form>
			</div>
		<?php } ?>
	</body>
</html>
