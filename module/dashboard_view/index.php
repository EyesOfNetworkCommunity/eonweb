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

?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.monitoring_view.title"); ?></h1>
		</div>
	</div>
	
	<?php if($_COOKIE["user_limitation"] == 0){ ?>
	<div class="row">
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a style="text-decoration:none;" class="frameLoader" href="<?php echo getFrameURL($path_nagios_cgi."/status.cgi?hostgroup=all&style=hostdetail"); ?>">
						<i class="fa fa-bar-chart-o fa-fw"></i>
						<?php echo getLabel("label.monitoring_view.equip_nagios"); ?>
					</a>
				</div>
				<div class="panel-body">
					<div id="container_hosts_state"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a style="text-decoration:none;" class="frameLoader" href="<?php echo getFrameURL($path_nagios_cgi."/status.cgi?host=all"); ?>">
						<i class="fa fa-bar-chart-o fa-fw"></i>
						<?php echo getLabel("label.monitoring_view.serv_nagios"); ?>
					</a>
				</div>
				<div class="panel-body">
					<div id="container_services_state"></div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="row">
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a style="text-decoration:none;" href="/module/monitoring_ged/index.php?q=active">
						<i class="fa fa-bar-chart-o fa-fw"></i>
						<?php echo getLabel("label.monitoring_view.act_event"); ?>
					</a>
				</div>
				<div class="panel-body">
					<div id="container_event_state_nbr"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a style="text-decoration:none;" href="/module/monitoring_ged/index.php?q=active">
						<i class="fa fa-bar-chart-o fa-fw"></i>
						<?php echo getLabel("label.monitoring_view.act_event"); ?>
					</a>
				</div>
				<div class="panel-body">
					<div id="container_event_state_nbr_by_time"></div>
				</div>
			</div>
		</div>
	</div>

</div>

<?php include("../../footer.php"); ?>
