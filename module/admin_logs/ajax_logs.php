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
include("../../include/function.php");

// create WHERE clause according to parameters
extract($_POST);

$where_clause = "";
$where_prepare=array();
if($user != ""){ $where_clause .= " AND user LIKE ?" ; $where_prepare[]="%$user%"; }
if($module != ""){ $where_clause .= " AND module LIKE ?" ; $where_prepare[]="%$module%"; }
if($description != ""){ $where_clause .= " AND description LIKE ?" ; $where_prepare[]="%$description%"; }
if($source != ""){ $where_clause .= " AND source LIKE ?" ; $where_prepare[]="%$source%"; }

// period clause
if($date != ""){
	$times = explode(" - ", $date);
	$start = strtotime($times[0]);
	$end = strtotime($times[1]) + 86400;
	$where_clause .= " AND date >= ? AND date < ?";
	$where_prepare[]=(string)$start;
	$where_prepare[]=(string)$end;
}

$sql = "SELECT * FROM logs WHERE id!=0".$where_clause." ORDER BY date DESC";
$results = sql($database_eonweb,$sql,$where_prepare);

?>

<div class="dataTable_wrapper">
	<table class="table table-striped datatable-eonweb table-condensed">
		<thead>
			<tr>
				<th>Date</th>
				<th class="col-md-2"><?php echo getLabel("label.user"); ?></th>
				<th class="col-md-2">Module</th>
				<th class="col-md-4">Description</th>
				<th>Source</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $results as $log ) { ?>
				<tr>
					<td><?php echo date($dateformat, $log["date"]); ?></td>
					<td><?php echo $log["user"]; ?></td>
					<td><?php echo $log["module"]; ?></td>
					<td><?php echo $log["description"]; ?></td>
					<td><?php echo $log["source"]; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>

<?php include("admin_logs.php"); ?>