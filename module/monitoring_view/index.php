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
		<script src="../../js/jquery.min.js"></script>
		<script src="../../js/highcharts.js"></script>
		<script src="../../js/highcharts-exporting.js"></script>
		
		<script src="monitoring_view_script.js"></script>
		<script type="text/javascript">
			ajaxCharts();
			setInterval(ajaxCharts, <?php echo $refresh_time * 1000; ?>);
		</script>
	</head>

<body id="main">
<?php
// # Get language module
$dashboard = $xmlmodules->getElementsByTagName("dashboard");
?>
<h1><?php echo $dashboard->item(0)->getAttribute("title")?></h1>
<div id="ged_messages" align="right">
        <i>screen refresh every <?php echo $refresh_time?> seconds</i>
</div>
<br>

	<div id="container" style="margin: 0 auto; width: 870px; text-align: center;">
		<div id="container_hosts_state" style="display: inline-block; margin: 5px;"></div>
		<div id="container_services_state" style="display: inline-block; margin: 5px;"></div>
		<div id="container_event_state_nbr" style="display: inline-block; margin: 5px;"></div>
		<div id="container_event_state_nbr_by_time" style="display: inline-block; margin: 5px;"></div>
	</div>
</body>
</html>
